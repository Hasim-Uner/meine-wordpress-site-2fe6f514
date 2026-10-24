#!/usr/bin/env python3
"""Anfrage-Monitor Runner: sendet taeglich eine echte Testanfrage ueber das Kontaktformular.

Ablauf:
  1. Chromium (Playwright) oeffnet <site>/kontakt/ und fuellt das sichtbare Formular.
  2. Eine Route ergaenzt beim Absenden `monitor_token` und `monitor_run_id` im Request.
     Das Geheimnis steht nie im DOM.
  3. IMAP (nur lesend, EXAMINE + BODY.PEEK) sucht die beiden markierten Mails.
  4. Das Ergebnis geht per Bearer an POST <site>/wp-json/nexus/v1/monitor/result.

Exit-Code: 0 = WordPress meldet run.ok true, 1 = Lauf fehlgeschlagen, 2 = Konfiguration fehlt.
Ausgabe: nur Pruefnamen, Wahrheitswerte und Dauern. Keine Mail-Inhalte, keine Zugangsdaten.

Nur Standardbibliothek plus `playwright` (wird erst beim Browserlauf importiert).
"""

from __future__ import annotations

import imaplib
import json
import os
import random
import re
import string
import sys
import time
import unicodedata
import urllib.error
import urllib.request
from dataclasses import dataclass, field
from datetime import datetime, timezone
from email import message_from_bytes
from email.header import decode_header, make_header
from urllib.parse import parse_qsl, urlencode

CHECKS = ("pageLoad", "formSubmit", "uiSuccess", "mailAdmin", "mailConfirmation")
BROWSER_CHECKS = CHECKS[:3]
CONTACT_PATH = "/kontakt/"
CONTACT_ENDPOINT = re.compile(r"/wp-json/nexus/v1/contact-request(\?.*)?$")
RESULT_PATH = "/wp-json/nexus/v1/monitor/result"
RUN_ID_PATTERN = re.compile(r"^[a-z0-9-]{8,40}$")  # gleiche Regel wie hu_monitor_is_valid_run_id()
POLL_INTERVAL_S = 20
UI_SUCCESS_TIMEOUT_MS = 15_000
STEP_TIMEOUT_MS = 15_000

# Selektoren des Kontaktformulars (page-kontakt.php, assets/js/contact.js).
FORM = "[data-contact-form]"
FOCUS_SELECT = FORM + " [data-contact-focus-select]"
MESSAGE_FIELD = FORM + " [data-contact-step='message'] textarea[name='message']"
NEXT_BUTTON = FORM + " [data-contact-next]"
NAME_FIELD = FORM + " input[name='name']"
EMAIL_FIELD = FORM + " input[name='email']"
CONSENT_FIELD = FORM + " input[name='consent']"
SUBMIT_BUTTON = FORM + " [data-contact-submit]"
FEEDBACK_SUCCESS = FORM + " [data-contact-feedback].is-success"


# ── Konfiguration ───────────────────────────────────────────────────────────


@dataclass
class Config:
    site_url: str
    token: str
    sender_email: str
    imap_host: str
    imap_port: int
    imap_user: str
    imap_password: str
    imap_folders: list[str]
    mail_timeout_s: int
    dry_run: bool


def load_config(env: dict[str, str]) -> Config:
    """Liest die Umgebung. Fehlende Pflichtwerte beenden den Lauf mit Exit-Code 2."""
    required = ("HU_MONITOR_TOKEN", "MONITOR_IMAP_USER", "MONITOR_IMAP_PASSWORD")
    missing = [name for name in required if not env.get(name)]
    if missing:
        print("Konfiguration unvollstaendig, es fehlt: " + ", ".join(missing), file=sys.stderr)
        raise SystemExit(2)

    folders = [f.strip() for f in env.get("MONITOR_IMAP_FOLDERS", "INBOX,Monitor").split(",") if f.strip()]
    try:
        port = int(env.get("MONITOR_IMAP_PORT") or 993)
        timeout = int(env.get("MONITOR_MAIL_TIMEOUT_S") or 600)
    except ValueError:
        print("MONITOR_IMAP_PORT und MONITOR_MAIL_TIMEOUT_S muessen Zahlen sein.", file=sys.stderr)
        raise SystemExit(2)

    return Config(
        site_url=(env.get("MONITOR_SITE_URL") or "https://hasimuener.de").rstrip("/"),
        token=env["HU_MONITOR_TOKEN"],
        sender_email=env.get("MONITOR_SENDER_EMAIL") or "kontakt@hasimuener.de",
        imap_host=env.get("MONITOR_IMAP_HOST") or "mail.infomaniak.com",
        imap_port=port,
        imap_user=env["MONITOR_IMAP_USER"],
        imap_password=env["MONITOR_IMAP_PASSWORD"],
        imap_folders=folders or ["INBOX"],
        mail_timeout_s=timeout,
        dry_run=env.get("MONITOR_DRY_RUN") == "1",
    )


# ── Reine Hilfsfunktionen (ohne Netz, im Selbsttest geprueft) ───────────────


def make_run_id(now: datetime | None = None) -> str:
    """run-YYYYMMDD-HHMM-xxxx in UTC mit vier zufaelligen Zeichen [a-z0-9]."""
    now = now or datetime.now(timezone.utc)
    suffix = "".join(random.choices(string.ascii_lowercase + string.digits, k=4))
    return f"run-{now:%Y%m%d-%H%M}-{suffix}"


def add_marker(body: str, content_type: str, token: str, run_id: str) -> str | None:
    """Ergaenzt den Request-Body um den Testmarker. None, wenn das Format unbekannt ist."""
    marker = {"monitor_token": token, "monitor_run_id": run_id}
    content_type = (content_type or "").lower()
    try:
        if "json" in content_type:
            data = json.loads(body or "{}")
            if not isinstance(data, dict):
                return None
            data.update(marker)
            return json.dumps(data)
        if "x-www-form-urlencoded" in content_type:
            pairs = [(k, v) for k, v in parse_qsl(body or "", keep_blank_values=True) if k not in marker]
            return urlencode(pairs + list(marker.items()))
    except ValueError:
        return None
    return None


def normalize(text: str) -> str:
    """Einheitliche Schreibweise fuer den Betreffvergleich (Unicode, Leerraum)."""
    return " ".join(unicodedata.normalize("NFC", text).split())


def decode_subject(raw_header: bytes) -> str:
    """Betreff aus einem rohen Header-Block lesen (RFC 2047 dekodiert, Leerraum normalisiert)."""
    message = message_from_bytes(raw_header)
    value = message.get("Subject", "")
    try:
        return normalize(str(make_header(decode_header(value))))
    except (UnicodeError, LookupError):
        return normalize(value)


def classify_subjects(subjects: list[str], run_id: str) -> dict[str, bool]:
    """mailAdmin: Betreff traegt `[MONITOR <lauf>]`; mailConfirmation: `[MONITOR <lauf> · Bestätigung]`."""
    admin_tag = normalize(f"[MONITOR {run_id}]")
    confirmation_tag = normalize(f"[MONITOR {run_id} · Bestätigung]")
    normalized = [normalize(s) for s in subjects]
    return {
        "mailAdmin": any(admin_tag in s for s in normalized),
        "mailConfirmation": any(confirmation_tag in s for s in normalized),
    }


def build_result(run_id: str, started_at: datetime, checks: dict[str, bool], durations: dict[str, int]) -> dict:
    """Body fuer POST /monitor/result. WordPress ergaenzt crmStored und eventRecorded selbst."""
    return {
        "runId": run_id,
        "startedAt": started_at.astimezone(timezone.utc).strftime("%Y-%m-%dT%H:%M:%SZ"),
        "checks": {name: bool(checks.get(name, False)) for name in CHECKS},
        "durationsMs": {name: int(ms) for name, ms in durations.items() if name in CHECKS},
    }


def ms_since(start: float) -> int:
    return int((time.monotonic() - start) * 1000)


# ── Browser ─────────────────────────────────────────────────────────────────


@dataclass
class BrowserOutcome:
    checks: dict[str, bool] = field(default_factory=lambda: {name: False for name in BROWSER_CHECKS})
    durations: dict[str, int] = field(default_factory=dict)
    submitted_at: float | None = None  # time.monotonic() der Antwort auf das Absenden


def message_text(run_id: str) -> str:
    return (
        f"Automatische Testanfrage des Anfrage-Monitors ({run_id}). "
        "Sie wird täglich ausgelöst und braucht keine Antwort."
    )


def run_browser(cfg: Config, run_id: str) -> BrowserOutcome:
    """Fuellt das Formular wie eine Person und prueft Seite, Absenden und Erfolgsmeldung."""
    from playwright.sync_api import sync_playwright  # erst hier: Selbsttest braucht kein Playwright

    outcome = BrowserOutcome()
    stage = "start"

    def inject_marker(route) -> None:
        request = route.request
        if request.method != "POST":
            route.continue_()
            return
        try:
            body = request.post_data or ""
        except UnicodeError:
            route.continue_()
            return
        patched = add_marker(body, request.headers.get("content-type", ""), cfg.token, run_id)
        if patched is None:
            route.continue_()  # unbekanntes Format: der Server sieht keinen Marker, formSubmit schlaegt fehl
        else:
            route.continue_(post_data=patched)

    with sync_playwright() as playwright:
        browser = playwright.chromium.launch(headless=True)
        try:
            page = browser.new_context(locale="de-DE").new_page()
            page.set_default_timeout(STEP_TIMEOUT_MS)
            page.route(CONTACT_ENDPOINT, inject_marker)

            # 1. Seite laden: HTTP 2xx.
            stage = "pageLoad"
            started = time.monotonic()
            response = page.goto(cfg.site_url + CONTACT_PATH, wait_until="load", timeout=30_000)
            outcome.durations["pageLoad"] = ms_since(started)
            outcome.checks["pageLoad"] = bool(response and 200 <= response.status < 300)
            log_check("pageLoad", outcome.checks["pageLoad"], outcome.durations)
            if not outcome.checks["pageLoad"]:
                return outcome

            # 2. Formular ausfuellen und absenden. Schritte: Thema -> Nachricht -> Kontakt.
            stage = "formFill"
            page.wait_for_selector(FORM, state="visible")
            if page.is_visible(FOCUS_SELECT):  # ohne URL-Vorgabe ist der Themenschritt sichtbar
                focus_value = page.eval_on_selector(
                    FOCUS_SELECT,
                    "s => { const o = [...s.options].find(o => o.value && !o.disabled && !o.hidden); return o ? o.value : null; }",
                )
                if not focus_value:
                    raise RuntimeError("keine waehlbare Themenoption")
                page.select_option(FOCUS_SELECT, focus_value)  # contact.js schaltet nach kurzer Pause weiter
            page.fill(MESSAGE_FIELD, message_text(run_id))
            page.click(NEXT_BUTTON)
            page.fill(NAME_FIELD, "Anfrage-Monitor")
            page.fill(EMAIL_FIELD, cfg.sender_email)
            page.check(CONSENT_FIELD)

            stage = "formSubmit"
            clicked = time.monotonic()
            with page.expect_response(
                lambda r: bool(CONTACT_ENDPOINT.search(r.url)) and r.request.method == "POST", timeout=30_000
            ) as response_info:
                page.click(SUBMIT_BUTTON)
            reply = response_info.value
            outcome.submitted_at = time.monotonic()
            outcome.durations["formSubmit"] = ms_since(clicked)
            body = reply.json()
            monitor = body.get("monitor") if isinstance(body, dict) else None
            outcome.checks["formSubmit"] = bool(
                reply.status == 201
                and body.get("ok") is True
                and isinstance(monitor, dict)
                and monitor.get("runId") == run_id
            )
            log_check("formSubmit", outcome.checks["formSubmit"], outcome.durations)
            if not outcome.checks["formSubmit"]:
                return outcome

            # 3. Erfolgsmeldung im Formular.
            stage = "uiSuccess"
            waited = time.monotonic()
            try:
                page.wait_for_selector(FEEDBACK_SUCCESS, state="visible", timeout=UI_SUCCESS_TIMEOUT_MS)
                outcome.checks["uiSuccess"] = True
            except Exception as error:  # Timeout oder Seitenfehler
                print(f"uiSuccess: {type(error).__name__}")
            outcome.durations["uiSuccess"] = ms_since(waited)
            log_check("uiSuccess", outcome.checks["uiSuccess"], outcome.durations)
        except Exception as error:
            # Nicht erreichte Schritte bleiben false. Nur der Fehlertyp wird ausgegeben.
            print(f"Browserlauf abgebrochen bei {stage}: {type(error).__name__}")
        finally:
            browser.close()
    return outcome


# ── Postfach ────────────────────────────────────────────────────────────────


class MailboxWatcher:
    """Liest nur Betreffzeilen markierter Mails. EXAMINE und BODY.PEEK veraendern nichts."""

    def __init__(self, cfg: Config) -> None:
        self.cfg = cfg
        self.conn: imaplib.IMAP4_SSL | None = None

    def connect(self) -> None:
        self.close()
        conn = imaplib.IMAP4_SSL(self.cfg.imap_host, self.cfg.imap_port, timeout=60)
        conn.login(self.cfg.imap_user, self.cfg.imap_password)
        self.conn = conn

    def close(self) -> None:
        if self.conn is None:
            return
        try:
            self.conn.logout()
        except (imaplib.IMAP4.error, OSError):
            pass
        self.conn = None

    def subjects(self, run_id: str) -> list[str]:
        """Betreffzeilen aller Mails mit `MONITOR <lauf>` im Betreff, ueber alle Ordner."""
        if self.conn is None:
            self.connect()
        assert self.conn is not None
        found: list[str] = []
        for folder in self.cfg.imap_folders:
            typ, _ = self.conn.select('"' + folder.replace("\\", "\\\\").replace('"', '\\"') + '"', readonly=True)
            if typ != "OK":
                continue  # Ordner existiert nicht: ueberspringen
            typ, data = self.conn.search(None, "SUBJECT", f'"MONITOR {run_id}"')
            ids = data[0].split() if typ == "OK" and data and data[0] else []
            if not ids:
                continue
            typ, parts = self.conn.fetch(b",".join(ids).decode("ascii"), "(BODY.PEEK[HEADER.FIELDS (SUBJECT)])")
            if typ != "OK":
                continue
            found.extend(decode_subject(item[1]) for item in parts if isinstance(item, tuple))
        return found


def wait_for_mails(cfg: Config, run_id: str, since: float) -> tuple[dict[str, bool], dict[str, int]]:
    """Fragt alle 20 Sekunden das Postfach ab, bis beide Mails da sind oder die Zeit ablaeuft."""
    checks = {"mailAdmin": False, "mailConfirmation": False}
    durations: dict[str, int] = {}
    deadline = time.monotonic() + cfg.mail_timeout_s
    watcher = MailboxWatcher(cfg)
    try:
        while True:
            try:
                seen = classify_subjects(watcher.subjects(run_id), run_id)
            except (imaplib.IMAP4.error, OSError) as error:
                print(f"IMAP-Abfrage fehlgeschlagen: {type(error).__name__}")
                watcher.close()  # naechste Runde meldet sich neu an
                seen = {}
            for name, present in seen.items():
                if present and not checks[name]:
                    checks[name] = True
                    durations[name] = ms_since(since)
                    log_check(name, True, durations)
            if all(checks.values()) or time.monotonic() + POLL_INTERVAL_S > deadline:
                break
            time.sleep(POLL_INTERVAL_S)
    finally:
        watcher.close()
    for name, present in checks.items():
        if not present:
            log_check(name, False, durations)
    return checks, durations


# ── Ergebnis melden ─────────────────────────────────────────────────────────


def post_result(cfg: Config, result: dict) -> dict | None:
    """Sendet das Ergebnis an WordPress. Gibt die Antwort als dict zurueck oder None bei Fehlern."""
    request = urllib.request.Request(
        cfg.site_url + RESULT_PATH,
        data=json.dumps(result).encode("utf-8"),
        method="POST",
        headers={
            "Authorization": "Bearer " + cfg.token,
            "Content-Type": "application/json",
            "User-Agent": "anfrage-monitor/1.0",
        },
    )
    for attempt in range(1, 4):
        try:
            with urllib.request.urlopen(request, timeout=30) as response:
                print(f"Ergebnis gemeldet: HTTP {response.status}")
                return json.loads(response.read().decode("utf-8"))
        except urllib.error.HTTPError as error:
            print(f"Ergebnis abgelehnt: HTTP {error.code}")
            if error.code < 500:
                return None
        except (urllib.error.URLError, OSError, ValueError) as error:
            print(f"Ergebnis nicht gesendet: {type(error).__name__}")
        if attempt < 3:
            time.sleep(10)
    return None


# ── Ablauf ──────────────────────────────────────────────────────────────────


def log_check(name: str, ok: bool, durations: dict[str, int]) -> None:
    ms = durations.get(name)
    print(f"{name}: {'true' if ok else 'false'}" + (f" ({ms} ms)" if ms is not None else ""))


def main(argv: list[str]) -> int:
    if "--self-test" in argv:
        return self_test()

    cfg = load_config(dict(os.environ))
    run_id = make_run_id()
    started_at = datetime.now(timezone.utc)
    print(f"Lauf {run_id} gegen {cfg.site_url}")

    outcome = run_browser(cfg, run_id)
    checks = {name: False for name in CHECKS} | outcome.checks
    durations = dict(outcome.durations)

    # Postfach nur pruefen, wenn der Server die markierte Anfrage angenommen hat.
    if outcome.checks.get("formSubmit") and outcome.submitted_at is not None:
        mail_checks, mail_durations = wait_for_mails(cfg, run_id, outcome.submitted_at)
        checks |= mail_checks
        durations |= mail_durations

    result = build_result(run_id, started_at, checks, durations)

    if cfg.dry_run:
        print("MONITOR_DRY_RUN=1: Ergebnis wird nicht gemeldet.")
        print(json.dumps(result, indent=2))
        return 0 if all(result["checks"].values()) else 1

    reply = post_result(cfg, result)
    run = reply.get("run") if isinstance(reply, dict) else None
    if not isinstance(run, dict):
        print("run.ok: unbekannt (keine Antwort von WordPress)")
        return 1
    print(f"run.ok: {run.get('ok')}")
    print(f"run.failedAt: {run.get('failedAt')}")
    return 0 if run.get("ok") is True else 1


# ── Selbsttest (ohne Netz) ──────────────────────────────────────────────────


def self_test() -> int:
    from email.header import Header

    failures: list[str] = []

    def check(condition: bool, label: str) -> None:
        print(("ok    " if condition else "FEHLER ") + label)
        if not condition:
            failures.append(label)

    # runId
    now = datetime(2026, 10, 25, 4, 17, tzinfo=timezone.utc)
    run_id = make_run_id(now)
    check(re.fullmatch(r"run-20261025-0417-[a-z0-9]{4}", run_id) is not None, "runId hat das Format run-YYYYMMDD-HHMM-xxxx")
    check(RUN_ID_PATTERN.match(run_id) is not None, "runId erfuellt die Regel des Servers")

    # Marker im Request-Body
    patched = json.loads(add_marker('{"name":"x"}', "application/json; charset=utf-8", "tok", run_id) or "{}")
    check(patched == {"name": "x", "monitor_token": "tok", "monitor_run_id": run_id}, "Marker im JSON-Body")
    form = dict(parse_qsl(add_marker("name=x&monitor_token=alt", "application/x-www-form-urlencoded", "tok", run_id) or ""))
    check(form == {"name": "x", "monitor_token": "tok", "monitor_run_id": run_id}, "Marker im Formular-Body ersetzt alte Werte")
    check(add_marker("x", "text/plain", "tok", run_id) is None, "Unbekanntes Format bleibt unveraendert")
    check(add_marker("[1]", "application/json", "tok", run_id) is None, "JSON ohne Objekt bleibt unveraendert")

    # Betreffzeilen: echte RFC-2047-Formen (Base64 und Quoted-Printable) und gefaltete Header
    admin = f"[MONITOR {run_id}] [hasimuener.de] Neue Projektanfrage - Anfrage-Monitor"
    confirmation = f"[MONITOR {run_id} · Bestätigung] [hasimuener.de] Projektanfrage eingegangen"
    raw_admin = b"Subject: " + Header(admin, "utf-8").encode().encode("ascii") + b"\r\n\r\n"
    raw_confirmation = (
        b"Subject: "
        + Header(confirmation, "utf-8", maxlinelen=40).encode(linesep="\r\n ").encode("ascii")
        + b"\r\n\r\n"
    )
    qp = b"Subject: =?utf-8?q?[MONITOR_" + run_id.encode() + b"_=C2=B7_Best=C3=A4tigung]_Eingang?=\r\n\r\n"
    check(decode_subject(raw_admin) == admin, "Base64-Betreff wird dekodiert")
    check(decode_subject(raw_confirmation) == confirmation, "Gefalteter Betreff wird dekodiert")
    check(decode_subject(qp).startswith(f"[MONITOR {run_id} · Bestätigung]"), "Quoted-Printable-Betreff wird dekodiert")

    both = classify_subjects([decode_subject(raw_admin), decode_subject(raw_confirmation)], run_id)
    check(both == {"mailAdmin": True, "mailConfirmation": True}, "Beide Mails erkannt")
    only_admin = classify_subjects([decode_subject(raw_admin)], run_id)
    check(only_admin == {"mailAdmin": True, "mailConfirmation": False}, "Nur die interne Mail erkannt")
    only_confirmation = classify_subjects([decode_subject(qp)], run_id)
    check(only_confirmation == {"mailAdmin": False, "mailConfirmation": True}, "Bestätigung zählt nicht als interne Mail")
    other = classify_subjects([admin.replace(run_id, "run-20200101-0000-zzzz"), "Neue Anfrage"], run_id)
    check(other == {"mailAdmin": False, "mailConfirmation": False}, "Fremder Lauf und normale Mails werden ignoriert")

    # Postfachabfrage mit Attrappe: nur lesend, fehlender Ordner wird uebersprungen
    class FakeConn:
        def __init__(self) -> None:
            self.calls: list[tuple] = []

        def select(self, mailbox, readonly=False):
            self.calls.append(("select", mailbox, readonly))
            return ("OK", [b"2"]) if mailbox == '"INBOX"' else ("NO", [b"nicht vorhanden"])

        def search(self, charset, *criteria):
            self.calls.append(("search", charset, criteria))
            return "OK", [b"1 2"]

        def fetch(self, message_set, parts):
            self.calls.append(("fetch", message_set, parts))
            return "OK", [(b"1 (BODY[HEADER.FIELDS (SUBJECT)] {1}", raw_admin), b")", (b"2 (BODY[HEADER.FIELDS (SUBJECT)] {1}", qp), b")"]

    fake = FakeConn()
    mail_cfg = load_config({"HU_MONITOR_TOKEN": "t", "MONITOR_IMAP_USER": "u", "MONITOR_IMAP_PASSWORD": "p"})
    watcher = MailboxWatcher(mail_cfg)
    watcher.conn = fake  # type: ignore[assignment]
    subjects = watcher.subjects(run_id)
    check(classify_subjects(subjects, run_id) == {"mailAdmin": True, "mailConfirmation": True}, "Abfrage findet beide Mails im vorhandenen Ordner")
    check(all(c[2] is True for c in fake.calls if c[0] == "select"), "Ordner nur mit EXAMINE (readonly) geoeffnet")
    check(all("PEEK" in c[2] for c in fake.calls if c[0] == "fetch"), "Abruf nur mit BODY.PEEK")
    check(len([c for c in fake.calls if c[0] == "select"]) == 2 and len([c for c in fake.calls if c[0] == "fetch"]) == 1, "Fehlender Ordner Monitor wird uebersprungen")
    watcher.conn = None

    # Ergebnis-JSON
    result = build_result(run_id, now, {"pageLoad": True, "formSubmit": True}, {"pageLoad": 812, "other": 1})
    check(result["startedAt"] == "2026-10-25T04:17:00Z", "startedAt als ISO-Zeit in UTC")
    check(list(result["checks"]) == list(CHECKS) and result["checks"]["uiSuccess"] is False, "Alle fünf Prüfungen, fehlende sind false")
    check(result["durationsMs"] == {"pageLoad": 812}, "Nur bekannte Dauern, ganzzahlig")
    check("token" not in json.dumps(result).lower(), "Kein Geheimnis im Ergebnis")

    # Konfiguration
    try:
        load_config({})
        check(False, "Fehlende Pflichtwerte beenden mit Exit-Code 2")
    except SystemExit as stop:
        check(stop.code == 2, "Fehlende Pflichtwerte beenden mit Exit-Code 2")
    cfg = load_config({"HU_MONITOR_TOKEN": "t", "MONITOR_IMAP_USER": "u", "MONITOR_IMAP_PASSWORD": "p", "MONITOR_SITE_URL": "https://example.test/"})
    check(cfg.site_url == "https://example.test" and cfg.imap_folders == ["INBOX", "Monitor"] and cfg.mail_timeout_s == 600, "Standardwerte")

    print(f"{'FEHLER' if failures else 'OK'}: Selbsttest {'fehlgeschlagen' if failures else 'bestanden'}")
    return 1 if failures else 0


if __name__ == "__main__":
    sys.exit(main(sys.argv[1:]))

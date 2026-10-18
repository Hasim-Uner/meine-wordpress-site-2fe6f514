# Chat-Assistent

Stand: 2026-10-18. **Phase zwei: öffentlicher Assistent gebaut, Abnahme im
Modus `preview` offen.** `live` erst nach Hasims Freigabe und juristischer
Prüfung des Datenschutz-Entwurfs.

## Modi

| `HU_CHAT_MODE` | Wirkung |
| --- | --- |
| `off` (Standard) | keine Route, kein Admin-Menü, keine Fassade, kein Cron |
| `preview` | Fassade, Chat und Übergabe nur für eingeloggte Admins; Admin-Streaming-Test aktiv |
| `live` | Fassade, Chat und Übergabe für alle Besucher; Streaming-Test gesperrt |

Alle übrigen Konstanten und Prüfungen gelten unverändert (Abschnitt
„Konfiguration“ unten). Fehlt eine, ist der Assistent aus.

## Oberfläche

- `facade.php` gibt per `wp_footer` ein rundes Symbol (`.tafel`, umlaufender
  Lichtring) mit Inline-CSS und Ladeskript aus. Vor Klick oder Scroll-Auslöser
  gibt es keine Netzanfrage. Hover oder Fokus laden vor.
- **Auslöser:** Klick auf das Symbol, oder einmal je Sitzung, sobald der
  Besucher rund 78 % der Seite gescrollt hat (nur auf Seiten, die mindestens
  1,6 Bildschirmhöhen lang sind). Wer schließt oder schon geöffnet hat, sieht
  keinen automatischen Auftritt mehr (`sessionStorage` `hu-chat-auto`). Der
  automatische Auftritt setzt keinen Fokus, damit mobil keine Tastatur aufgeht.
- **Leiste:** breite Eingabeleiste unten mittig (bis 780 px, mobil volle
  Breite über der Sticky-CTA), dunkle Tafel mit umlaufendem Lichtrand. Die
  Begrüßung fällt Wort für Wort ein, darunter drei Schnellwahlen und ein großes
  Eingabefeld mit wechselnden Beispielfragen. Das Gespräch wächst über der
  Leiste nach oben.
- **Begrüßung und Schnellwahl** (`hu_chat_intro()`): Standard „Wie kann ich
  Ihnen helfen? Kurz beraten oder direkt ein Termin?“ mit Beratung, Termin
  buchen (Cal.com aus `nexus_get_audit_calendar_url()`), Preise. White-Label:
  ihr-Form, Fit-Gespräch (`nexus_get_whitelabel_calendar_url()`), Ablauf.
  Energie: Marktcheck statt Preise.
- KI-Hinweis und Datenschutz-Link stehen dauerhaft unter dem Eingabefeld.
- Nicht ausgegeben auf `/datenschutz/`, `/impressum/`, den noindex-Slugs und
  -Templates aus `seo-meta.php`, im Admin und im Customizer.
- Markdown wird ohne `innerHTML` gerendert: Absätze, fett, Listen, Links.
  Klickbar sind nur Links auf den eigenen Host und die beiden Terminlinks
  (neuer Tab), alle anderen bleiben Text.
- Verlauf in `sessionStorage` (`hu-chat-v1`), „Neues Gespräch“ unter der
  Eingabe. Esc schließt. Die Leiste ist nicht modal.
- Tracking-Hooks: `chat_open`, `chat_starter`, `chat_booking`,
  `chat_marktcheck`, `chat_handover_submit` als `data-track-*`, ohne Inhalte.

## Antwortstil

Der Systemprompt stellt Kürze über alles: höchstens zwei kurze Sätze, genau
eine Frage oder ein nächster Schritt am Ende, keine Einleitung, keine
Aufzählungen ohne Aufforderung. Der Assistent spricht über Haşim in der
dritten Person („Haşim meldet sich …“) und nie als Haşim. Die Kundenführung
(zuhören, eine entscheidende Frage, leichte Ja-Schritte, entlasten) ist mit
kurzen Gut-/Schlecht-Beispielen hinterlegt. Wer einen Menschen sprechen will,
bekommt sofort den Terminlink. `max_tokens` für den sichtbaren Text: 450.

Terminlinks rendert das Fenster als Schaltfläche („Termin mit Haşim wählen“),
auch wenn das Modell sie fett setzt oder als nackte Adresse schreibt.
`cal-embed.js` öffnet sie als Overlay und gibt `metadata[quelle]=chat` mit.

## Nachdenken vor der Antwort

Schalter unter Werkzeuge → KI-Assistent: Aus, Normal (Standard), Gründlich.
Bis Claude 4.5 wird daraus ein festes Budget (2048 bzw. 4096 Tokens), bei
neueren Modellen adaptives Denken mit Effort `medium` bzw. `high`.
`HU_CHAT_THINKING` in `wp-config.php` überschreibt den Schalter:

| Wert | Wirkung |
| --- | --- |
| leer, `off` | kein Nachdenken (Standard) |
| Zahl ab `1024` | festes Denkbudget in Tokens, für Claude 4.5 und älter (Haiku 4.5) |
| `low`, `medium`, `high`, `max` | adaptives Denken mit Effort, für neuere Modelle |

Das Budget kommt zu den 450 Antworttokens hinzu und wird in der
Kostenreservierung mitgerechnet. Das Nachdenken erreicht den Besucher nie.
Jede Änderung des Werts leert den Prompt-Cache einmal. Neuere Modelle lehnen
eine feste Zahl ab, Haiku 4.5 lehnt `low` bis `max` ab: Wert und Modell
gemeinsam wechseln.

## Gespräch: `POST /nexus/v1/chat/messages`

- Payload `{ "messages": [{ "role": "user"|"assistant", "text": "…" }], "page": "/pfad/" }`.
  Nur Text, keine Tool-Blöcke. Führende Assistent-Turns (Begrüßung) fallen
  weg, gleiche Rollen werden zusammengelegt, der letzte Turn muss vom
  Besucher sein. Höchstens 20 Besucher-Turns, je 2.000 Zeichen,
  Assistent-Turns je 6.000 Zeichen. Steuerzeichen außer Zeilenumbruch und
  Tab werden entfernt. Die Seite ist nur ein Hinweis; alles außer einem
  relativen Pfad wird `/`.
- Zugriff: Modus und Konfiguration, Origin bzw. Referer auf den eigenen Host.
  `preview` verlangt `manage_options`; dafür sendet die Fassade nur
  eingeloggten Nutzern einen REST-Nonce, anonyme Seiten bleiben cachebar.
- Reihenfolge: Payload (die Bytezahl bestimmt die Reservierung), Systemprompt,
  Budget, Stundenlimit (30 je gesalzenem IP-Hash). Die Reservierung zählt jedes
  Request-Byte als höchstens ein Token zum höchsten Preis plus 450
  Ausgabetokens, für Stream und Fallback zusammen.
- Bedrock-Body: Systemprompt mit `cache_control: ephemeral`, ein zweiter
  Systemblock mit der aktuellen Seite, Tool `anfrage_uebergeben`,
  `max_tokens` 450.
- SSE an den Browser: `text`, `tool`, `done`, `error`. `probe`-Events bleiben
  intern. Tool-Aufrufe werden vor der Ausgabe bereinigt: nur
  `anfrage_uebergeben`, nur die sechs Felder, E-Mail geprüft, `weg` aus der
  Liste, Längen gekappt.
- Fehlertext nennt nur die Kanon-Adresse. Logs und Telemetrie erhalten
  feste Kategorien, nie Inhalte.

## Übergabe: `POST /nexus/v1/chat/handover`

- Nur nach Klick des Besuchers auf der Übergabe-Karte. Name, E-Mail und
  Firma sind editierbar, die Zusammenfassung sichtbar, Einwilligung im
  Wortlaut des Kontaktformulars Pflicht. „Gesprächsverlauf mitsenden“ ist
  standardmäßig aus.
- Honeypot, eigenes Stundenlimit (5) und das Limit des Kontaktformulars.
- Ablauf wie White-Label: CRM-Kontakt (`chat_handover`, Segmente
  `contact_inquiry` und `chat_handover`), Aktivität „Chat-Anfrage · Weg“,
  interne Mail an `hu_get_contact_email()`, bei Mailfehler
  `nexus_record_lead_notification_failure`, dann `anfrage_gesendet` mit
  Formular `chat`.
- Der Empfänger kommt aus `hu_chat_handover_notification_email()`. Kein
  Request-Feld und kein Modell-Output erreicht ihn; der serverseitige Filter
  folgt dem Muster der anderen Strecken. Keine Bestätigungsmail an den
  Besucher, die Bestätigung steht nur im Fenster.

## Wissen

- `knowledge.php` liest alle internen Routen aus `nexus_get_llms_txt_sections()`
  per Loopback, extrahiert `<main>` (ohne Navigation, Formulare, Skripte,
  Kopf, Fuß und den Chat selbst) als Markdown und hängt die letzten 40
  Blogartikel nur mit Titel, URL, Kurzfassung und H2-Gliederung an.
- Je Seite höchstens 14.000 Zeichen, gesamt 150.000. Blogartikel fallen
  zuerst weg, die Reihenfolge des Routen-Index setzt Money Pages nach vorn.
- Gelingt weniger als die Hälfte der Seiten, bleibt die alte Datei aktiv.
- Auslöser: täglicher Cron, einmal nach jedem neuen Deploy-SHA, stündlicher
  Wiederholversuch nach Fehlschlag, Knopf unter Werkzeuge → KI-Assistent.
- Die Datei enthält nur öffentlichen Website-Text. `.htaccess` und
  `index.php` im Ordner sind Höflichkeit für Apache; Raidboxes (nginx)
  liefert sie theoretisch aus, das ist hinnehmbar.

## Abnahme in `preview`

1. Werkzeuge → KI-Assistent → „Wissen jetzt aufbauen“; Seitenanzahl prüfen.
2. Startseite eingeloggt: Knopf unten rechts, vor dem Klick keine neue
   Netzanfrage (DevTools).
3. Die 20 Testfälle aus dem Auftrag durchspielen; 16 (Prompt-Ausleitung) und
   17 (Mail an fremde Adresse) müssen bestehen.
4. Übergabe mit eigener Adresse: CRM-Kontakt, Aktivität und Mail an die
   Kanon-Adresse prüfen.
5. Tastatur, Screenreader, 360 px ohne horizontales Scrollen.

---

# Phase eins: Admin-Transport-Spike (Historie)

## Spike-Contract

Dieser Stand implementiert ausschließlich den ersten, verpflichtenden Spike
und die separat beauftragte Prompt-Vorlage samt Kanon-Ersetzung. Ein öffentlicher
Chat, Knowledge-Builder, Tool-Aufruf und CRM-Handover sind noch nicht aktiv.
Es gibt keinen Orb, Footer-Hook, Browser-Speicher oder öffentlichen Chat-Asset-
Request. Bestehende Formulare, CRM- und Brevo-Strecken ändern ihren Contract nicht.

Der tatsächliche Testpfad:

1. Ein eingeloggter Administrator öffnet **Werkzeuge → Chat: Streaming-Test**.
2. Erst der Klick startet einen First-Party-POST an `/wp-json/nexus/v1/chat`.
3. Das Payload ist exakt `{}` oder `{"padding":true|false}`; der einzige
   Schalter wählt, ob jede Sendung aufgefüllt wird (`padded`) oder zum
   Vergleich nicht (`plain`). Nachrichten, Empfänger, Adressen und
   frei wählbare Modell- oder Hostnamen werden nicht akzeptiert.
4. Die Route verlangt `HU_CHAT_MODE=preview`,
   vollständige Runtime-Konfiguration, `manage_options` und einen REST-Nonce.
   Die WordPress-Umgebung schränkt den Durchlauf nicht ein; er ist auch auf
   der Live-Box erlaubt. `live` und anonyme Besucher bleiben gesperrt.
   Bei `off`, fehlender/ungültiger Konfiguration oder fehlendem cURL werden
   weder Route noch Admin-Menü registriert. Auch ein direkter Aufruf der
   Admin-Seite erzeugt dann kein Markup oder Skript.
5. Vor Inferenz reserviert die Route atomar das konservative Kostenmaximum
   für Stream **und** einen möglichen Fallback im laufenden UTC-Monat. Danach
   folgen Stundenlimit (gesalzener IP-Hash, höchstens 30 Aufrufe) und Payload.
6. WordPress sendet nur den festen Zähltest „Zähle langsam von 1 bis 30, jede
   Zahl in eine eigene Zeile.“ mit `max_tokens` 200 an Bedrock Frankfurt
   (`HU_CHAT_SPIKE_PROMPT`, `HU_CHAT_SPIKE_MAX_TOKENS` in `config.php`). Die
   Budgetreservierung deckt diese Ausgabegrenze für Stream und Fallback ab.
   Eigene SigV4-Signatur, cURL, TLS-Prüfung, keine Redirects und kein SDK.
7. Binäre AWS-Frames werden inkrementell einschließlich beider CRCs geprüft.
   Anthropic-Textdeltas werden als SSE `text` ausgegeben; Start und Transport
   erscheinen als `probe`, Abschluss als `done`, Fehler als `error`.
8. Ein Fallback auf `InvokeModel` erfolgt höchstens einmal und ausschließlich
   vor sichtbarem Text. Er gilt nicht als bestandener Streaming-Test.
9. Die endgültige Usage ersetzt bekannte Kostenreservierungen. Unterbrochene
   oder unklare Versuche behalten ihr Kostenmaximum. Zähler speichern nur
   Kosten und Tokenzahlen. Datenbankfehler verhindern neue Modellaufrufe.

`rest_pre_serve_request` liefert SSE ohne REST-JSON-Hülle. Vor dem ersten Byte
schaltet die Route PHP-seitige Komprimierung ab (`zlib.output_compression`,
`brotli.output_compression`, unter Apache `no-gzip`), schließt alle
Output-Buffer und entfernt `Content-Length` (`hu_chat_stream_begin()` in
`sse.php`). Gesendet werden `Content-Type: text/event-stream`,
`Cache-Control: no-store, no-transform`, `Content-Encoding: identity` und
`X-Accel-Buffering: no`. `identity` ist keine registrierte Inhaltskodierung, aber nginx
und viele Proxys komprimieren nicht erneut, sobald eine Kodierung gesetzt ist.
Was danach Raidboxes (nginx, Varnish, CDN) tut, kann PHP nicht erzwingen.

**Sendetakt gegen größenbasierte Proxy-Puffer** (`HU_Chat_SSE_Writer`):
Textdeltas werden gesammelt und höchstens alle 120 ms als ein `text`-Event
gesendet (`HU_CHAT_FLUSH_INTERVAL_MS`). Andere Events (`probe`, `done`,
`error`) gehen sofort raus, vorher der gesammelte Text. Damit Text auch
während einer Bedrock-Pause rechtzeitig rausgeht, prüft cURLs
Progress-Callback den Takt ebenfalls. Jede Sendung wird mit einem
SSE-Kommentar (`:` plus Leerzeichen) auf ganze Blöcke von `HU_CHAT_FLUSH_PAD`
Byte aufgefüllt, danach `flush()`. Standard 4096, per `wp-config.php`
überschreibbar, `0` schaltet das Auffüllen ab, Maximum 65536, ungültige
Werte behalten 4096. Der Browser-Parser ignoriert Kommentare. Kosten: ein
Block je Sendung, also bei 120 ms Takt rund 33 KB je Sekunde Antwortzeit.
Auf der Testseite wählt die Checkbox `padded` (Standard, konfigurierter
Block) oder `plain` (ohne Auffüllen, gleicher Takt) zum Vergleich.
Ein Client-Disconnect verhindert die abschließende Budgetabrechnung nicht.

Fehlertexte nennen nur die kanonische Kontaktadresse. `error_log` und vorhandene
API-Telemetrie erhalten ausschließlich feste Kategorien, keine AWS-Fehlertexte,
Nachrichten, Zugangsdaten oder Payload-Schlüssel. Die allgemeine API-Telemetrie
überspringt `/chat` und `/chat/*`, da auch fremde JSON-Schlüssel PII enthalten können.

## Konfiguration und externe Voraussetzungen

Nur in der `wp-config.php` der Live-Box oder einer bestehenden privaten Runtime-
Konfiguration. Keine Zugangsdaten ins Repo, PR, Testprotokoll oder Chat kopieren.

| Konstante | Voraussetzung |
| --- | --- |
| `HU_CHAT_MODE` | `preview`; Standard `off` |
| `HU_BEDROCK_ACCESS_KEY_ID` | IAM-Benutzer `iris`, lokal auf Raidboxes hinterlegt |
| `HU_BEDROCK_SECRET_ACCESS_KEY` | zugehöriger privater Schlüssel |
| `HU_BEDROCK_REGION` | ausschließlich `eu-central-1` |
| `HU_BEDROCK_MODEL_ID` | vorerst `eu.anthropic.claude-haiku-4-5-20251001-v1:0`, explizit setzen |
| `HU_CHAT_MONTHLY_BUDGET_USD` | Standard `20` |
| `HU_CHAT_FLUSH_PAD` | optional; Blockgröße der SSE-Auffüllung in Byte, Standard `4096`, `0` = aus |
| `HU_CHAT_PRICE_INPUT` | verifizierter USD-Preis je Million Eingabetokens |
| `HU_CHAT_PRICE_OUTPUT` | verifizierter USD-Preis je Million Ausgabetokens |
| `HU_CHAT_PRICE_CACHE_WRITE` | verifizierter USD-Preis je Million Cache-Schreibtokens |
| `HU_CHAT_PRICE_CACHE_READ` | verifizierter USD-Preis je Million Cache-Lesetokens |

Die Modell-ID hat **keinen Code-Standardwert**. Das Format verlangt ein
Anthropic-EU-Profil; ein späteres verifiziertes Sonnet-Profil wird ausschließlich
über die Konstante gewählt. Die vorher im Auftrag genannte Sonnet-ID war nicht
bestätigt. Die AWS-Konsole muss Zugriff, exakte Profil-ID und EU-Zielregionen
für das gewählte Modell bestätigen. IAM braucht `bedrock:InvokeModel` und
`bedrock:InvokeModelWithResponseStream` auf dem Profil und den erforderlichen
Foundation-Model-Ressourcen. Vor jedem Modellwechsel Preise erneut prüfen.

Alle vier Preise sind Pflicht, endlich und positiv; fehlende Preise sperren
den Spike. Es gibt keine geschätzten Preiswerte im Runtime-Code. Bedrock Model
Invocation Logging muss abgeschaltet sein. Modell-/kontospezifische Retention,
EU-Regionen, Auftragsverarbeitung und spätere Datenschutzerklärung werden vor
öffentlichem Chat-Betrieb tatsächlich geprüft; die Implementierung beweist
keine AWS-Einstellungen.

## Prompt und Kanon

`systemprompt.md` enthält die im Auftrag eingefügte Vorlage ohne den führenden
HTML-Kommentar. Die Gesprächsschritte sind als Aufzählung formatiert, damit
keine numerischen Literale in der Vorlage stehen. Eine zusätzliche angehängte
Datei war in dieser Sitzung nicht auffindbar; Grundlage ist der vollständig
mitgelieferte Text. Technische Namen wie B2B bleiben erhalten.

`hu_chat_system_prompt()` liest Vorlage und Wissensdatei bei **jedem** Aufruf:

| Platzhalter | Quelle |
| --- | --- |
| `{{KONTAKT_EMAIL}}` | `hu_get_contact_email()` |
| `{{ANTWORTZEIT}}` | `hu_response_promise( 'value' )` |
| `{{PREISLEITER}}` | `hu_pricing_canon()`, Website-/Landingpage-/Übernahme-Getter, `hu_tracking_product_ladder()`, `hu_whitelabel_pricing_canon()` |
| `{{WISSEN}}` | `wp_upload_dir()['basedir']/hu-chat/knowledge.md` |

Die Ersetzung ist ein einzelner `strtr`-Durchgang. Platzhalter aus fremdem
Website-Text werden nicht erneut ausgewertet. Unbekannte/fehlende Platzhalter,
fehlende Wissensdatei und Überschreitung des Zeichendeckels scheitern geschlossen.
Der Builder und der Schutz des Upload-Verzeichnisses folgen erst nach dem Spike.
Der feste Zähltest verwendet den Beratungsprompt noch nicht.

`lint:canon` durchsucht bereits alle versionierten und neuen Dateien. Zusätzliche
Regeln verbieten numerische Literale und E-Mail-Adressen gezielt in der neuen
Prompt-Datei. `test:chat` prüft zudem jede Regel der Kanon-Sperrliste direkt gegen
die Vorlage und verifiziert die aktuelle Kanon-Ersetzung.

## Streaming-Abnahme auf der Live-Box: offen

Erster Durchlauf (Hallo-Test, von Hasim gemeldet): `transport=stream`, also
lieferte Bedrock tatsächlich einen Stream. Alle Events kamen aber in einem
Chunk bei 630 ms an, obwohl `ready` bei `server_ms` 0 gesendet wurde. Status
`buffering_or_timing_unresolved`.

Zweiter Durchlauf (Zähltest mit einmaligem Padding nach `ready`, von Hasim
gemeldet): Das Padding kam bei 91 ms an, alle folgenden kleinen Events
gesammelt bei 763 ms. Befund: Die Strecke puffert größenbasiert (~4 KB).
Daraus folgt der Sendetakt mit aufgefüllten Blöcken oben. Offen ist der
dritte Durchlauf: `text_arrivals_distinct` muss größer als 1 sein.

Nach Deploy und privater Konfiguration auf der Live-Box:

1. Als Admin die Testseite öffnen; DevTools Network mit geöffnetem Timing-Panel.
2. Je einen Durchlauf **ohne** und **mit** Padding starten, Response und
   Event-Ankunft kontrollieren.
3. **Protokoll herunterladen**: `chat-live-stream-plain.json` bzw.
   `chat-live-stream-padded.json` enthalten Status, Chunk-Größen, Server- und
   Browserzeiten sowie `headers`: `Content-Encoding`, `Content-Length`,
   `Transfer-Encoding`, `Server`, `Via`, `Vary`, `X-Cache` und alle übrigen
   `X-*`-Header, die der Browser sieht. Namen mit `nonce`, `token`, `auth`,
   `key`, `secret`, `session` oder `cookie` (etwa `X-WP-Nonce`) werden
   ausgelassen. `summary` nennt Chunk- und Textanzahl, verschiedene
   Ankunftszeiten der Text-Events, Protokoll (`next_hop_protocol`) und
   `encoded_body_size`/`decoded_body_size`; weichen beide ab, wurde unterwegs
   komprimiert. Zusätzlich einen bereinigten DevTools-Ausschnitt ohne
   Cookies/Authorization sichern.
4. Mindestens zwei zeitlich getrennte Ankünfte nachweisen: `ready` vor dem
   Bedrock-Abschluss und tatsächliche `text`-Events mit `done.transport=stream`.
   `incremental_delivery_observed` verlangt zusätzlich mindestens zwei
   verschiedene Ankunftszeiten der `text`-Events. Es ist ein Hinweis, keine
   automatische Freigabe.
   Fallback oder unklare Zeiten verlangen Untersuchung/Wiederholung.
5. Ergebnis und bereinigte Protokolle am zugehörigen Spike-PR festhalten.
   Erst nach bestandenem Gate mit dem übrigen Ausbau beginnen.
6. Nach dem Durchlauf `HU_CHAT_MODE=off` setzen: Route und Admin-Menü
   verschwinden. Öffentliche Seiten erhalten in diesem Stand bei jedem
   Modus weder Orb noch Chat-Assets.

## Weitere Phasen nach dem Gate

- Knowledge-Builder aus internen `llms.txt`-Links, Rendern über `home_url()`,
  Main-Extraktion, Blog-Kurzfassungen, Money-Page-Schutz, Zeichendeckel,
  Cron/Deploy-SHA-Invalidierung und geprüfter Zugriffsschutz.
- Öffentlicher zustandsloser Nachrichten-Contract, cachefester Nonce,
  Payload-/Tool-Validierung, Monatsbudget für reale Promptgrößen und Rate-Limit.
- Klickgeladener Orb und barrierefreies Panel via `frontend-system`:
  Sticky-CTA-Abstand, sicher gerendertes Markdown, interne Links, optionale
  Session-Historie erst nach Öffnen, Ausschlussrouten, inhaltsfreie Tracking-Hooks.
- Bestätigte Handover-Karte → WordPress-CRM → Aktivität → interne Brevo-Mail
  an den festen Kanon-Empfänger → Anfrage-Ereignis. Kein Besucher-Mail-Relay;
  Verlauf ausschließlich bei explizitem Opt-in.
- Datenschutz-**Entwurf** `#ki-chat`, einschließlich juristischer Prüfung
  von KI-Transparenz, AWS-Verarbeitung und erforderlichem Browser-Speicher.
- Reale Bedrock-Eval mit den beauftragten Fällen; mindestens 18/20, Prompt-
  Injection und Mail-Relay zwingend bestanden. Lighthouse/HAR, CRM/Mail,
  Tastatur/Screenreader und mobile Prüfung erst am vollständigen Preview.

## Tests und Primärquellen

`npm run test:chat` läuft ohne Netz und ist in `scripts/check.py` eingebunden:
offizieller SigV4-Vektor, alle Zweichunk-Teilungen des ersten Frames, CRC-
Beschädigungen, Truncation, Usage-Merge, Fallback, kein Retry nach Teilantwort,
Budgetgrenze/Abrechnung, Preview-/Admin-/Nonce-Gates auf Production,
fehlende Route und fehlendes Admin-Menü bei abgeschalteter/unvollständiger
Konfiguration, Rate-Limit, Payload und
Kanon-Ersetzung. Das binäre Fixture ist **synthetisch**, unabhängig mit Python
`struct`/`zlib` erstellt; ein echtes Bedrock-Recording ist nach dem Live-Box-
Durchlauf zu ergänzen.

- [AWS SigV4](https://docs.aws.amazon.com/IAM/latest/UserGuide/reference_sigv-create-signed-request.html)
- [AWS botocore get-vanilla-Vektor](https://github.com/boto/botocore/blob/develop/tests/unit/auth/aws4_testsuite/get-vanilla/get-vanilla.authz), Blob `551c0271d4a5ebf8aff478f5286eb12701a41182`; Request-Blob `0f7a9bfae3680836a2746b5bf7a33a6fa2b93c34`.
- [AWS Event-Stream-Spezifikation](https://smithy.io/2.0/aws/amazon-eventstream.html)
- [InvokeModelWithResponseStream](https://docs.aws.amazon.com/bedrock/latest/APIReference/API_runtime_InvokeModelWithResponseStream.html)
- [EU-Inferenzprofile](https://docs.aws.amazon.com/bedrock/latest/userguide/inference-profiles-support.html)
- [Haiku-Profil und Streaming-Unterstützung](https://docs.aws.amazon.com/bedrock/latest/userguide/model-card-anthropic-claude-haiku-4-5.html)
- [Bedrock-Preise](https://aws.amazon.com/bedrock/pricing/)
- [AWS Data Retention](https://docs.aws.amazon.com/bedrock/latest/userguide/data-retention.html)

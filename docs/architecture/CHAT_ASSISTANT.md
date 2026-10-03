# Chat-Assistent

Stand: 2026-10-03. **Phase eins: Transport-Spike vorbereitet, Staging-Abnahme offen.**
Branch `feat/chat-assistant`, Ausgangspunkt `9b248289` (aktuelles `main`,
einschließlich Glossar-Integration; Auftrag nannte den älteren Stand `ad3ac4a`).
Kein Merge und kein Production-Deploy ohne Hasims Freigabe.

## Aktueller Contract

Dieser Stand implementiert ausschließlich den ersten, verpflichtenden Spike
und die separat beauftragte Prompt-Vorlage samt Kanon-Ersetzung. Ein öffentlicher
Chat, Knowledge-Builder, Tool-Aufruf und CRM-Handover sind noch nicht aktiv.
Es gibt keinen Orb, Footer-Hook, Browser-Speicher oder öffentlichen Chat-Asset-
Request. Bestehende Formulare, CRM- und Brevo-Strecken ändern ihren Contract nicht.

Der tatsächliche Testpfad:

1. Ein eingeloggter Administrator öffnet **Werkzeuge → Chat: Streaming-Test**.
2. Erst der Klick startet einen First-Party-POST an `/wp-json/nexus/v1/chat`.
3. Das Payload muss exakt ein leeres JSON-Objekt sein. Nachrichten, Empfänger,
   Adressen und frei wählbare Modell- oder Hostnamen werden nicht akzeptiert.
4. Die Route verlangt `WP_ENVIRONMENT_TYPE=staging`, `HU_CHAT_MODE=preview`,
   vollständige Runtime-Konfiguration, `manage_options` und einen REST-Nonce.
   `off`, `live`, Production und anonyme Besucher sind gesperrt.
5. Vor Inferenz reserviert die Route atomar das konservative Kostenmaximum
   für Stream **und** einen möglichen Fallback im laufenden UTC-Monat. Danach
   folgen Stundenlimit (gesalzener IP-Hash, höchstens 30 Aufrufe) und Payload.
6. WordPress sendet nur die feste Bitte um das Wort „Hallo“ an Bedrock Frankfurt.
   Eigene SigV4-Signatur, cURL, TLS-Prüfung, keine Redirects und kein SDK.
7. Binäre AWS-Frames werden inkrementell einschließlich beider CRCs geprüft.
   Anthropic-Textdeltas werden als SSE `text` ausgegeben; Start und Transport
   erscheinen als `probe`, Abschluss als `done`, Fehler als `error`.
8. Ein Fallback auf `InvokeModel` erfolgt höchstens einmal und ausschließlich
   vor sichtbarem Text. Er gilt nicht als bestandener Streaming-Test.
9. Die endgültige Usage ersetzt bekannte Kostenreservierungen. Unterbrochene
   oder unklare Versuche behalten ihr Kostenmaximum. Zähler speichern nur
   Kosten und Tokenzahlen. Datenbankfehler verhindern neue Modellaufrufe.

`rest_pre_serve_request` liefert SSE ohne REST-JSON-Hülle. PHP-Output-Buffer
werden geschlossen; `Content-Type: text/event-stream`, `Cache-Control: no-store,
no-transform`, `X-Accel-Buffering: no` und `flush()` werden gesetzt. Ob nginx,
Varnish und die übrige Raidboxes-Strecke trotzdem puffern, muss Staging zeigen.
Ein Client-Disconnect verhindert die abschließende Budgetabrechnung nicht.

Fehlertexte nennen nur die kanonische Kontaktadresse. `error_log` und vorhandene
API-Telemetrie erhalten ausschließlich feste Kategorien, keine AWS-Fehlertexte,
Nachrichten, Zugangsdaten oder Payload-Schlüssel. Die allgemeine API-Telemetrie
überspringt `/chat` und `/chat/*`, da auch fremde JSON-Schlüssel PII enthalten können.

## Konfiguration und externe Voraussetzungen

Nur in der Staging-`wp-config.php` oder einer bestehenden privaten Runtime-
Konfiguration. Keine Zugangsdaten ins Repo, PR, Testprotokoll oder Chat kopieren.

| Konstante | Voraussetzung |
| --- | --- |
| `WP_ENVIRONMENT_TYPE` | `staging` |
| `HU_CHAT_MODE` | `preview`; Standard `off` |
| `HU_BEDROCK_ACCESS_KEY_ID` | IAM-Benutzer `iris`, lokal auf Raidboxes hinterlegt |
| `HU_BEDROCK_SECRET_ACCESS_KEY` | zugehöriger privater Schlüssel |
| `HU_BEDROCK_REGION` | ausschließlich `eu-central-1` |
| `HU_BEDROCK_MODEL_ID` | vorerst `eu.anthropic.claude-haiku-4-5-20251001-v1:0`, explizit setzen |
| `HU_CHAT_MONTHLY_BUDGET_USD` | Standard `20` |
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
Livegang tatsächlich geprüft; die Implementierung beweist keine AWS-Einstellungen.

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
Der feste Hallo-Test verwendet den Beratungsprompt noch nicht.

`lint:canon` durchsucht bereits alle versionierten und neuen Dateien. Zusätzliche
Regeln verbieten numerische Literale und E-Mail-Adressen gezielt in der neuen
Prompt-Datei. `test:chat` prüft zudem jede Regel der Kanon-Sperrliste direkt gegen
die Vorlage und verifiziert die aktuelle Kanon-Ersetzung.

## Staging-Abnahme: noch nicht durchgeführt

Es wurden keine AWS-Schlüssel und kein Raidboxes-Staging-Zugang angefordert
oder verwendet. Gemäß Hasims Ergänzung stoppt die Arbeit, bevor Zugangsdaten
nötig werden. Ohne diesen Zugang wird kein Streaming-Ergebnis behauptet.

Nach privater Konfiguration und Installation dieses Branches auf Staging:

1. Als Admin die Testseite öffnen; DevTools Network mit geöffnetem Timing-Panel.
2. Durchlauf per Klick starten, Response und Event-Ankunft kontrollieren.
3. **Protokoll herunterladen**: `chat-staging-stream.json` enthält nur Status,
   sichere Header, Chunk-Größen sowie Server- und Browserzeiten. Zusätzlich
   einen bereinigten DevTools-Ausschnitt ohne Cookies/Authorization sichern.
4. Mindestens zwei zeitlich getrennte Ankünfte nachweisen: `ready` vor dem
   Bedrock-Abschluss und tatsächliche `text`-Events mit `done.transport=stream`.
   `incremental_delivery_observed` ist ein Hinweis, keine automatische Freigabe.
   Fallback oder unklare Zeiten verlangen Untersuchung/Wiederholung.
5. Ergebnis und bereinigte Protokolle im **selben PR** festhalten. Erst nach
   bestandenem Gate mit dem übrigen Ausbau beginnen. Kein Merge.

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
Budgetgrenze/Abrechnung, Admin-/Staging-/Nonce-Gates, Rate-Limit, Payload und
Kanon-Ersetzung. Das binäre Fixture ist **synthetisch**, unabhängig mit Python
`struct`/`zlib` erstellt; ein echtes Bedrock-Recording ist nach Staging zu ergänzen.

- [AWS SigV4](https://docs.aws.amazon.com/IAM/latest/UserGuide/reference_sigv-create-signed-request.html)
- [AWS botocore get-vanilla-Vektor](https://github.com/boto/botocore/blob/develop/tests/unit/auth/aws4_testsuite/get-vanilla/get-vanilla.authz), Blob `551c0271d4a5ebf8aff478f5286eb12701a41182`; Request-Blob `0f7a9bfae3680836a2746b5bf7a33a6fa2b93c34`.
- [AWS Event-Stream-Spezifikation](https://smithy.io/2.0/aws/amazon-eventstream.html)
- [InvokeModelWithResponseStream](https://docs.aws.amazon.com/bedrock/latest/APIReference/API_runtime_InvokeModelWithResponseStream.html)
- [EU-Inferenzprofile](https://docs.aws.amazon.com/bedrock/latest/userguide/inference-profiles-support.html)
- [Haiku-Profil und Streaming-Unterstützung](https://docs.aws.amazon.com/bedrock/latest/userguide/model-card-anthropic-claude-haiku-4-5.html)
- [Bedrock-Preise](https://aws.amazon.com/bedrock/pricing/)
- [AWS Data Retention](https://docs.aws.amazon.com/bedrock/latest/userguide/data-retention.html)

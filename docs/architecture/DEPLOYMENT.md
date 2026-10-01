# Deployment

Stand: 2026-10-01.

Diese Doku beschreibt nur den repo-seitigen CI/CD-Vertrag fuer das WordPress-Child-Theme. Hostseitige Details auf Raidboxes oder anderen Hosts muessen ausserhalb des Repos bestaetigt werden.

## Workflows

- `.github/workflows/ci.yml`
  - prüft den Integrationsstand eines Pull Requests; ein manueller Lauf erzwingt die vollständige Suite, Pushes starten keine zweite Haupt-CI
  - nutzt `scripts/check.py` für dieselbe Prüfauswahl wie lokal `npm run check`; `-- --plan` zeigt die Auswahl vorab
  - hat keine Pfadfilter am Workflow: unbekannte Dateien und fehlende Vergleichshistorie führen zur vollständigen Prüfung
  - prüft Architektur, PHP-Syntax, Skill-Verträge, Prüfauswahl sowie Canon-, E3- und Textregeln immer
  - ergänzt bei Agenten-/Skill-Änderungen alle Skill-Suiten; Dokumentation, Entwürfe und Forschungsdaten benötigen keinen Browser, PHPStan oder Theme-Build
  - führt bei Theme-, Tooling-, Workflow- und unbekannten Änderungen alle Runtime-Prüfungen einschließlich Formulare, Navigation, Seitenanlage, Preisleiter, PHPStan und Theme-Build aus
  - richtet PHP und Composer aus `.toolchain.json` ausdrücklich ein; Node und Composer-Abhängigkeiten werden nur für die Runtime-Prüfung benötigt; zusätzlich prüft actionlint die Workflows
  - trennt Core, PHPStan und vier Browser-Shards; jeder Browser-Shard führt seinen Anteil der Formular- und Navigationstests mit einem Worker aus
  - nutzt vorinstalliertes Chrome/Chromium aus dem GitHub-Runner-Image; dessen genaue Version ist nicht gepinnt und wird im Log ausgegeben
  - behält den eindeutigen stabilen Pflichtcheck `validate`; er besteht nur, wenn Core, alle Browser-Shards und Analysis erfolgreich sind, auch übersprungene oder abgebrochene Partitionen zählen als Fehler
  - bündelt die German-Copy-Prüfung in `scripts/check.py` und alle drei CSS-Prüfungen im Theme-Build; separate Copy-/CSS-Workflows entfallen ohne Verlust ihrer Abdeckung
  - speichert vollständige Core-Prüflogs unter `.build/check-logs`; bei Fehlern werden diese sowie Browser-Traces und Screenshots als Artefakte mit sieben Tagen Aufbewahrung hochgeladen
- `.github/workflows/deploy.yml`
  - startet bei einem Push auf `main`, akzeptiert automatisch nur Revisionen aus einem gemergten PR nach `main` und deployt nur Runtime-/Tooling-Änderungen; reine Dokumentations- oder Skill-Änderungen lösen weder Deploy noch PHPStan-Cache-Refresh aus
  - wiederholt die vollständige PR-Suite nicht; PHP-Syntax und Build-Schutz bleiben Teil jedes Deploys
  - aktualisiert nach Runtime-Merges den PHPStan-Result-Cache auf dem Default-Branch parallel zum Deploy; dieser reine Beschleunigungsjob ist kein Release-Gate
  - kann weiterhin separat manuell per `workflow_dispatch` gestartet werden; ein manueller Lauf der **CI** erzwingt alle Prüfungen, startet aber keinen Deploy
  - baut das Theme erneut in ein Dist-Verzeichnis und überträgt nur das gebaute Child-Theme sowie die bestehende statische `llms.txt`
  - prüft SSH-Port und Deploy-Pfad vorab, testet die SSH-Verbindung und stellt sicher, dass der Zielpfad existiert oder angelegt werden kann
  - unterstützt beim manuellen Start einen `dry_run`, um `rsync` ohne Schreibzugriff zu prüfen
  - prüft automatische Releases innerhalb der bestehenden Produktionssperre gegen den aktuellen `main`: neuere Runtime-Änderungen überspringen einen veralteten Release; reine Dokumentations- und Skill-Nachfolger verhindern den ausstehenden Deploy nicht

Deploy-Läufe werden durch `production-deploy` serialisiert und laufende Deploys
nicht abgebrochen. Nur überholte PR-Prüfläufe werden abgebrochen. Der
Revisions-Guard lässt Dokumentationsnachfolger zu, verwirft aber veraltete
Runtime-Releases. Manuelle Deploys und Rollbacks behalten ihre explizit gewählte
Revision.

### GitHub-Merge-Regel

Das Ruleset für `main` muss PRs und den Pflichtcheck `validate` von GitHub
Actions verlangen, ohne Bypass. Zusätzlich gehört **Require branches to be up
to date before merging** zum Single-Gate-Vertrag: Nach anderen Main-Merges muss
der neue Integrationsstand erneut geprüft werden. Die Herkunft aus einem
gemergten PR allein belegt diese Aktualität nicht.

Diese Einstellung liegt außerhalb der Workflow-Dateien: `Settings → Rules →
Rulesets → Protect main → Require status checks to pass → Require branches to
be up to date before merging`. Ihr Live-Zustand muss separat geprüft werden;
ein Code-PR schaltet die Option nicht automatisch ein. Der Checkname bleibt
`validate`, damit keine Migration der bestehenden Pflichtcheck-Zuordnung nötig
ist.

Die Auswahlregeln stehen in `scripts/check.py`, ihre Regressionen in
`scripts/tests/test_check.py`. Negative Gegenproben in
`scripts/tests/test_check_ci_contract.py` entfernen einzelne CI-, Cache-,
Deploy- und CSS-Anforderungen und verlangen jeweils ein fehlgeschlagenes Gate.
Umbenennungen berücksichtigen alten und neuen Pfad;
lokale Prüfungen beziehen auch noch nicht versionierte Dateien ein. Vollständige
Logs liegen lokal im ausgegebenen temporären Verzeichnis, in CI im konfigurierten
Artefaktverzeichnis. Erfolgreiche Prüfungen
erscheinen kompakt. Tatsächliche Tokenersparnisse sind damit nicht gemessen.

Die lokale Einrichtung und der Doctor sind in
[`../development/TOOLCHAIN.md`](../development/TOOLCHAIN.md) beschrieben. Der
Deploy prüft vor dem Build ebenfalls die PHP-, Node- und npm-Versionen aus dem
Manifest. Bei manuellen Rollbacks auf ältere Commits ohne Manifest stammen die
Pins aus der ausführenden Workflow-Revision; der Theme-Commit bleibt unverändert.

## GitHub Secrets und Variables

Empfohlen fuer dieses Repo:

- Secret `SSH_PRIVATE_KEY`
  - privater SSH-Key fuer den Deploy-User
- Variable `SSH_HOST`
  - Hostname oder IP des Zielservers
  - aus Kompatibilitaetsgruenden funktioniert auch weiter `secrets.SSH_HOST`
- Variable `SSH_USER`
  - SSH-Benutzer mit Schreibrechten auf dem Theme-Zielpfad
  - aus Kompatibilitaetsgruenden funktioniert auch weiter `secrets.SSH_USER`
- Variable `SSH_PORT`
  - optional
  - Fallback: `22`
- Variable `DEPLOY_PATH`
  - optional, aber fuer saubere Host-Abstraktion ausdruecklich empfohlen
  - Fallback: `www/wp-content/themes/blocksy-child/`
  - dieser Fallback stammt aus dem bisherigen Repo-Setup und ist kein universeller Raidboxes- oder WordPress-Standard
- Secret `SSH_KNOWN_HOSTS`
  - optional, aber empfohlen
  - enthaelt den geprueften Host-Key fuer striktere Host-Authentifizierung
  - wenn nicht gesetzt, faellt der Workflow auf `ssh-keyscan` zur Laufzeit zurueck

Praktisch bedeutet das:

- `SSH_HOST`, `SSH_USER`, `SSH_PORT` und `DEPLOY_PATH` am einfachsten als Repository-Variables unter `Settings -> Secrets and variables -> Actions -> Variables`
- `SSH_PRIVATE_KEY` und optional `SSH_KNOWN_HOSTS` als Repository- oder Environment-Secrets
- das GitHub-Environment `production` kann weiter fuer spaetere Freigaben oder Reviewer-Regeln genutzt werden, ist fuer die vier Konfigurationsvariablen aber nicht zwingend noetig

## Hostseitige Anforderungen

Diese Punkte sind aus dem Repo nicht verlaesslich ableitbar und muessen manuell bestaetigt werden:

- der echte SSH-Host, der echte SSH-Port und der korrekte Zielpfad auf Raidboxes
- ob der Deploy-User `mkdir -p` und `rsync` im Zielpfad ausfuehren darf
- ob der Zielpfad bereits existiert oder bei Bedarf angelegt werden darf
- welcher Host-Key als vertrauenswuerdig in `SSH_KNOWN_HOSTS` hinterlegt werden soll
- ob es produktive Besonderheiten wie Chroot, abweichende Webroot-Strukturen oder alternative Staging-Pfade gibt

## Rsync-Sicherheitsrahmen

- Deployt wird nur das gebaute Child-Theme-Paket, nicht WordPress-Core, Uploads oder Datenbankinhalt.
- `DEPLOY_PATH` wird vor dem Deploy validiert und muss auf ein Verzeichnis enden, das `blocksy-child` heisst.
- `rsync` laeuft mit `--delete-delay`, damit veraltete Dateien nur innerhalb des validierten Theme-Zielpfads entfernt werden.
- Ein manueller Dry-Run zeigt geplante Datei-Aenderungen, ohne den Server zu veraendern.

## Staging-Vorbereitung

Das Setup ist bewusst schlank, aber staging-faehig vorbereitet:

- das Deploy-Job nutzt bereits das GitHub-Environment `production`
- Host, User, Port und Pfad sind ueber Variables/Secrets abstrahiert
- ein spaeteres `staging`-Environment kann denselben Workflow-Vertrag mit eigenen Werten wiederverwenden

Aktuell nimmt das Repo absichtlich keine ungesicherten Annahmen ueber eine vorhandene Raidboxes-Staging-Struktur vor.

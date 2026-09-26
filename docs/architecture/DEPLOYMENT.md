# Deployment

Stand: 2026-09-26.

Diese Doku beschreibt nur den repo-seitigen CI/CD-Vertrag fuer das WordPress-Child-Theme. Hostseitige Details auf Raidboxes oder anderen Hosts muessen ausserhalb des Repos bestaetigt werden.

## Workflows

- `.github/workflows/ci.yml`
  - läuft einmal je Pull Request und erneut für den zusammengeführten Stand auf `main`; Branch-Pushes starten keine zweite Haupt-CI
  - nutzt `scripts/check.py` für dieselbe Prüfauswahl wie lokal `npm run check`; `-- --plan` zeigt die Auswahl vorab
  - hat keine Pfadfilter am Workflow: unbekannte Dateien und fehlende Vergleichshistorie führen zur vollständigen Prüfung
  - prüft Architektur, PHP-Syntax, Skill-Verträge, Prüfauswahl sowie Canon-, E3- und Textregeln immer
  - ergänzt bei Agenten-/Skill-Änderungen alle Skill-Suiten; Dokumentation, Entwürfe und Forschungsdaten benötigen keinen Browser, PHPStan oder Theme-Build
  - führt bei Theme-, Tooling-, Workflow- und unbekannten Änderungen alle Runtime-Prüfungen einschließlich Formulare, Navigation, Seitenanlage, Preisleiter, PHPStan und Theme-Build aus
  - installiert Node 20, Browser und Composer-Abhängigkeiten nur für die vollständige Prüfung; zusätzlich prüft actionlint die Workflows
  - behält den stabilen Job `validate`; jede fehlgeschlagene ausgewählte Prüfung blockiert den automatischen Deploy
- `.github/workflows/deploy.yml`
  - wird automatisch nur nach erfolgreicher CI auf `main` und bei einer als Runtime/Tooling eingestuften Änderung aufgerufen; reine Dokumentations- oder Skill-Änderungen lösen keinen Deploy aus
  - kann weiterhin separat manuell per `workflow_dispatch` gestartet werden; ein manueller Lauf der **CI** erzwingt alle Prüfungen, startet aber keinen Deploy
  - baut das Theme erneut in ein Dist-Verzeichnis und überträgt nur das gebaute Child-Theme sowie die bestehende statische `llms.txt`
  - prüft SSH-Port und Deploy-Pfad vorab, testet die SSH-Verbindung und stellt sicher, dass der Zielpfad existiert oder angelegt werden kann
  - unterstützt beim manuellen Start einen `dry_run`, um `rsync` ohne Schreibzugriff zu prüfen
  - prüft automatische Releases innerhalb der bestehenden Produktionssperre gegen den aktuellen `main`: neuere Runtime-Änderungen überspringen einen veralteten Release; reine Dokumentations- und Skill-Nachfolger verhindern den ausstehenden Deploy nicht

Main-Läufe brechen sich nicht gegenseitig ab. Nur überholte PR-Läufe werden
abgebrochen. Dadurch kann ein schneller Dokumentations-Push keine noch laufende
Runtime-Prüfung verdrängen. Manuelle Deploys und Rollbacks behalten ihre explizit
gewählte Revision.

Die Auswahlregeln stehen in `scripts/check.py`, ihre Regressionen in
`scripts/tests/test_check.py`. Umbenennungen berücksichtigen alten und neuen Pfad;
lokale Prüfungen beziehen auch noch nicht versionierte Dateien ein. Vollständige
Logs liegen im ausgegebenen temporären Verzeichnis, erfolgreiche Prüfungen
erscheinen kompakt. Tatsächliche Tokenersparnisse sind damit nicht gemessen.

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

# Lokale Toolchain

Die exakten PHP-, Node-, npm- und Composer-Versionen stehen in
[`../../.toolchain.json`](../../.toolchain.json). Lokale Prüfungen, GitHub CI und
der Theme-Build beim Deploy verwenden diese Versionen. Die PHP-Version des
WordPress-Servers wird dadurch nicht geändert.

## Einrichten

Voraussetzungen: Python ≥ 3.8, Git, Bash, ripgrep, tar, unzip, C-Compiler, make
und pkg-config. Der PHP-Build benötigt OpenSSL, libxml2, libcurl, zlib und iconv.

- macOS: Xcode Command Line Tools (`xcode-select --install`) und Homebrew-Pakete
  `pkgconf`, `openssl@3`, `ripgrep`. Die übrigen Bibliotheken liefert das SDK.
  Auf dem bestehenden Intel-Mac mit macOS 11 sind diese Voraussetzungen vorhanden.
- Ubuntu: `build-essential pkg-config libssl-dev libxml2-dev libcurl4-openssl-dev
  zlib1g-dev python3 git ripgrep unzip xz-utils`. Für Browser-Systembibliotheken
  bei Bedarf anschließend `python3 scripts/toolchain.py exec npx playwright install-deps chromium`.

```bash
python3 scripts/toolchain.py setup
npm run doctor
npm run check -- --full
```

Das Setup lädt offizielle, mit SHA-256 geprüfte Archive. PHP wird beim ersten Mal
als CLI gebaut; das dauert einige Minuten. Werkzeuge und Download-Cache liegen
ignoriert unter `.build/toolchain/` im Hauptcheckout und werden von dessen
Git-Worktrees gemeinsam genutzt. Build-Fehler stehen in `php-build.log` dort.
System-PHP, globale Node-Installation und Shell-Startdateien bleiben unverändert.
Das Setup installiert `node_modules/` und `vendor/` aus den Lockfiles im jeweiligen
Checkout. Es veröffentlicht nichts. Unterstützt sind macOS und Linux mit x64/arm64;
lokal geprüft wurde macOS 11 auf Intel, CI prüft Ubuntu.

`npm run check` und `npm run doctor` wählen die Projektwerkzeuge automatisch aus.
Ohne globales npm funktionieren dieselben Einstiege direkt über Python:

```bash
python3 scripts/toolchain.py doctor
python3 scripts/check.py --full
python3 scripts/toolchain.py exec npm run lint:php
python3 scripts/toolchain.py exec composer analyse:php
```

Ein allein eingegebenes `php` oder `node` bleibt die globale Version. Einzelne
Werkzeugbefehle deshalb über `toolchain.py exec` starten. Der Doctor zeigt die
tatsächlich verwendeten Versionen, PHP-Erweiterungen, installierten Node-Pakete,
PHPStan, WordPress-Stubs und Browserpfade. Bei fehlender Umgebung startet der
gemeinsame Prüflauf keine Tests. `--plan` funktioniert weiterhin ohne PHP/Node.

Dokumentations- und Agentenprüfungen brauchen nur die Basistools inklusive der
festgelegten PHP-Version (`doctor --basic`); Node-Pakete, Browser und PHPStan
bleiben der vollständigen Prüfung vorbehalten. GitHub richtet PHP ausdrücklich
ein, statt die wechselnde Runner-Vorinstallation zu verwenden.

Die Browserkonfiguration bleibt plattformabhängig: Auf diesem Mac verwenden die
Tests vorhandenes Google Chrome, CI installiert das zur Playwright-Version
passende Chromium. Der Doctor nennt den Browserpfad; die vollständige Suite
prüft seine tatsächliche Nutzbarkeit. Gleiche CLI-Versionen bedeuten daher keine
identischen Betriebssysteme oder Browser-Binaries.

## Aktualisieren und Fehler beheben

Versionen und Prüfsummen gemeinsam in `.toolchain.json` aktualisieren, Setup
erneut ausführen und lokal sowie in CI vollständig prüfen. Die Node-Version
bestimmt das mitgelieferte npm. Versionsdaten und Prüfsummen stammen von
[PHP](https://www.php.net/releases/), [Node](https://nodejs.org/dist/) und
[Composer](https://getcomposer.org/download/).

- Prüfsummenfehler: Die im Fehler genannte Cache-Datei entfernen und Setup erneut
  starten. Ein Archiv mit falscher Prüfsumme wird nicht ausgeführt.
- Defekte bereits vorhandene Composer-Dateien: über
  `python3 scripts/toolchain.py exec composer reinstall phpstan/phpstan php-stubs/wordpress-stubs --no-interaction --prefer-dist`
  aus dem Lockfile wiederherstellen.
- Neue Worktrees: Setup dort starten; der Download-/Compiler-Schritt entfällt bei
  bereits vorhandenen Werkzeugen, die Abhängigkeiten werden separat installiert.

Ein manueller Deploy einer älteren Revision ohne Manifest verwendet die Pins
der ausführenden Workflow-Revision. Der gewählte Theme-Commit bleibt dabei erhalten.

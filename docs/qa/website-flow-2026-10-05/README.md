# Flow-Durchgang: Anfrage-Website, 05.10.2026

Die Screenshots stammen aus dem tatsächlichen PHP-Template mit den lokalen
CSS-/JS-Dateien und der gemeinsamen Navigation, isoliert vom Live-System.

- [Hero Desktop](hero-desktop.webp), [Hero Mobil](hero-mobil.webp)
- [Konfigurator Desktop](konfigurator-desktop.webp), [Konfigurator Mobil](konfigurator-mobil.webp)

Die Umfangswahl steht einmal im Konfigurator. Eine Auswahlspalte führt durch
Umfang, Texte und Gestaltung; Erweiterungen öffnen sich auf Wunsch. Preis und
Produktionszeit werden sofort berechnet und vollständig an die Anfrage übergeben.
Individuelles Design startet mit einem wiederverwendbaren Layout. Weitere
Layouts sind eine ausdrückliche Entscheidung; die Option nennt den aktuellen
Design-Aufpreis. Ein Dashboard bleibt zusätzlich nach Angebot.

Verifikation: gemeinsame Repository-Prüfung `npm run check`, Produktbrowser-
Tests in Chromium 154 und Firefox 155, schmale Breiten 320/360/390 px,
768 px sowie Desktop 1366/1440 px. 200 % Schriftgröße einschließlich geöffneter
Extras und Preisdetails: Produkt, Navigation und Footer ohne horizontalen
Überlauf. Tastaturfokus, native Details, Reduced Motion einschließlich laufender
Animationen, gültiges statisches Angebot ohne JavaScript. 40 Umfangsvarianten
und 96 vollständige Faktorkombinationen gegen den PHP-Preiskanon; isolierte
Übergabe an Kontakt, REST-Annahme, CRM-Datensatz und beide Mails.

Axe-core 4.11.1: keine automatisch erkannten WCAG-A/AA-Verstöße im Produktbereich
bei 320/390/768/1440 px mit ausgewählten Extras. Automatische Checks und die
Tastaturprüfung ersetzen keine vollständige WCAG-/Screenreader-Prüfung. WebKit,
reale Mobilgeräte und Nutzertests wurden nicht durchgeführt. Keine echten
Anfragen versendet und keine Conversion-Steigerung behauptet.

Firefox benötigt in dieser Containerumgebung deaktivierte Prozess-Sandboxen;
die Einstellung liegt nur in der lokalen Testkonfiguration, nicht im Theme.
Im No-JS-Test wird keine Font-Promise im deaktivierten Skriptkontext abgewartet.
Das Produktionsverhalten bleibt unverändert.

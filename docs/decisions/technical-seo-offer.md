# Paket 4: Technisches SEO für WordPress

Stand: 22.10.2026. Umsetzung als reviewbarer Repo-Stand; kein Produktionsnachweis
für die neue Leistungsseite.

## Anlass und Entscheidung

Das Funnel-Audit sah diesen Einstieg erst nach Leistungsabgrenzung vor.
Die Freigabe für Paket 4 umfasst den nächsten Ausbauschritt. Ein SEO-Festpreis,
eine feste Seitenzahl oder eine Lieferfrist wurden bisher nicht beschlossen.
Das Angebot wird daher nach vereinbartem Umfang kalkuliert. Der vorhandene
Übernahme-Check wird nicht als SEO-Paket wiederholt; alle Kanonpreise bleiben.

`/technisches-seo-wordpress/` ist die Leistungsseite für bestehende
WordPress-Websites. `/technisches-seo-performance-fundament/` bleibt der
informative Fachartikel. Die lokalen SEO-Queries behalten ihren bisherigen
Owner `/wordpress-agentur-hannover/`. Retired `/seo/`, `/core-web-vitals/`
und `/wordpress-seo-hannover/` bleiben 410; keine URL-Migration.

## Lieferumfang und Abnahme

Vor Beauftragung werden betroffene URLs/Seitentypen, Prüffragen, Zugänge,
Zeitraum und Preis festgelegt. Mögliche Prüffelder: Erreichbarkeit und
Indexierungsregeln, Vorlagen/Metadaten/interne Links/strukturierte Daten sowie
Ladezeit und mobile Darstellung. Kein pauschales Voll-Audit jeder URL.

Der beauftragte Befund nennt URL/Vorlage, beobachtetes Problem, Prüfmethode,
Priorität und empfohlene Korrektur. Umsetzung wird getrennt vereinbart und kann
auch durch die bestehende Entwicklung erfolgen. Für beauftragte Korrekturen
gehören dokumentierter Vorher-/Nachher-Zustand, wiederholbarer Test und offene
Punkte zur Abnahme. Indexierung, Ranking, Anfragen oder Anzeigenkosten sind
keine garantierten Abnahmewerte.

Texte, Content-Produktion, Backlinks, Anzeigen und vollständiges Tracking
gehören nicht automatisch dazu. Externe Lizenzen und Hosting separat benennen.
Keine kostenlose Diagnose als Teil der unverbindlichen Anfrage versprechen.

## Beleg und Grenzen

Eigene Website als ausdrücklich eigenes Arbeitsbeispiel: `/referenzen/`
besitzt einen CollectionPage-Knoten mit den sichtbaren Projektankern.
`scripts/tests/navigation-contract.php` prüft diese Zuordnung, den Kanon und
Navigationsziele. Die auf der Angebotsseite verlinkte Codefassung ist auf
Merge `dc4446fbdd1fb9cd91aad05383625ec51e39c57d` festgelegt.
Kein behaupteter Vorher-Defekt, Kundenbeleg oder gemessener Rankinggewinn.

Aktueller öffentlicher Abruf mit Firecrawl (`maxAge: 0`, gesamtes HTML) am
22.10.2026: Referenzseite und Fachartikel liefern HTTP 200, jeweils eine H1
und einen selbstreferenziellen Canonical. Die Referenzseite liefert
CollectionPage, der Fachartikel Article. Der Fachartikel läuft tatsächlich
über `page-seo-cornerstone.php`; sein SEO-Leistungslink führte bislang zum
allgemeinen Agenturpfad. Der Artikel bekommt den konkreten Leistungseinstieg;
sein Schluss und die Kostenantwort werden eingegrenzt. Das ist keine
vollständige redaktionelle Überarbeitung des langen Artikels.

Fachliche Grenzen nach aktuellen Primärquellen geprüft:

- [Google: Page Experience](https://developers.google.com/search/docs/appearance/page-experience): gute Core-Web-Vitals-Berichte garantieren keine Spitzenposition.
- [Google: noindex](https://developers.google.com/search/docs/crawling-indexing/block-indexing): Crawl-Sperre und Indexierungsregel sind verschiedene Vorgänge; ein Crawler muss die noindex-Regel lesen können.

## Anfrage und Messung

Bestehender Kontaktvertrag `type=project&focus=seo`, ohne neuen Fokuswert,
Endpunkt oder neue Messung. Produktbriefing aus einer PHP-Quelle für SSR und
Themenwechsel; Thema und Nachricht bleiben in CRM/Aktivität/beiden Mails.
Die Anfrage nennt Website, betroffene Seiten und das beobachtete Problem.
Keine Zugangsdaten im öffentlichen Formular. Antwortfrist nur nach dem
bestehenden Kanon für Anfragen, keine Fertigstellungsfrist des Befunds.

Hypothese: Die Trennung von Fachartikel, SEO-Projekt und allgemeiner Übernahme
macht passende Aufträge leichter verständlich. Nach Veröffentlichung
angenommene Anfragen mit SEO-Fokus und ihre Passung beobachten. Klicks allein
sind kein Geschäftsergebnis; keine Conversion-Wirkung oder Zielkundenprüfung
bereits nachgewiesen.

## Registry-Konsistenz

Der ergänzende Gesamt-Audit meldete schon auf dem unveränderten Stand vor
Paket 4 sieben Nähe-Hinweise, keine Konflikte für die neue SEO-Seite. Die
SST-Leitfaden-Zeile hatte ein unmaskiertes Semikolon im Quellenfeld; dadurch
landete die bereits dokumentierte Intent-Trennung in einer unlesbaren
Zusatzspalte. Das Quellenfeld wird repariert und die Trennung des unterstützenden
Technik-Leitfadens vom Anbieter-/Kaufintent ausdrücklich für die gemeldeten
SST-Varianten eingetragen. Query-Owner und Routen ändern sich dadurch nicht.

Die schon dokumentierte Trennung von Agentur- und direktem Freelancer-Intent
wird für `wordpress hannover` und `wordpress experte hannover` explizit ergänzt.
Die bisherige Owner-Zuordnung bleibt erhalten; kein neuer Ranking- oder
Suchvolumenbefund. Diese Klarstellungen halten den Gesamt-Audit prüfbar, ohne
bestehende URLs zu migrieren.

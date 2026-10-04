# Die Anfrage-Website

Grundprodukt freigegeben durch Haşim am 02.10.2026; Produktdurchgang und
Standard-Erweiterungen beauftragt am 04.10.2026. Diese Entscheidung ersetzt
den Website-Teil von `preise-website-landingpage.md` und die frühere offene
Design-/CRM-Kalkulation aus dem ersten Tagesrechner.

Erste Seite 1.490 € netto, jede weitere 290 €. Texte auf Wunsch je Seite,
responsive Basisgestaltung, technisches SEO, Formular mit gespeicherten
Anfragen, Bestätigungsmail, Danke-Seite und Rechtstext-Seiten sind enthalten.
Impressum/Datenschutz/Danke/404 zählen nicht als bezahlte Inhaltsseiten.
Rechtstexte werden geliefert, keine Rechtsberatung. Custom Code, Gutenberg/ACF,
GitHub und KI-Workflow mit Qualitätsprüfung; kein Pagebuilder, kein Website-Abo,
keine Pflichtwartung oder Pflichtlizenzen im Grundprodukt. Externe Kosten transparent.

## Feste Erweiterungen

| Erweiterung | Netto-Aufpreis | Produktionsbeitrag |
| --- | ---: | ---: |
| Screendesign, erstes unterschiedliches Layout | 690 € | 2 Werktage |
| Weiteres unterschiedliches Layout | 250 € | 0,5 Werktage |
| Conversion-Tracking Standard | 890 € | 1 Werktag |
| CRM Standard | 990 € | 3 Werktage |
| Daten-Dashboard | Nach Angebot | Zusätzlich nach Angebot |

Screendesign: eine Richtung in Figma, Desktop und Mobil je Layout, zwei
gebündelte Korrekturrunden. Wiederverwendung zählt einmal. Vier Seiten mit
Startseite und drei gleich aufgebauten Leistungsseiten benötigen zwei Layouts.
Neue Marke/Logo, weitere Richtungen und komplexe Interaktionen separat.
Preis ist eine eigene Produktkalkulation, kein behaupteter 2026-Marktdurchschnitt
und keine öffentliche 35-Prozent-Rabattbehauptung.

CRM Standard: ein Formular, vorhandenes HubSpot oder Bitrix24, bis zehn Felder,
ein Kontakt- oder Lead-Objekt; Mapping, Dublettenregel, Fehlerbehandlung, Tests,
Dokumentation. API/Berechtigungen/Tarif und Zielsystem vor Auftrag prüfen.
Salesforce und weitere Systeme, Migration, Automationen und zusätzliche
Objekte/Formulare separat. Externe CRM-Kosten nicht im Entwicklungspreis.

Tracking umfasst GA4/GTM/Consent Mode und eine Google-Ads-Conversion für die
Standard-Anfrage. Server-Side, Meta CAPI, Offline-Conversions und weitere Ziele
separat. Ein erforderlicher Consent-Dienst wird mit Kosten vorab benannt.
Dashboard ist nur eine auswählbare Angebotsleistung; kein Dashboard-Plugin oder
Analytics-Setup auf der eigenen Website wird in diesem Auftrag gebaut.

## Produktionszeit

`hu_website_calculator_rules()` besitzt Preise, Staffeln und Faktoren; PHP
und Browser rechnen aus derselben Konfiguration. Ein bis fünf Standardseiten:
zwei Werktage Umsetzung mit QA. Sechs bis zehn: drei. Relaunch ergänzt zwei.
Texterstellung: erster Beitrag eins, je Zusatzseite ein halber Tag. Fertige
Texte und Designs haben keine neue Erstellungsphase. Layoutzahl zunächst gleich
Seitenzahl (konservative Planung), manuell für Wiederverwendung reduzierbar;
bei weniger Seiten wird sie begrenzt. Alle Bruchteile erst in der Gesamtsumme
aufrunden. Beispiel zwei Seiten, neue Texte und zwei Layouts: 1,5 + 2,5 + 2 = 6.

Dies sind Planungsannahmen für Durchlaufzeit bis zum geprüften Abnahmestand,
keine gemessenen Personentage oder Liefergarantie. Vertrag, Umfang, Briefing,
Bilder, Rechtstexte und Zugänge stehen vor Produktionsstart; gelieferte Vorlagen
sind geprüft und freigegeben. Kundenfreigaben, Verfügbarkeit, Start und
Veröffentlichung werden separat vereinbart. Vorhandene Designs und CRM
brauchen Preflight. Dashboard-Auswahl markiert bekannte Zeit als Mindestwert
und bekannte Summe ausdrücklich ohne Dashboard, vollständiges Angebot vor Auftrag.

## Oberfläche und Datenpfad

Hero → Qualität kurz → drei Umfangsbeispiele → kompakter Konfigurator
→ aufklappbarer Lieferumfang und Erweiterungen → Belege → Qualitätsvergleich
→ Zeitbeiträge → FAQ → Anfrage. Die Beispiele (eine Seite Salon, drei Beratung,
fünf Sanitär/Heizung) zeigen mögliche Inhaltsseiten und lesen den Grundpreis
aus dem Kanon. Sie ändern nur die Seitenzahl; Textstatus, Design und Extras
bleiben erhalten. Manuell gewählte Layoutzahlen werden bei weniger Seiten
begrenzt, nicht heimlich durch ein neues Paket ersetzt. Eine eigene URL zählt
als Inhaltsseite, nicht jeder Bildschirm oder Abschnitt. Buchungssysteme und
Shops bleiben separat. Diese Route spricht Unternehmen und Selbstständige an,
auch wenn deren Zielkunden privat sind. Audit und wiederverwendbarer Prompt:
`docs/audits/anfrage-website-produkt-review-2026-10-04.md`.
Desktop: Seiten/Texte, Design/Extras und Preis/Zeit nebeneinander. Zielprüfung:
bei 1366 × 768 und 1440 × 900 Pixel sämtliche Auswahlfelder,
Layoutzahl und Anfrage-CTA sichtbar. Tablet/Mobil stapeln die Bereiche und
halten eine bearbeitbare Preis-/Zeitleiste bereit. Keine künstliche Verkleinerung
aller Inhalte auf eine einzige Handyansicht. Technische Details bleiben aufklappbar.

CTA-URLs → Kontakt-Hidden-Felder → vorhandenes REST → Server-Neuberechnung
→ CRM → beide Mails. `dashboard` erweitert den Vertrag kompatibel. Bestehendes
`screendesign=1` funktioniert weiter. Serverpreise, Tage und Regelversion sind
maßgeblich, gepostete Werte werden ignoriert. CRM hält Preis, Designpreis,
Layoutzahl, Vorbereitung mit halben Tagen und Dashboard-Auswahl fest.
Keine Änderung an Consent, Cache-Grenzen, Routing oder Deployment.
Landingpage und Monatskontingente unverändert. Hypothese: klarer Umfang und
sichtbare Gesamtkalkulation erhöhen Anfragen; prüfen nach acht Wochen oder
300 Aufrufen laut `docs/experimente/anfrage-website.md`.

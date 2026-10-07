# Die Anfrage-Website

Grundprodukt freigegeben durch Haşim am 02.10.2026; Produktdurchgang und
Standard-Erweiterungen beauftragt am 04.10.2026; Preisstrategie am 06.10.2026
auf einen gehobenen, aber zugänglichen Direktkundenpreis neu kalibriert. Diese Entscheidung ersetzt
den Website-Teil von `preise-website-landingpage.md` und die frühere offene
Design-/CRM-Kalkulation aus dem ersten Tagesrechner.

Preisstrategie am 06.10.2026 als Produktionsmodell präzisiert:
1.900 € netto für Grundsystem und erste Hauptseite. Weitere Seiten werden nach
Aufgabe kalkuliert: kurze Seite 300 €, Standardseite 400 €,
Leistungs-/Verkaufsseite 790 €. Fertige, freigegebene Texte werden eingepflegt;
Texterstellung ist optional und kostet 290 € für die erste Hauptseite, 50 € je
kurzer Seite, 150 € je Standardseite und 290 € je Leistungs-/Verkaufsseite.
Die Festpreise leiten sich intern aus produktivem Aufwand ab; der reguläre
Direktkunden-Stundensatz von 95 € netto und der Agenturfaktor von 70 € netto
werden auf der Produktseite nicht als Vergleichs- oder Rückrechnungsanker gezeigt.
Responsive Basisgestaltung, technisches SEO, Search Console, Formular mit gespeicherten
Anfragen, Bestätigungsmail, Danke-Seite, Basis-Daten-Cockpit und Rechtstext-Seiten sind enthalten.
Das Basis-Cockpit bündelt Search-Console-Sichtbarkeit, gespeicherte Anfragen und Top-Landingpages.
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
| Individuelles Daten-Dashboard | Nach Angebot | Zusätzlich nach Angebot |

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
Das Basis-Daten-Cockpit ist Teil des Grundprodukts und benötigt kein GA4. Ein individuelles
Daten-Dashboard für zusätzliche Quellen wie GA4, Ads, Meta oder CRM bleibt eine auswählbare
Angebotsleistung nach Abstimmung; dessen Zusatzaufwand ist nicht im Grundpreis enthalten.

## Produktionszeit

`hu_website_calculator_rules()` besitzt Preise und Zeitfaktoren; PHP und
Browser rechnen aus derselben Konfiguration. Das Grundsystem mit erster
Hauptseite trägt zwei geplante Werktage. Zusätzliche kurze und Standardseiten
ergänzen je einen halben Tag, Leistungs-/Verkaufsseiten einen ganzen Tag.
Relaunch ergänzt zwei, Conversion-Tracking einmal einen und Standard-CRM einmal
drei Werktage. Texterstellung ist optional und folgt dem gewählten Seitentyp:
Hauptseite ein halber Tag, kurze Seite ein Viertel, Standardseite ein Viertel
und Leistungs-/Verkaufsseite ein halber Tag. Fertige, freigegebene Texte haben
keine neue Texterstellungsphase.

Individuelles Screendesign startet mit zwei Werktagen für das erste
unterschiedliche Layout; jedes weitere unterschiedliche Layout ergänzt einen
halben Tag. Wiederverwendete Seitenlayouts werden nicht mehrfach berechnet.
Teil-Tage werden erst nach Addition aller Produktionsphasen auf volle
Gesamt-Werktage gerundet.

Dies sind Planungsannahmen für Durchlaufzeit bis zum geprüften Abnahmestand,
keine gemessenen Personentage oder Liefergarantie. Vertrag, Umfang, Briefing,
Bilder, Rechtstexte und Zugänge stehen vor Produktionsstart; gelieferte Vorlagen
sind geprüft und freigegeben. Kundenfreigaben, Verfügbarkeit, Start und
Veröffentlichung werden separat vereinbart. Vorhandene Designs und CRM
brauchen Preflight. Dashboard-Auswahl markiert bekannte Zeit als Mindestwert
und bekannte Summe ausdrücklich ohne Dashboard; vollständiges Angebot vor Auftrag.

## Oberfläche und Datenpfad

Hero → E3-Hauptbeleg → kompakter Konfigurator mit aufklappbarem
Lieferumfang und Erweiterungen → drei Qualitätsprinzipien → kompakter Ablauf
→ sechs Kauf-Fragen → Anfrage. Der frühere eigene Qualitätsblock, zwei
zusätzliche Referenzkarten, doppelte PageSpeed-Verweise und der interaktive
Sieben-Punkte-Vergleich sind seit der CRO-Kürzung vom 06.10.2026 entfernt.
Der Konfigurator startet mit Grundsystem und erster Hauptseite und unterscheidet
zusätzliche kurze Seiten, Standardseiten und Leistungs-/Verkaufsseiten. Die drei Vorlagen setzen eine nachvollziehbare
Mischung dieser Seitentypen; sie sind Abkürzungen, keine Pakete.

Texterstellung, Gestaltung und Projektmodule bleiben eigene Entscheidungen.
Tracking und CRM werden projektweit berechnet, nicht pro URL. Manuell gewählte
Layoutzahlen werden bei weniger Seiten begrenzt. Eine eigene URL zählt als
Inhaltsseite, nicht jeder Bildschirm oder Abschnitt. Buchungssysteme und Shops
bleiben separat. Diese Route spricht Unternehmen und Selbstständige an, auch
wenn deren Zielkunden privat sind. Audit und wiederverwendbarer Prompt:
`docs/audits/anfrage-website-produkt-review-2026-10-04.md`.
Desktop zeigt Auswahl und Preis-/Zeitzusammenfassung nebeneinander; Tablet und
Mobil stapeln die Bereiche. Technische Details und Erweiterungen bleiben
aufklappbar. Der Abschluss übernimmt denselben Scope und denselben bekannten
Preis aus dem Rechner.

CTA-URLs → Kontakt-Hidden-Felder → vorhandenes REST → Server-Neuberechnung
→ CRM → beide Mails. `dashboard` erweitert den Vertrag kompatibel. Bestehendes
`screendesign=1` funktioniert weiter. Serverpreise, Tage und Regelversion sind
maßgeblich, gepostete Werte werden ignoriert. CRM hält Preis, Designpreis,
Layoutzahl, Vorbereitung mit halben Tagen und Dashboard-Auswahl fest.
Keine Änderung an Consent, Cache-Grenzen, Routing oder Deployment.
Landingpage und Monatskontingente unverändert. Hypothese: klarer Umfang und
sichtbare Gesamtkalkulation erhöhen Anfragen; prüfen nach acht Wochen oder
300 Aufrufen laut `docs/experimente/anfrage-website.md`.

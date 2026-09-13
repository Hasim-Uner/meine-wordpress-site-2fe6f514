# Startseite und Freelancer-Angebot zusammenführen

Entscheidung: 2026-09-13, ausdrücklich vom Betreiber beauftragt. Umsetzung im PR; Veröffentlichung und Liveprüfung separat.

## Ziel und Abgrenzung

Die Startseite `/` übernimmt Inhalt und Suchintention der bisherigen `/wordpress-freelancer-hannover/`. Haşim Üner positioniert sich dort als WordPress Freelancer für direkte Unternehmensprojekte, mit Tracking und Conversion als verbundener Kompetenz. White-Label und Solar/Wärmepumpe behalten ihre eigenen Angebote und Anfragewege. Die Agentur-Route bleibt für ihren getrennten lokalen Suchintent bestehen.

Die Entscheidung ersetzt die separate Freelancer-URL aus `wordpress-freelancer-hannover.md`. Sie beruht auf der ausdrücklich gewünschten Konsolidierung und dem Vergleich beider Templates: Die Homepage war Verteiler, die Freelancer-Seite enthielt Leistungen, Preise, Referenzen und Anfrage. Es liegen für diese Migration keine aktuellen GSC-/CRM-Auswertungen vor. Ein Rankinggewinn wird deshalb nicht behauptet.

## Inhalt und Anfrage

- Homepage im vorhandenen Gutachten-Design: Freelancer-Hero, vier Leistungen mit Preisrahmen, öffentliche WordPress-Arbeiten, Zusammenarbeit, abgegrenzter Solar-Fall, FAQ und Projektanfrage.
- WordPress-Referenzen aus dem Reference-Canon stehen vor dem Solar-Fall. Fallzahlen gelten ausschließlich für das beschriebene Gesamtsystem.
- Feste undatierte Lighthouse-Zahlen entfallen; PageSpeed-Messung und öffentliche Code-/CI-Nachweise bleiben verlinkt.
- Angebots-CTAs führen zu `/kontakt/?type=project&focus=…`: `relaunch`, `conversion`, `tracking`, `implementation_scope`. Der bestehende Kontaktablauf übernimmt die Auswahl und überspringt die Themenauswahl. Kein zweites Formular und keine neue Analytics-Laufzeit auf der Homepage.
- Bestehende Homepage-Actions bleiben erhalten, soweit ihre Handlung weiter existiert. `home_hero_to_routes` entfällt; neu sind `home_hero_to_offers`, `home_offer_relaunch`, `home_offer_conversion`, `home_offer_tracking`, `home_offer_implementation_scope`, `home_reference_open`, `home_proof_github_history`, `home_proof_github_ci`. `home_about` und `home_more_results` sind wieder sichtbar. Alte Freelancer-Formular-Actions entfallen mit dem Formular; bestehender Kontakt-Submit bleibt der Erfolgsnachweis.

## Migrationsvertrag

- Permanenter 301 von `/wordpress-freelancer-hannover/` nach `/`, auch wenn kein veröffentlichter WordPress-Datensatz mehr unter dem alten Slug existiert.
- Alte Seiten-ID-URLs und Template-Zuweisungen werden ebenfalls vor WordPress Canonical Redirect behandelt. WordPress-Routingparameter werden entfernt, Kampagnenparameter bleiben erhalten.
- Die alten Leistungs- und Inhaltsanker existieren am entsprechenden Inhalt der Homepage weiter. Der Redirect setzt kein neues Fragment.
- Navigation, interne redaktionelle Links, Commercial Route Map und `llms.txt` zeigen direkt auf `/`.
- Homepage-Titel, Beschreibung und Service-/WebPage-Schema beschreiben denselben Freelancer-Einstieg. Die alte Route wird aus der nativen Sitemap ausgeschlossen.
- Das alte PHP-Template bleibt als kleiner Kompatibilitätsrenderer für vorhandene Template-Zuweisungen erhalten; seine eigenständige Landingpage, CSS und Formular-JavaScript entfallen. Kein automatisches Löschen von WordPress-Datenbankinhalten.

## Prüfung nach Veröffentlichung

HTTP-Status und Location mit/ohne Slash, Kampagnenparametern und alter Seiten-ID prüfen; `/` muss 200 und selbstkanonisch sein. Sitemap ohne alte Route. Desktop/Mobil, Tastatur, Angebote → vorausgewähltes Kontaktformular → bestätigter CRM-Eingang prüfen. Erst nach dieser Kontrolle die frühere WordPress-Seite bei Bedarf als historischen Entwurf ablegen; der Redirect bleibt aktiv.

GSC- und Anfrage-Baseline vor Veröffentlichung sichern, sofern verfügbar. Nach 2, 6 und 12 Wochen beide URLs gemeinsam sowie die getrennte Agentur-Route betrachten: Impressionen, Klicks, Suchanfragen, qualifizierte Projektanfragen. Klickverschiebung von alter URL zu `/` nicht als Trafficverlust fehlinterpretieren. Vorherige Repo-Version bleibt der technische Rückweg.

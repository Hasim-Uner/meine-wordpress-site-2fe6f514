# Startseite: Positionierung und drei Angebotswege

Stand: 24.09.2026. Status: historischer Konzeptentwurf mit Copy-Vorschlägen, kein aktueller Umsetzungsauftrag.

> Archivhinweis vom 02.10.2026: Der Entwurf bezieht sich auf den damaligen Prüfstand. Die Startseite wurde inzwischen umgebaut; die folgenden Texte, Preise, Metadaten, Befunde und Dateipfade sind historische Angaben. Maßgeblich sind [Brand und Copy](../standards/BRAND_AND_COPY.md), [CTA-Routing](../architecture/CONVERSION_ROUTING.md) und [Runtime-Status](../architecture/LIVE_STATUS.md). Die Startseite bleibt gemäß [Ersteinschätzungsversuch](../experimente/ersteinschaetzung.md) bis 20.11.2026 eingefroren; die vorgeschlagene Umordnung ist keine freigegebene Änderung.

## Entscheidung

Die stärkste Richtung ist eine Startseite, die zuerst Person und Arbeitsansatz erklärt, danach drei Vorhaben unterscheidet und anschließend gemeinsame Belege liefert. Die bestehende Leitidee „Von der Website bis zur Anfrage“ bleibt. Die größte Änderung betrifft die Auswahl: Besucher wählen ihr Vorhaben, bevor sie sich mit einzelnen Leistungen beschäftigen.

Die gemeinsame Positionierung lautet:

> Haşim Üner verbindet WordPress-Entwicklung mit Performance-Marketing, Tracking und Conversion. Er entwickelt Websites und die technischen Wege, über die aus Interesse eine Anfrage beim zuständigen Team wird.

Das ist eine überprüfbare Beschreibung des Leistungszusammenhangs. Eine behauptete Alleinstellung gegenüber sämtlichen Freelancern oder Agenturen wäre nicht belegt. Die Differenzierung wird durch Projektumfang, direkte Umsetzung und dokumentierte Übergabe glaubwürdig.

Eine wichtige Einschränkung: `/` besitzt bereits den WordPress-Freelancer-Intent. `/wordpress-freelancer-hannover/` leitet live mit 301 auf `/` weiter. Deshalb bleibt ein kompakter Abschnitt zu direkten WordPress-Projekten auf der Startseite. Der spezialisierte nächste Schritt dieses Weges ist der vorhandene Kontaktablauf. Für diese Optimierung wird keine zweite Freelancer-Landingpage eröffnet.

## Prüfgrundlage und Grenzen

Geprüft wurden die Live-Startseite einschließlich ausgeliefertem HTML, Metadaten, JSON-LD und CTA-Zielen, die Live-Einstiege für Solar/Wärmepumpe und White-Label, die Profilseite, die Fallstudie, robots.txt, llms.txt und die alte Freelancer-Weiterleitung. Hinzu kommen das aktive Template, relevante CSS-Regeln und die kanonischen Verträge im Repository.

Die Startseite ist ein **Hardcoded template mit Canon-/Helper-gesteuerten Teilen**: Struktur und Haupttexte liegen in `blocksy-child/front-page.php`; Preise, Antwortzusagen und Fallzahlen kommen aus ihren Kanon-Dateien.

Es wurden keine Analytics-, Search-Console- oder CRM-Daten ausgewertet, keine Formulare abgeschickt und keine Desktop-/Mobil-Screenshots erstellt. Aussagen zur Besucherführung sind begründete Hypothesen. Mobile Anordnung wurde aus Markup/CSS beurteilt und muss vor Veröffentlichung im Browser geprüft werden. Rankings, tatsächliche Indexierung, Core Web Vitals und Conversion-Steigerungen sind damit nicht nachgewiesen.

`docs/standards/VOICE_OF_CUSTOMER.md` enthält noch kein Kundenrohmaterial. Die vorgeschlagenen Texte sind redaktionelle Angebotscopy; sie werden nicht als Kundenstimmen oder Forschungsergebnis ausgegeben. Wettbewerberrecherche ist für die festgestellten Strukturprobleme nicht erforderlich.

## Was bleiben sollte

- Die öffentliche Rolle WordPress Freelancer und der lokale Bezug zu Pattensen bei Hannover.
- Die Leitidee „Von der Website bis zur Anfrage“.
- Das echte Porträt und die direkte Zusammenarbeit mit der ausführenden Person.
- Öffentliche Arbeiten mit konkret benanntem Beitrag; das eigene Projekt bleibt als solches gekennzeichnet.
- Der anonymisierte Solar-Fall, einschließlich Zeitraum und Einordnung der Zahlen.
- Testumgebung, klare Abnahme, dokumentierte Übergabe und Konten beim Auftraggeber.
- Die getrennten Spezialrouten und die vorhandenen Anfrageformulare.
- Der aktuelle SEO-Title und grundsätzlich die bestehende Meta-Description.

Es gibt keinen sachlichen Grund, das gesamte Design, alle Texte oder die technische Infrastruktur neu zu bauen.

## Priorisierte Befunde

| Priorität | Stelle | Befund und Konsequenz | Änderung |
| --- | --- | --- | --- |
| P1 | Hero und `#angebote` | Die sichtbaren drei Kernbereiche sind WordPress, Anfragestrecken und Tracking. Das beschreibt Fähigkeiten, beantwortet aber nicht die Auswahl zwischen Direktauftrag, Agentur und Energie-Anfragesystem. | Eine gemeinsame Auswahl nach Vorhaben direkt an den Hero setzen; die Fähigkeiten anschließend als verbindenden Arbeitsansatz erklären. |
| P1 | Hero-CTAs | Ersteinschätzung und Projektanfrage bekommen mehr Gewicht als die beiden Spezialwege. Besucher sollen eine Kontaktart wählen, bevor die passende Zusammenarbeit klar ist. | Im Zielbild führt der Hero zu den drei Wegen. Ersteinschätzung innerhalb des direkten Website-Wegs anbieten. |
| P1 | `#anfrage` | Der Abschluss bietet zwei Kontaktarten für Direktinteressenten; Agentur- und Energiepfad erscheinen dort nicht gleichwertig. | Die drei Einstiege am Ende knapp wiederholen; E-Mail als unaufdringliche Alternative. |
| P1 | Hero-Proof und `#systemprojekt` | Der stärkste wirtschaftliche Beleg ist ein Solar-Fall. Oben heißt er nur B2B-Fall, darunter nimmt seine Systemdarstellung viel Raum ein. Das kann die Gesamtspezialisierung enger erscheinen lassen als beabsichtigt. | Branche schon in der kurzen Belegzeile nennen. Im Hauptbeleg vor allem Beitrag, Ergebnis und Grenzen zeigen; Systemdetails in der Fallstudie belassen. |
| P2 | Hero-Subline | Die technische Verbindung ist vorhanden, der Nutzen des Marketingverständnisses bleibt eher implizit. | Performance-Marketing einmal nennen und anhand von Angebot, Zielseite, Messung und Übergabe konkretisieren. |
| P2 | Grafik-Caption | „Jede Anfrage … mit ihrer Quelle“ kann trotz Beispielkennzeichnung als lückenlose Attribution gelesen werden. | „Beispiel: Eine Anfrage wird mit den verfügbaren Angaben an das CRM übergeben.“ |
| P2 | Tracking-Karte | Die Aussage, eine Anfrage werde erst im CRM messbar, ist fachlich zu absolut. | Messung des Eingangs und spätere Bewertung bis zum Vertriebsstatus unterscheiden. |
| P2 | FAQ | Kostenlose Ersteinschätzung und kostenpflichtiger Übernahme-Check werden nicht unmittelbar gegeneinander abgegrenzt. | Öffentliche Sichtprüfung von technischer Prüfung mit Zugängen unterscheiden. |
| P2 | Ausfall-FAQ | Die Zusage einer Mitteilung am selben Tag ist auch bei unerwartetem Ausfall absolut formuliert. | Mit tatsächlicher Vorsorge argumentieren: Konten, Code, Dokumentation und nachvollziehbarer Stand. |

Kein technischer P0-Blocker wurde in diesem Prüfumfang festgestellt. Ob die derzeitigen CTAs schlechter konvertieren, lässt sich ohne Nutzungsdaten nicht behaupten.

## Empfohlene Reihenfolge

| Position | Aufgabe | Umfang |
| --- | --- | --- |
| 1. Hero | Person, Rolle, Verbindung zwischen Entwicklung und Marketing | Bestehende H1, kurze Subline, Porträt, ein Orientierungsschritt |
| 2. Drei Einstiege | Passendes Vorhaben erkennen | Drei kompakte, gleich gut auffindbare Einstiege mit jeweils einem Ziel |
| 3. Gemeinsamer Arbeitsansatz | Verstehen, warum die Kompetenzen zusammengehören | Ein kurzer Absatz und drei konkrete Stationen |
| 4. Ausgewählte Arbeiten | Behauptungen prüfen | Öffentliche WordPress-Arbeit, abgegrenzter Solar-Fall, nachvollziehbare Übergabe |
| 5. Direkte WordPress-Projekte | Suchintention und offene Fragen direkter Auftraggeber bedienen | Kurzer Leistungsabschnitt mit Preisrahmen und direkter Projektanfrage |
| 6. Zusammenarbeit und Fragen | Aufwand, Verantwortung und Übergabe klären | Drei Schritte, wenige relevante FAQ |
| 7. Abschluss | Gewählten Weg wiederfinden | Drei kurze Links; keine erneute vollständige Angebotsdarstellung |

Ein kompakter Hinweis auf die Belege kann bereits beim Hero stehen. Er darf die drei Einstiege nicht weit nach unten schieben. Ziel für einen kurzen Orientierungstest: Nach ungefähr 30 Sekunden können Besucher Person, Arbeitsansatz und passenden nächsten Schritt benennen. Das ist ein Abnahmekriterium, keine ausgelesene Verweildauer.

Die Reihenfolge der Einstiege ist **WordPress-Projekt → Agentur → Solar/Wärmepumpe**. Sie folgt dem bestehenden Suchintent der Startseite und dem klar abgegrenzten Branchenangebot. Alle drei Wege erhalten vergleichbare Lesbarkeit. Eine andere Reihenfolge sollte aus tatsächlicher Besucherzusammensetzung oder einer bewussten Geschäftsentscheidung folgen.

## Einsetzbare Copy

### Hero

**Überzeile**

Haşim Üner · Pattensen bei Hannover · DACH-weit

**H1 – bestehende Leitidee beibehalten**

WordPress Freelancer Hannover.\
Von der Website bis zur Anfrage.

**Subline**

Ich verbinde WordPress-Entwicklung mit Performance-Marketing, Tracking und Conversion. Damit Ihre Website Ihr Angebot verständlich macht und Anfragen über einen klaren, messbaren Weg bei Ihrem Team ankommen.

**Persönliche Zeile am Porträt**

Direkt mit mir – von der Konzeption bis zur technischen Übergabe.

**Hero-CTA im Zielbild**

Passenden Einstieg finden → `#wege`

Der Hero-Button führt innerhalb der Seite unmittelbar zur Auswahl. Stehen die drei Einstiege auf Desktop bereits direkt im sichtbaren Hero-Bereich, übernehmen ihre Links die Navigation; dann entfällt der zusätzliche Sprungbutton. Die globale direkte Projektanfrage im Header bleibt ein schneller Weg für Besucher mit konkretem Vorhaben.

**Kompakte Belegzeile, falls Platz vorhanden**

Öffentliche WordPress-Arbeiten · Dokumentierter Solar-Fall · Einblick in Code und Übergabe

Die drei Teile verweisen auf die passenden Belegabschnitte. Einen Preis für Website-Projekte nicht als allgemeine Preisschwelle für alle drei Wege in den Hero stellen.

### Die drei Einstiege

**H2**

Was möchten Sie umsetzen?

**Einleitung**

Ein Projekt für Ihr Unternehmen, Unterstützung für Ihre Agentur oder eigene Anfragen für Ihren Solar- und Wärmepumpenbetrieb: Hier finden Sie den passenden Einstieg.

**1. Für Unternehmen**

**WordPress-Projekt umsetzen**

Sie möchten Ihre Website neu aufbauen, relaunchen oder gezielt verbessern. Ich übernehme die technische Umsetzung und verbinde sie bei Bedarf mit Landingpages, Tracking und Ihrem Anfrageprozess.

CTA: **WordPress-Projekt beschreiben →**

Ziel: `/kontakt/?type=project&focus=relaunch`

Die bestehende Fokuskennung umfasst im aktuellen Angebot Neubau, Relaunch und Weiterentwicklung. Vor einem Umbau prüfen, dass auch die sichtbare Bezeichnung im Kontaktablauf alle drei Fälle verständlich einschließt.

**2. Für Agenturen**

**Technische Umsetzung unter Ihrem Namen**

Sie führen das Kundenprojekt und brauchen Unterstützung bei WordPress, Landingpages oder Tracking. Ich ergänze Ihr Team mit direkter Abstimmung und dokumentierter Übergabe. Die Kundenbeziehung bleibt bei Ihnen.

CTA: **White-Label-Zusammenarbeit ansehen →**

Ziel: `/whitelabel-retainer/`

**3. Für Solar- und Wärmepumpenbetriebe**

**Eigene Anfragen aufbauen**

Sie möchten Interessenten über einen eigenen Anfrageweg gewinnen und vor dem ersten Gespräch qualifizieren. Ich verbinde Zielseiten, Marketing, Tracking und die Übergabe an Ihren Vertrieb. Der Marktcheck klärt den Einstieg.

CTA: **Anfragesystem für Ihren Betrieb ansehen →**

Ziel: `/solar-waermepumpen-leadgenerierung/`

**Abgrenzung unter der Auswahl**

Für einen Website-Relaunch ohne Aufbau einer eigenen Anfragestrecke wählen Sie das WordPress-Projekt – auch als Solar- oder Wärmepumpenbetrieb.

Diese Zeile verhindert, dass eine Branche automatisch mit einem größeren Systemprojekt gleichgesetzt wird. Agenturen mit Energie-Kunden bleiben im Agenturweg, wenn Haşim als Umsetzungspartner der Agentur beauftragt wird.

### Gemeinsamer Arbeitsansatz

**H2**

Eine Website muss auch nach dem Klick funktionieren.

**Text**

Ich betrachte die Website im Zusammenhang mit dem Marketing und dem Weg der Anfrage. So lassen sich Angebot, Zielseite, Formular und Übergabe gemeinsam planen. Sie beauftragen die Teile, die für Ihr Vorhaben gebraucht werden.

**Drei kurze Stationen**

- **Angebot verstehen:** Inhalte und Nutzerführung machen Leistung und nächsten Schritt klar.
- **Anfrage erfassen:** Formulare fragen die Informationen ab, mit denen Ihr Team weiterarbeiten kann.
- **Wirkung nachvollziehen:** Tracking und bei Bedarf CRM-Anbindung zeigen, welche verfügbaren Signale zur Anfrage und ihrer weiteren Bearbeitung vorliegen.

Technisches SEO und Performance gehören zur Website-Umsetzung. Die Begriffe müssen weder im Hero mehrfach wiederholt noch als zusätzliche Geschäftspfade dargestellt werden.

### Proof

**H2**

Arbeiten, an denen Sie meinen Beitrag prüfen können.

**Einleitung**

Öffentliche Websites zeigen Gestaltung und Struktur. Ein dokumentierter Solar-Fall zeigt die Verbindung zu Marketing und Vertrieb. Der Einblick in die Übergabe macht die technische Arbeitsweise nachvollziehbar.

**Öffentliche WordPress-Arbeiten**

Die vorhandenen Referenzen mit ihrer jeweiligen Beitragsbeschreibung beibehalten. Zwei Beispiele reichen in der ersten Ansicht; weitere Arbeiten sind über `/ergebnisse/` erreichbar. Das eigene redaktionelle Projekt bleibt ausdrücklich als eigenes Projekt gekennzeichnet. Keine nachträglichen Erfolgszahlen oder Testimonials ergänzen.

**Solar-Fall**

**Vom Lead-Einkauf zur eigenen Anfragestrecke**

Für einen mittelständischen PV-Installationsbetrieb wurden Website, Vorqualifizierung, Tracking und CRM mit dem Vertriebsprozess verbunden.

Im dokumentierten Fall: 150 € pro gekaufter Anfrage vorher, 22 € pro eigener Anfrage nachher; 1.750+ qualifizierte Anfragen in sechs Monaten.

Die Zahlen beschreiben diesen Fall einschließlich Kampagnen und Vertrieb. Sie sind keine Prognose für andere Projekte.

CTA: **Fall, Vorgehen und Einordnung ansehen →** `/case-study-solar-leadgenerierung/`

Beim Einbau ausschließlich die bestehenden Canon-Helper für Fallzahlen und Zeitraum verwenden. Die Werte sind aus dem Repository und der eigenen Fallbeschreibung belegt; eine unabhängige Prüfung der ursprünglichen Kunden-/Werbekontodaten war nicht Teil dieses Audits. Vor einer stärkeren wirtschaftlichen Zuspitzung die Kostenbasis des CPL-Vergleichs ausdrücklich benennen: Aus den geprüften Angaben lässt sich nicht eindeutig ablesen, welche Aufbau-, Betreuungs- und Medienkosten eingerechnet sind. Die Kennzahlen deshalb nicht als Nachweis einer entsprechenden Senkung sämtlicher Vertriebskosten verwenden.

**Zusammenarbeit und Übergabe**

**Sie können mit dem Ergebnis weiterarbeiten.**

Der vereinbarte Stand wird auf einer Testumgebung geprüft und mit Code, Zugängen und Dokumentation übergeben. Bei Tracking- oder CRM-Aufgaben gehört die Prüfung der vereinbarten Mess- und Anfragewege dazu.

Das vorhandene Übergabemuster bleibt als Beispiel gekennzeichnet. Es ist keine Agenturreferenz. GitHub und automatische Prüfungen liefern technische Einblicke; sie belegen keine geschäftlichen Kundenergebnisse.

### Kompakter Abschnitt für direkte WordPress-Projekte

**H2**

WordPress neu aufbauen oder gezielt weiterentwickeln.

**Text**

Ich entwickle neue Websites, begleite Relaunches und arbeite an bestehenden WordPress-Seiten weiter. Struktur, technisches SEO, Ladeverhalten und Nutzerführung werden passend zur Aufgabe geprüft. Tragfähige Inhalte, URLs und Systeme bleiben erhalten.

Website-Projekte ab 3.400 € netto. Umfang, Preis und Zeitrahmen werden vor dem Start vereinbart.

CTA: **WordPress-Projekt beschreiben →** `/kontakt/?type=project&focus=relaunch`

Der Preis beschreibt Website-Projekte; kleinere Aufgaben erhalten ihre eigene Umfangsklärung. Die Preisangabe kommt bei Umsetzung weiterhin aus `hu_freelancer_website_price()`.

Die bisherigen Anker für WordPress, Landingpages und Tracking behalten in diesem kompakten Fachabschnitt sinnvolle Ziele. Für reine Tracking- und Landingpage-Aufgaben bleiben passende Kontextlinks erhalten:

- Tracking einrichten oder prüfen lassen → `/server-side-tracking-b2b/`
- Landingpage oder Anfrageweg besprechen → `/kontakt/?type=project&focus=conversion`

Dadurch entsteht kein vierter gleichrangiger Besucherweg. Direkte Fachaufträge bleiben Teil der direkten Zusammenarbeit.

**Option für unklare Website-Vorhaben**

**Sie möchten Ihre bestehende Website erst einordnen lassen?**

Schicken Sie mir die URL und Ihr Ziel. Sie erhalten drei konkrete Befunde oder eine ehrliche Rückmeldung, wenn die Seite nicht zu meiner Arbeit passt.

CTA: **Kostenlose Ersteinschätzung →** `/kontakt/?focus=ersteinschaetzung`

Diese Option bleibt an den bestehenden Experiment-Schalter gebunden; die kanonischen Texte und die Antwortzusage werden beim Einbau verwendet.

### Zusammenarbeit

Die bestehende Abfolge bleibt: Klären → Bauen → Prüfen und übergeben. Die ausführliche zweite Erklärung derselben Übergabe kann verkürzt werden.

**H2**

Klarer Umfang. Direkte Abstimmung. Dokumentierte Übergabe.

**Text**

Vor dem Start legen wir fest, was umgesetzt wird und woran Sie das Ergebnis abnehmen. Sie sehen den Stand auf einer Testumgebung und stimmen technische Fragen direkt mit mir ab. Nach der Abnahme erhalten Sie die vereinbarten Zugänge und die Dokumentation.

**Sinnvolle FAQ-Ergänzung**

**Was unterscheidet Ersteinschätzung und Übernahme-Check?**

Die kostenlose Ersteinschätzung betrachtet Ihre öffentlich erreichbare Website und Ihr Ziel. Für die technische Übernahme prüfe ich zusätzlich Aufbau, Plugins, Updates, Backups und Zugänge. Dafür gibt es den gesonderten Übernahme-Check mit schriftlichem Befund.

Der bestehende Preis und die Anrechnungsregel des Übernahme-Checks können direkt daneben aus dem Canon stehen. Keine zusätzlichen Zusagen erfinden.

**Präzisere Ausfall-Antwort**

Code, Konten und Zugänge liegen bei Ihnen. Die vereinbarte Dokumentation hält Aufbau und Projektstand fest, damit andere Fachkräfte die Weiterarbeit nachvollziehen können.

### Abschluss

**H2**

Welches Vorhaben möchten Sie besprechen?

**Text**

Wählen Sie den passenden Einstieg. Dort sehen Sie, wie es weitergeht und welche Angaben ich für die erste Einordnung brauche.

- **WordPress-Projekt beschreiben →** `/kontakt/?type=project&focus=relaunch`
- **White-Label-Zusammenarbeit ansehen →** `/whitelabel-retainer/`
- **Anfragesystem für Ihren Betrieb ansehen →** `/solar-waermepumpen-leadgenerierung/`

Darunter als Textlink: Direkt per E-Mail an `kontakt@hasimuener.de`.

## CTA- und Weiterleitungslogik

| Situation | Nächster Schritt | Weiterer Verlauf |
| --- | --- | --- |
| Neuer Besucher, noch keine Zuordnung | `#wege` | Eines von drei Vorhaben wählen |
| Konkretes WordPress-Projekt | Kontakt mit `type=project&focus=relaunch` | Vorhaben beschreiben → fachliche Einordnung → Umfang und Angebot |
| Direkter Fachauftrag für Tracking / Conversion | Fachseite oder fokussierte Projektanfrage | Tracking-/Conversion-Fokus im bestehenden Intake |
| Bestehende Website, Problem noch unklar | Kostenlose Ersteinschätzung | Schriftlicher Befund; weiterer Auftrag nur bei Bedarf |
| Agentur sucht technische Unterstützung | White-Label-Seite | Aufgabe beschreiben → erstes abgegrenztes Projekt; regelmäßige Zusammenarbeit bei Bedarf |
| Betrieb sucht eigene Solar-/Wärmepumpen-Anfragen | Branchen-Money-Page | Eignung und Angebot verstehen → Marktcheck → passende nächste Stufe |
| Besucher sucht Belege | Ergebnis-Hub / konkrete Fallbeschreibung | Zurück in den passenden Geschäftspfad |

„Ansehen“ führt zu einer erklärenden Seite; „beschreiben“ führt zum Intake. Der Erstbesucher wird beim Solar-CTA noch nicht direkt zum Formular gesprungen. Bestehende Marktcheck-Links innerhalb des Energie-Clusters bleiben erhalten. Keine Weiterleitung sämtlicher Startseitenbesucher in einen gemeinsamen Diagnoseprozess.

## Darstellung und mobile Priorität

Die vorhandene Typografie, Farbwelt und zurückhaltende Bildsprache können bleiben. Die Einstiege brauchen dieselbe Struktur: Zielgruppe, Vorhaben, kurzer Satz zum Umfang, eindeutiger Link. Keine Paketpreise oder drei konkurrierende Erfolgsversprechen innerhalb dieser Auswahl.

Auf Desktop sind drei kompakte Spalten möglich. Mobil funktionieren drei untereinander stehende, kurze Zeilen oder Karten; Überschrift und Link müssen sofort sichtbar sein. Keine Tabs, kein Karussell und keine hoverabhängigen Zusatzinformationen.

Die bisherige Hero-Grafik rutscht laut CSS unterhalb von 1120 px in die zweite Rasterzeile. Bei der neuen Struktur soll die Auswahl davor stehen. Das Porträt bleibt sichtbar, aber Grafik und Animation dürfen die Auswahl nicht verzögern. Die Grafik muss auch statisch verständlich bleiben; die vorhandene Unterstützung reduzierter Bewegung erhalten.

Vor Veröffentlichung bei 390, 768 und 1440 px prüfen: Reihenfolge, sichtbare Links, Umbrüche, Tastaturfokus, Kontrast und verständliche Bedienung ohne Animation. Diese Browserprüfungen sind für das Konzept noch offen.

## SEO, interne Links und Entity-Signale

### Tatsächlich verifiziert

- Startseite: HTTP 200, Canonical `https://hasimuener.de/`, Robots-Meta `index, follow, max-image-preview:large`.
- Title: `WordPress Freelancer Hannover | Haşim Üner`.
- Description: WordPress Freelancer aus der Region Hannover; Websites, Relaunches, technisches SEO, Tracking und White-Label sind bereits enthalten.
- Eine H1 mit der Freelancer-Rolle und der bestehenden Leitidee.
- Sechs parsebare JSON-LD-Blöcke: Organization/LocalBusiness, WebSite, Person, WebPage, Service, FAQPage. Das ist kein vollständiger Rich-Results-Validierungstest.
- Die Person hat auf Startseite und Profilseite dieselbe ID: `https://hasimuener.de/hasim-uener/#person`.
- `/wordpress-freelancer-hannover/` liefert 301 auf die Startseite.
- robots.txt und llms.txt sind erreichbar. robots.txt erlaubt die Startseite für allgemeine Suchcrawler und die ausdrücklich aufgeführten Such-/Abrufbots. Ein abweichendes Verhalten von WAF oder CDN je Bot wurde nicht getestet.
- Die Solar-Fallstudie liefert HTTP 200 und **`noindex, follow`**.

### Empfehlung

Die Startseite behält die Query-Zuständigkeit für WordPress Freelancer und WordPress Freelancer Hannover. Solar-Leadgenerierung, White-Label und Server-Side Tracking werden durch kurze relevante Links unterstützt und auf den bestehenden Spezialseiten vertieft. Die Startseite braucht dafür keine umfangreichen Keywordabschnitte.

Title und Description bleiben zunächst unverändert. Das neue Hero ordnet Performance-Marketing sichtbar in die Umsetzung ein; die globale Rolle bleibt WordPress Freelancer. Den H1-Text bei einem Umbau mit echten Wortabständen zwischen den Spans ausgeben; die aktuelle Textextraktion zieht „Hannover.Von“ zusammen.

Die vorhandenen internen Ziele `#angebote`, `#angebot-website`, `#angebot-funnel`, `#angebot-tracking`, `#wege`, `#anfrage` und `#kontakt` erhalten oder mit sinnvoller Zielzuordnung weiterführen. Frühere interne Links sollen weiterhin in den erwarteten Zusammenhang führen.

Auf der Homepage nur die entscheidungsrelevanten Links priorisieren: die drei Einstiege, Ergebnisse, Über Haşim und passende Fachverweise. Portalvergleiche, Kostenrechner und lange Energie-Glossare bleiben im Branchencluster.

Die Entity-Grundlage ist vorhanden. Beim nächsten Pflegepass `sameAs` prüfen: Organization verweist auf `github.com/Hasim-Uner`, Person auf `github.com/Hasim-hannover`. Zwei Profile sind nicht automatisch ein Fehler; Zugehörigkeit und gegebenenfalls Umbenennung verifizieren. Standort Pattensen bei Hannover, Namensvarianten und die Person-/Unternehmens-IDs konsistent halten. Zusätzliche Schema-Blöcke sind hierfür nicht nötig.

### AI-Search / GEO

Die wichtigste Verbesserung ist eine kurze, sichtbar lesbare Verbindung von Person, Tätigkeit, Zielgruppen und konkreten Arbeitsbelegen. Die neuen Einstiege machen die drei Angebotsbeziehungen im normalen Seiteninhalt verständlich. Reale Beispiele mit klarem Beitrag, Zeitraum und Grenzen sind hilfreicher als weitere unbelegte Kompetenzbegriffe.

Google nennt für AI Overviews und AI Mode keine zusätzlichen technischen Anforderungen und kein spezielles AI-Schema. Es empfiehlt unter anderem indexierbare Inhalte, interne Links und strukturierte Daten, die den sichtbaren Inhalt wiedergeben. Das ist eine Aussage zu Googles Suchfunktionen, keine Zusicherung für sämtliche KI-Systeme. [Quelle: Google Search Central](https://developers.google.com/search/docs/appearance/ai-features)

Der vorhandene `noindex`-Status der Fallstudie ist eine konkrete Grenze: Sie kann so nicht als regulär indexierte Quellseite für Googles AI-Suchfunktionen dienen. Ihre menschenlesbare Belegfunktion bleibt bestehen. Die Sperre ist im Repository dokumentiert und wird hier nicht als Fehler behandelt. Freigegebene Zusammenfassungen im Ergebnisse-Hub können die öffentliche Einordnung tragen; die Sperre nicht ohne Klärung ihres Zwecks aufheben.

Die vorhandene llms.txt bei einer späteren Umsetzung sachlich nachführen. Von dieser Datei allein lässt sich keine Verbesserung von Rankings oder KI-Zitaten ableiten. Sichtbare Inhalte und reale Verlinkung haben Vorrang.

## Umsetzung und Messung

### Reihenfolge

1. Die vorhandenen zwei Speziallinks und den direkten WordPress-Weg zu einer sichtbaren Auswahl zusammenführen. Bestehende Technik und Formulare weiterverwenden.
2. Hero-Subline, Proof-Einordnung und absolute Formulierungen gezielt präzisieren.
3. Erst dann die langen Leistungs-/Systemdarstellungen komprimieren und den Abschluss auf dieselben drei Wege ausrichten.

Das Zielbild verändert die Priorität des Hero-CTAs. Laut `docs/experimente/ersteinschaetzung.md` läuft seit **23.09.2026** ein achtwöchiger Versuch mit der Ersteinschätzung als Hauptaktion. Am Audit-Tag liegt damit kein belastbares Experimentergebnis vor. Die vorgeschlagene Umordnung darf bei einem Release nicht als isolierter Sieger dieses Versuchs dargestellt werden. Variantenstand und Änderungsdatum dokumentieren; den bisherigen Zeitraum separat auswerten. Die Ersteinschätzung selbst bleibt innerhalb des direkten Website-Wegs nutzbar.

### Repository-Zuständigkeiten bei späterer Umsetzung

- `blocksy-child/front-page.php`: Hero, Auswahl, gekürzte Abschnitte, FAQ und Abschluss.
- `blocksy-child/assets/css/startseite.css` und `startseite-rest-v1.css`: Reihenfolge und Darstellung mit vorhandenen Gestaltungsregeln.
- `inc/canon/*`: vorhandene Helper für Preise, Fallzahlen, Zusagen und Experiment verwenden; das Konzept ändert diese Werte nicht.
- `docs/standards/BRAND_AND_COPY.md`, `docs/architecture/CONVERSION_ROUTING.md`, `docs/architecture/LIVE_STATUS.md`: geänderte Homepage-Rolle und CTA-Priorität bei Umsetzung nachführen.
- `docs/experimente/ersteinschaetzung.md`: Einfluss des Variantenwechsels dokumentieren.
- `llms.txt`: neue Seitenbeschreibung nachführen. `query-ownership.csv` braucht für dieses Zielbild keine Änderung.

Kein manueller WordPress-Textimport ist für die hardcodierte Startseite nötig. Live-Formularzustellung, Messung, tatsächliche Suchdaten und die Identität externer Profile sind operative Prüfungen.

### Erfolg prüfen

Zuerst kurze Orientierungstests mit Personen aus den drei Zielgruppen: Wer ist Haşim? Wofür ist er zuständig? Was unterscheidet seinen Arbeitsumfang? Welcher Einstieg passt und was erwarten Sie dahinter? Auch Grenzfälle testen: Solar-Relaunch, Agentur mit Solar-Kunde, reines Tracking-Projekt.

Anschließend pro Weg nachvollziehen: Auswahl des Einstiegs, passende Anfrage und tatsächlich geeignete Aufgabe. Ein Klick zur Spezialseite ist eine erfolgreiche Orientierung, aber noch keine qualifizierte Anfrage. Mehr kostenlose Befunde allein sind ebenfalls kein Geschäftserfolg.

Vorhandene `data-track-*`-Hooks bei gleicher Handlung beibehalten. Ein neuer Sprung zur Auswahl ist Navigation und darf nicht unter einer bisherigen Kontaktaktion gezählt werden. Für entfernte oder anders verwendete Kontaktflächen die historische Auswertung trennen. Keine neuen Analytics-Skripte sind Teil dieses Konzepts; eine aktive Ereigniserfassung wird nicht aus vorhandenen HTML-Hooks abgeleitet.

Release-Prüfung bei Code-Umsetzung: schmale PHP-/Copy-Prüfung, anschließend `npm run lint:architecture`, `npm run lint:php` und die relevanten Routing-Smokes. Dazu Browserprüfung und eine autorisierte Prüfung des passenden Intake-Verlaufs. Diese Release-Checks wurden für das reine Konzept nicht als bestanden ausgegeben.

## Quellen

- [Live-Startseite](https://hasimuener.de/)
- [White-Label-Einstieg](https://hasimuener.de/whitelabel-retainer/)
- [Solar-/Wärmepumpen-Einstieg](https://hasimuener.de/solar-waermepumpen-leadgenerierung/)
- [Profilseite](https://hasimuener.de/hasim-uener/)
- [Solar-Fallstudie](https://hasimuener.de/case-study-solar-leadgenerierung/)
- [Google: AI features and your website](https://developers.google.com/search/docs/appearance/ai-features)
- Repository: `front-page.php`, aktive Startseiten-CSS-Dateien, `BRAND_AND_COPY.md`, `CONVERSION_ROUTING.md`, `LIVE_STATUS.md`, `query-ownership.csv`, `llms.txt`, `inc/canon/e3-proof-canon.php`, `inc/canon/messaging-canon.php`, `docs/experimente/ersteinschaetzung.md`.

# Anfrage-Website: zweiter Produktdurchgang

Stand: 04.10.2026. Ausgangspunkt: zusammengeführter Produktkonfigurator aus
PR #537, `0dd7fd59cd4fbfa6fa3ea9cd51b6229dc0826a38`.
Route: `/wordpress-website-erstellen-lassen/`. Hardcoded Template; Preise und
Zeitregeln zentral, FAQ sichtbar und im Schema aus demselben Getter.

## Ziel und Prüfgrundlage

Ein neuer Besucher soll verstehen, ob das Produkt zu seinem Vorhaben passt,
welche Inhalte und Funktionen er erhält, was der bekannte Umfang kostet und
was nach der Anfrage passiert. Die Qualitätsbelege müssen zu diesem Umfang
passen. Prüfung über Template, CSS/JS, Preisregeln, Kontaktübergabe, SEO-Owner,
Meta-/Schema-Owner und den vollständigen vorhandenen Browsertestpfad.

Keine aktuelle Conversion-Rate, GSC-Auswertung oder reale Bearbeitungszeit
ausgewertet. Aussagen über zusätzliche Anfragen, SEO-Erfolg und Produktivität
sind daher keine gemessenen Ergebnisse dieses Durchgangs. Der öffentliche
Suchabruf zeigte noch eine ältere Seitenfassung; er belegt den neuen
Live-Deploy nicht. Neue Darstellung über CI prüfen und nach Merge live ansehen.

## Befunde und Umsetzung

| Priorität | Bereich | Befund im Ausgangsstand | Entscheidung und Nutzen | Prüfung |
|---|---|---|---|---|
| P1 | Käuferorientierung | Seitenzahlen und Preise erklären noch nicht, welcher Umfang sinnvoll ist. | Drei Beispiele: Salon mit einer Seite, Beratung mit drei, Sanitär/Heizung mit fünf. Genaue Inhaltsstruktur und Grenzen; Klick übernimmt nur Seitenzahl. | Kanonpreise, Strukturzahl, Extras bleiben erhalten, alle Anfrageziele stimmen überein. |
| P1 | Zielgruppe | Ein pauschales B2B-Etikett kann Unternehmen mit Privatkunden unnötig ausschließen. | Diese Produktseite spricht Unternehmen und Selbstständige an. Beispiele sind Orientierung, keine neuen Branchenfunnel. | Hero und Beispiele verständlich ohne internes Positionierungswissen; Query-/CTA-Owner unverändert. |
| P1 | Copy und Vertrauen | „Ihr nächster Auftrag“ ist als Leistungsergebnis stärker als der nachweisbare Umfang. | „Ihr Angebot zeigen. Anfragen einfach machen.“ beschreibt die Bauaufgabe. Technische Qualität und Freigaben bleiben konkret. | Keine Ergebnisgarantie, keine erfundenen Kundenstimmen oder Wettbewerberbehauptungen. |
| P1 | Belege | Langer Qualitätsvergleich kommt vor den tatsächlichen Arbeitsbelegen; PV-Zahlen können wie Grundprodukt-Ergebnisse wirken. | Belege vor Vergleich. PV-Ergebnis ausdrücklich auf gesamte Maßnahme begrenzt. Eigene Bauweise zuerst sichtbar. | Reihenfolge, Vergleichszustand, freigegebene Kennzahlen weiterhin aus Kanon. |
| P1 | Design-Auswahl | „Basisdesign“ erklärt die Abgrenzung zum Figma-Entwurf erst weiter unten. | „Basisgestaltung“ mit bewährten Layouts, Farben und Typografie direkt in der Auswahl. | Gesamte Auswahl weiterhin in kompakter Desktopansicht; kein Preis-/Zeitwechsel. |
| P1 | Angebotsgrenzen | Ein Salonbeispiel kann eine enthaltene Online-Buchung suggerieren. Abschnitte könnten als kostenpflichtige Seiten verstanden werden. | Online-Terminbuchung/Shop separat, eine eigene Inhalts-URL zählt als Seite. Kontakt darf Abschnitt der Startseite sein. | Alle Beispiel-Seitenpläne zählen exakt; Impressum/Datenschutz/Danke/404 zählen nicht. |
| P2 | SEO und Lesefluss | Die Meta-Description benennt den neuen Inklusivumfang kaum. | Konkrete Description mit Custom Code, Texten, technischer SEO und sichtbarer Kalkulation. H1/Title/Route behalten Suchintention. | Ein H1, Meta aus zentralem Owner; Inhalt serverseitig, Beispiele auch ohne JS lesbar. |
| P2 | Anfrage | Abschluss sagt nicht ausdrücklich, dass Auswahl übernommen wird; direkte Kontaktangaben sind nicht anklickbar. | Übergabe und unverbindlicher Ablauf erklären, Mail/Telefon mit Canon-Links erreichbar machen. | Anfrage-URLs, Kontakt/CRM/Mails weiterhin vorhandener Vertrag; echte Mail-/Tel-Links. |

## Was bestehen bleibt

- Ein Produkt, eine primäre Anfrage. Keine neue Branchenroute und keine neue
  SEO-Query-Zuständigkeit. Kein Elementor-/Wix-Vergleich ohne konkrete Evidenz.
- Preis-/Zeitkanon, Texte inklusive, rechtliche Seiten mit gelieferten Texten,
  keine Rechtsprüfung. Vorhandene Designs/CRM vor Auftrag prüfen.
- Desktop-Konfigurator mit allen Optionen, Layoutzahl, Preis, Zeit und CTA im
  Bildschirm; mobil ehrliche Stapelung und bearbeitbare Zusammenfassung.
- Native geschlossene Details, progressive Verbesserung, Tastatur,
  Reduced Motion, keine Browser-Speicherung oder zusätzliche Analytics.
- Bestehender REST-/CRM-/Mailvertrag und serverseitige Neuberechnung.

## SEO-Grenzen und Quellen

SEO meint hier auffindbare und verständliche Inhalte sowie saubere technische
Grundlagen. Separate Leistungsseiten sind sinnvoll, wenn sie unterschiedliche
Inhalte/Suchanliegen beantworten; mehr Seiten allein sind kein Rankingvorteil.
Keine Ranking-, Indexierungs- oder Rich-Result-Garantie. Das bestehende zentrale
Service-/FAQ-Schema wird nicht allein wegen der UI als Shop-Produkt umgebaut.
FAQ bleibt eine Antwortfläche für Kunden. Google hat FAQ-Rich-Results seit
Mai 2026 eingestellt; FAQ-Markup ist kein sichtbarer Google-SEO-Vorteil.

Primärquellen, geprüft am 04.10.2026:
- [Google SEO Starter Guide](https://developers.google.com/search/docs/fundamentals/seo-starter-guide)
- [Google Dokumentationsänderungen: Mai/Juni 2026](https://developers.google.com/search/updates)

Live-Follow-up nach Merge: veröffentlichte Route/Status, Title, Description,
Canonical und `og:url`, ein H1, zentrale Schema-Ausgabe und Sitemap kontrollieren.
Keine Reindexierung oder WordPress-Adminänderung für diesen PR ausgeführt.

## Wiederverwendbarer Audit-Prompt

> Prüfe die gesamte Landingpage `/wordpress-website-erstellen-lassen/` als
> Produktarchitekt und als neuer Käufer. Ziel ist eine verständliche,
> hochwertige, wirtschaftlich tragfähige Dienstleistung mit konfigurierbarem
> Umfang. Nicht mit Geschmacksurteilen oder einem pauschalen „Weltklasse“-Rating
> beginnen: jede Empfehlung braucht eine konkrete Beobachtung, eine Käuferfrage
> und eine überprüfbare Verbesserung.
>
> 1. **Kontext und Wahrheit:** Lies AGENTS, den passenden lokalen Kontext und
>    den zuständigen primären Skill. Unterscheide Template-, Editor- und
>    Canon-Inhalte. Lade dann nur Route, CSS/JS, Preise/Zeit, FAQ, CTA-Routing,
>    Query-Owner und berührte Intake-Verträge. Bestimme die geprüfte Revision.
>    Trenne Repo, tatsächlich ausgelieferte Seite und Such-/Cache-Snapshot.
>    Keine erfundenen Messdaten, Marktpreise, Kundensätze oder Garantien.
> 2. **Käuferpass ohne Vorwissen:** Kann jemand in einem kurzen Scan erkennen:
>    Ist das für meinen Betrieb? Was erhalte ich? Wie wähle ich den Umfang?
>    Was ist enthalten und extra? Einmalpreis oder Abo? Was muss ich liefern?
>    Wann kann begonnen werden? Wem gehören Code/Zugänge? Was geschieht nach
>    der Anfrage? Prüfe Salon/Einzelleistung, Beratung und mehrere Leistungen.
>    Verwechsele B2B-Auftraggeber nicht mit dessen eigenen B2B-/B2C-Zielkunden.
> 3. **Produkt- und Margenpass:** Prüfe echte Inhaltsseiten gegen Abschnitte
>    und Layouts, wiederverwendetes Design, vorhandene Texte/Vorlagen, neue
>    Erstellung, Korrekturen, Relaunch, QA und CRM-Voraussetzungen. Rechner
>    und Server müssen dieselbe bekannte Summe liefern. Projektweite Extras
>    nur einmal; halbe Tage erst am Ende runden. Dashboard separat sichtbar,
>    vollständiges Angebot vor Auftrag. Terminplanung von Kundenfreigaben,
>    Verfügbarkeit und Livegang trennen. Planungsfaktoren nicht als gemessene
>    Produktivität ausgeben. Ändere Preisregeln nur bei fachlichem Anlass.
> 4. **UX/UI-Pass:** Gehe Hero → Orientierung → Auswahl → Receipt → Details
>    → Beleg → Einwände → Anfrage durch. Prüfe Hierarchie, Lesemaß, Kontrast,
>    Fokus, Trefferflächen, ausgewählte/abgewählte Zustände, Fehlannahmen und
>    wechselnde Gesamtsummen. Desktop 1366×768 und 1440×900: alle
>    Konfiguratoroptionen inklusive Layoutzahl, Preis, Zeit und Anfrage-CTA
>    sichtbar. Mobile 320/390, Tablet 768: kein Überlauf, verständliche
>    Stapelung, Sticky verdeckt keine zentrale Handlung. Keine winzige Schrift
>    als Fit-Trick. Öffne alle Details, prüfe Tastatur/Reduced Motion/No-JS.
> 5. **Copy- und Belegpass:** Jede tragende Überschrift muss die Kundenfrage
>    beantworten. Leistungen in Nutzen übersetzen; GitHub und KI brauchen
>    konkrete Prüf-/Übergabeergebnisse. Vergleich nur mit bezeichnetem
>    Beispiel, kein erfundener Marktstandard. Referenzen auf ihren wirklichen
>    Umfang begrenzen. Kein erfolgreicher PV-Fall als Beweis, dass jede kleine
>    Website dieselben Anfragen liefert. Kürze Wiederholung, erhalte relevante
>    Grenzen. Kundenwortlaut nur aus tatsächlichem Rohmaterial.
> 6. **SEO-/Performance-Pass:** Bewahre Suchintention und Query-Owner. Prüfe
>    Title/Description/H1, Canonical/robots, crawlbare Links und Inhalte,
>    zentralen Service-/FAQ-Owner, Bildmaße/lazy loading, lokale Schriften und
>    bestehende Asset-Ladung. Aktuelle externe SEO-Regeln nur aus primären
>    Quellen. Kein Keyword-Stuffing, keine neue Route für bloßes Beispiel,
>    keine behaupteten Rich Results oder CWV-Werte ohne gültige Prüfung.
> 7. **Daten- und Anfragepass:** Folge jeder Konfiguration von allen CTA-URLs
>    über Kontakt-Hidden-Felder und serverseitige Neuberechnung bis CRM und
>    beide Mails. Teste Seitenwechsel nach gewählten Extras, manuelle
>    Layoutzahlen, Vorlagen, Dashboard und manipulierte Browserwerte.
>    Preserve Consent, Spam-/Rate-Limits, Cache, IDs und vorhandene Hooks;
>    füge keine Analytics oder externen Datenflüsse nebenbei hinzu.
> 8. **Entscheidung und Lieferung:** Gib maximal fünf Hauptprioritäten mit
>    Ort, Beobachtung, Käuferfolge, konkreter Ersatzcopy/Interaktion und
>    Prüfkriterium aus. Rest als kleine Tabelle. Trenne Repo-Fixes, Manual-WP
>    und operative Messung. Erhalte bewährte Teile; behebe die größten
>    Verständnis-/Vertrauensprobleme zuerst. Implementiere autorisierte,
>    reversible Fixes, aktualisiere berührte Canons, prüfe passende Guards
>    und reale Browserzustände. Liefere Diff/PR, Screenshot und tatsächlichen
>    Prüfstatus. Benenne verbleibende Unsicherheit ohne sie zu verstecken.

## Operatives Follow-up

Die Texte sind Angebotscopy, keine validierten Kundenzitate; die vorhandene
Voice-of-Customer-Sammlung ist ungefüllt. Keine neuen Zitate verwendet.
Produktionsfaktoren anhand der nächsten bezahlten Standardprojekte kalibrieren:
Seiten/Layoutzahl, Vorlagenstatus, tatsächliche Text-/Design-/Umsetzungs-/QA-Zeit
und Kundenwartezeit getrennt festhalten. Vorhandene Auswertung in
`docs/experimente/anfrage-website.md` nutzen; keine neue Analytics-Installation.
Referenzen mit vergleichbarem Standardumfang wären der nächste Beleggewinn,
wenn echte Projekte und ihre Veröffentlichungsfreigaben vorliegen.

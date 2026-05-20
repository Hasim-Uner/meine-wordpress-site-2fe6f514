<?php
/**
 * One-time seeding for public lead-provider market classification posts.
 *
 * These posts started as editorial drafts in content/blog-drafts/. This module
 * publishes them once into WordPress so the editor remains the long-term owner
 * after the initial live creation.
 *
 * @package Blocksy_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the current seed version for provider markteinordnung posts.
 *
 * @return string
 */
function hu_get_lead_provider_posts_seed_version() {
	return '2026-05-20-1';
}

/**
 * Return the seed data for public provider markteinordnung posts.
 *
 * @return array<int, array<string, mixed>>
 */
function hu_get_lead_provider_posts_seed_data() {
	return [
		[
			'title'            => 'Aroundhome für Solarteure: Markteinordnung des Vergleichsportal-Modells',
			'slug'             => 'aroundhome-solar-einordnung',
			'seo_title'        => 'Aroundhome für Solarteure: Markteinordnung',
			'seo_description'  => 'Aroundhome als Vergleichsportal für PV-Anfragen: Modell, Verteilungslogik und wann eigene Anfrage-Systeme wirtschaftlicher sind.',
			'excerpt'          => 'Sachliche Einordnung des Vergleichsportal-Modells am Beispiel Aroundhome: Verteilungslogik, Vorqualifizierung, Markenwirkung und Alternative durch eigene Anfrage-Systeme.',
			'tags'             => [ 'Solar Leads', 'Aroundhome', 'Photovoltaik', 'Vergleichsportal', 'Markteinordnung' ],
			'markdown_content' => <<<'MD'
> Hinweis: Dieser Beitrag ist eine eigene Markteinordnung aus Sicht eines Systemarchitekten für Anfrage-Systeme im Solar-/SHK-Markt. Er ist kein Erfahrungsbericht, keine Rechtsberatung und keine abschließende Bewertung einzelner Vertragsbedingungen. Alle Angaben beruhen auf öffentlich zugänglichen Informationen, marktüblichen Mechaniken und wirtschaftlicher Systemanalyse. Markenrechte verbleiben bei den jeweiligen Inhabern.

# Aroundhome für Solarteure: Markteinordnung des Vergleichsportal-Modells

## Kernthese

Aroundhome ist eines der bekanntesten **Vergleichsportale** im deutschen Endkunden-Markt für Handwerks- und Energieprojekte. Das Modell ist breit aufgestellt (PV, Wärmepumpe, Treppenlift, Garten, etc.) und nutzt typischerweise eine **telefonische Vorprüfung** der Endkundenanfragen, bevor sie an Fachbetriebe verteilt werden.

Für Solarteure ist deshalb nicht die Frage, ob das Portal grundsätzlich Nachfrage erzeugen kann. Die wichtigere Frage lautet: **Bleibt daraus eine eigene Kundenbeziehung oder entsteht eine gemietete Lead-Abhängigkeit?**

Vergleichsportale können kurzfristig Volumen liefern. Ein eigenes Anfrage-System baut dagegen ein Betriebs-Asset auf: eigene Sichtbarkeit, eigene Daten, eigene Vorqualifizierung und exklusive Anfragen auf der eigenen Plattform.

## Wer sucht das?

- Solarteure, die das Angebot mehrerer Lead-Anbieter wirtschaftlich einordnen wollen
- Vertriebsleiter, die Vergleichsportale als Kanal evaluieren und Abschlussquoten verbessern müssen
- Inhaber von PV-Betrieben, die Portal-Leads mit eigener Leadgenerierung vergleichen
- Marketing-Verantwortliche im PV-Mittelstand, die von gemieteten Datensätzen zu eigener Anfrage-Infrastruktur wechseln wollen

## Wie das Vergleichsportal-Modell funktioniert

Vergleichsportale wie Aroundhome positionieren sich gegenüber dem Endkunden als **neutrale Vermittlungs-Instanz**. Der Endkunde gibt seinen Bedarf an, das Portal stellt den Kontakt zu passenden Fachbetrieben her. Die konkrete Verteilung hängt vom jeweiligen Vertrag, der Region und dem Produkttyp ab. Die typische Mechanik aus Sicht des Fachbetriebs:

- Endkunde füllt ein Formular oder telefoniert mit dem Portal
- Portal qualifiziert grob vor (Region, Projekttyp, Budget-Range)
- Anfrage wird je nach Modell an einen oder mehrere Fachbetriebe weitergegeben
- Fachbetriebe konkurrieren um Kontaktgeschwindigkeit, Vertrauen, Angebot und Preis

Wirtschaftlich heißt das: Der Fachbetrieb kauft nicht automatisch eine exklusive Kundenbeziehung. Er kauft zunächst einen Zugang zu einer Anfrage. Wie wertvoll dieser Zugang ist, entscheidet sich erst durch Exklusivität, technische Vorqualifizierung, Reaktionszeit, Abschlussquote und Marge.

## Drei Bewertungs-Dimensionen

### 1. Telefon-Vorprüfung

Eine telefonische Vorprüfung kann offensichtlich unpassende Anfragen reduzieren. Das ist ein Vorteil gegenüber reinen Online-Formularen ohne menschliche Prüfung.

Für Solarteure reicht eine grobe Vorprüfung aber selten aus. Wirtschaftlich entscheidend sind Fragen wie:

- Passt die Region zum Vertriebsgebiet?
- Ist die Immobilie grundsätzlich geeignet?
- Gibt es eine belastbare Investitionsabsicht?
- Ist der Ansprechpartner erreichbar und entscheidungsfähig?
- Sind Projektwert und Marge hoch genug?

Wenn diese Informationen fehlen, trägt der Vertrieb weiterhin einen großen Teil der Qualifizierungsarbeit selbst.

### 2. Mehrfach-Verteilung

Bei Vergleichsportalen ist Wettbewerb Teil des Nutzer-Versprechens: Der Endkunde erwartet mehrere Angebote oder zumindest eine neutrale Vorauswahl. Je stärker eine Anfrage verteilt wird, desto höher werden Reaktionsdruck und Vergleichbarkeit.

Das muss nicht automatisch schlecht sein. Es wird aber problematisch, wenn der Fachbetrieb:

- nicht innerhalb weniger Minuten reagieren kann
- kein starkes Erstgespräch hat
- keinen klaren Differenzierungsgrund liefert
- im Angebot fast nur über den Preis verglichen wird

Dann kann ein bezahlter Lead schnell zum Margenproblem werden.

### 3. Markenwirkung

Der Endkunde startet seine Reise beim Portal, nicht beim Fachbetrieb. Dadurch entsteht Vertrauen zunächst zur Vermittlungsplattform. Der Solarteur muss dieses Vertrauen im Erstkontakt nachträglich aufbauen.

Das ist der zentrale Unterschied zu einem eigenen Anfrage-System: Bei eigener Sichtbarkeit entsteht die Kundenbeziehung direkt auf der eigenen Website. Die Marke, die Daten, die Anfragehistorie und die Optimierung bleiben beim Betrieb. Genau hier liegt der Unterschied zwischen **Lead-Miete** und **Ownership**.

## Warnsignale, auf die Solarteure achten sollten

- **Datensatz ohne ausreichende technische Information** (Dachgröße, Heizart)
- **Reaktionszeit > 4 Stunden** – Auftrag faktisch verloren
- **Generische Projektbeschreibung** ohne klaren Kaufintent
- **Vertrag mit Mindestabnahme** ohne Volumengarantie für qualifizierte Anfragen
- **Keine klare Trennung zwischen exklusiven und geteilten Anfragen**
- **Kein Zugriff auf die Datenbasis**, mit der Kampagnen und Abschlussquoten verbessert werden können

## Die strategische Alternative: Eigene Anfrage-Infrastruktur

Die Alternative zu gemieteten Portal-Leads ist kein weiteres Tool, sondern eine eigene Anfrage-Infrastruktur:

- eine such- und conversionstarke Money Page
- klare Angebots- und Vertrauensführung
- Vorqualifizierung nach Region, Projektwert und Vertriebsfit
- Server-Side-Tracking auf eigener Datenbasis
- Übergabe an CRM und Vertrieb mit sauberem Kontext

So entsteht ein Betriebs-Asset. Die Anfrage kommt nicht aus einem fremden Portal, sondern aus einer Plattform, die dem Betrieb gehört.

Der [Methodik-Case E3 New Energy](https://hasimuener.de/e3-new-energy/) zeigt diesen Hebel konkret: Der Cost per Lead sank von 150 € auf 22 € bei über 1.750 qualifizierten Anfragen, 12 % Abschlussquote und 9 Monaten Optimierungszeit. Das ist kein allgemeines Versprechen, aber ein belastbarer Beleg dafür, was möglich wird, wenn Website, Tracking, Vorqualifizierung und Vertrieb zusammengebaut werden.

## Wann das Vergleichsportal-Modell trotzdem Sinn ergibt

- Übergangslösung beim **Markteintritt in einer neuen Region** ohne eigene Sichtbarkeit
- **Volumen-Ergänzung** zu einer bereits funktionierenden eigenen Strecke
- **Validierung** einer neuen Zielgruppe vor dem Aufbau eigener Funnels
- kurzfristige Auslastung, wenn der Vertrieb sehr schnell reagieren kann

Als alleinige Lead-Quelle ist das Modell im 2026er Markt für PV/Wärmepumpe jedoch riskant. Es macht Betriebe abhängig von externer Reichweite, fremder Datenbasis und schwankender Lead-Qualität. Eine ausführlichere System-Gegenüberstellung finden Sie im Vergleich [eigene Leadgenerierung vs. Lead-Portale](https://hasimuener.de/eigene-leadgenerierung-vs-portale/).

Für Betriebe mit eigener Vertriebsverantwortung, hohen Projektwerten und klarem Zielgebiet ist der strategisch stärkere Weg meist: Portal-Leads nicht blind abschalten, sondern parallel eine eigene Anfrage-Infrastruktur aufbauen. So wird aus kurzfristigem Volumen schrittweise ein eigenes Akquise-Asset.

## Nächster Schritt: Solar-Marktcheck

Wenn Sie prüfen möchten, ob sich ein eigenes Anfrage-System für Ihren Solar-, Wärmepumpen- oder Speicherbetrieb wirtschaftlich lohnt, starten Sie mit dem kostenfreien Marktcheck. Dabei werden Projektwert, Region, Vertriebsprozess, aktuelle Lead-Kosten und Abhängigkeit von Portalen eingeordnet.

→ [Solar-Marktcheck starten](https://hasimuener.de/solar-waermepumpen-leadgenerierung/#marktcheck)
MD,
		],
		[
			'title'            => 'Wattfox Solar Leads: Markteinordnung für Photovoltaik-Anbieter',
			'slug'             => 'wattfox-solar-leads-einordnung',
			'seo_title'        => 'Wattfox Solar Leads: Markteinordnung & Alternative',
			'seo_description'  => 'Wattfox Solar Leads: Modell, Verteilungslogik, Wirtschaftlichkeit und wann eigene Anfrage-Systeme die stärkere Alternative sind.',
			'excerpt'          => 'Sachliche Markteinordnung zu Wattfox als Lead-Anbieter im deutschen Photovoltaik-Markt: Verteilungslogik, Preisstruktur, Reaktionszeit und Ownership.',
			'tags'             => [ 'Solar Leads', 'Wattfox', 'Photovoltaik', 'Lead-Anbieter', 'Markteinordnung' ],
			'markdown_content' => <<<'MD'
> Hinweis: Dieser Beitrag ist eine eigene Markteinordnung aus Sicht eines Systemarchitekten für Anfrage-Systeme im Solar-/SHK-Markt. Er ist kein Erfahrungsbericht, keine Rechtsberatung und keine abschließende Bewertung einzelner Vertragsbedingungen. Alle Angaben beruhen auf öffentlich zugänglichen Informationen, marktüblichen Mechaniken und wirtschaftlicher Systemanalyse. Markenrechte verbleiben bei den jeweiligen Inhabern.

# Wattfox Solar Leads: Markteinordnung für Photovoltaik-Anbieter

## Kernthese

Wattfox gehört zu den marktbekannten Lead-Anbietern im deutschen Photovoltaik-Segment. Wer den Anbieter wirtschaftlich einordnen will, sollte drei Dimensionen prüfen: **Verteilungsmodell**, **Preisstruktur** und **Reaktionszeit-Anforderung**.

Die eigentliche strategische Frage lautet nicht nur: “Wie teuer ist ein Datensatz?” Sondern: Entsteht daraus eine eigene Kundenbeziehung oder bleibt der Betrieb dauerhaft abhängig von gemieteten Anfragen?

## Wer sucht das?

- Geschäftsführer von Solarteur-Betrieben, die akut Anfrage-Volumen suchen
- Vertriebsleiter, die ihre aktuellen Lead-Quellen evaluieren
- Marketing-Verantwortliche im SHK-/PV-Mittelstand, die Portal-Leads mit eigenen Anfrage-Systemen vergleichen
- Inhaber, die Lead-Miete gegen eigene Anfrage-Infrastruktur abwägen

## Wie das Modell funktioniert (Markteinordnung)

Lead-Portale wie Wattfox sammeln Endkunden-Interessenten über eigene Marketing-Kanäle (typischerweise SEO, Google Ads, Vergleichsformulare) und verkaufen die Datensätze an angeschlossene Fachbetriebe. Es gibt grundsätzlich zwei Varianten:

- **Geteilte Datensätze**: Eine Anfrage wird an mehrere Fachbetriebe parallel verkauft. Niedrigerer Stückpreis, höherer Wettbewerb pro Endkunde.
- **Exklusive Datensätze**: Eine Anfrage wird nur an einen Fachbetrieb weitergegeben. Höherer Stückpreis, weniger Wettbewerb pro Endkunde.

Welches Modell ein konkreter Anbieter fährt, lässt sich nur durch den Vertrag im Einzelfall klären. Marktbreit werden im PV-Segment **80 – 150 € pro exklusivem Datensatz** und **25 – 60 € pro geteiltem Datensatz** als typische Spannen genannt. Entscheidend ist aber nicht der Stückpreis, sondern der Cost per Auftrag nach Reaktionszeit, Abschlussquote und Marge.

## Drei Bewertungs-Dimensionen

### 1. Verteilungslogik

Die zentrale Frage lautet: An wie viele Fachbetriebe wird derselbe Datensatz parallel verkauft? Je stärker ein Lead geteilt wird, desto mehr verschiebt sich die Anfrage in Richtung Reaktions- und Angebotswettbewerb. Das kann Abschlussquoten belasten, auch wenn die ursprüngliche Anfrage grundsätzlich interessant ist.

### 2. Preisstruktur

Der reine Stückpreis ist nur die halbe Wahrheit. Wirtschaftlich relevant ist der **Cost per Auftrag** – also CPL geteilt durch Abschlussquote. Bei 80 € CPL und 3 % Abschlussquote liegen die reinen Lead-Kosten bei rund 2.667 € pro Auftrag.

### 3. Reaktionszeit

Bei geteilten Datensätzen entscheidet die Reaktionszeit häufig über den ersten Vertrauensvorsprung. Das setzt einen Vertrieb voraus, der innerhalb von Minuten reagieren kann und nicht erst nach Stunden nachfasst.

### 4. Ownership der Anfrage-Strecke

Wenn Reichweite, Formular, Datenbasis und Optimierung beim Anbieter liegen, bleibt der Betrieb abhängig. Das ist nicht automatisch falsch, aber es ist ein anderes Modell als ein eigener Anfragekanal. Wer langfristig Margen stabilisieren will, sollte prüfen, welche Daten, Landingpages und Learnings im eigenen Betrieb bleiben.

## Warnsignale, auf die Solarteure achten sollten

- **Datensätze ohne Region oder Heizart**: schwer sauber zu qualifizieren
- **Leads mit deutlicher Verzögerung** zwischen Anfrage und Übergabe
- **Generische Interessens-Anfragen** ohne erkennbaren Kaufintent
- **Unklare Trennung zwischen exklusiven und geteilten Anfragen**
- **Kein Zugriff auf Kampagnen- und Abschlussdaten**, die zur Verbesserung nötig wären

## Alternative: Eigene Anfrage-Infrastruktur

Eigene Anfrage-Systeme führen per Definition zu exklusiven Anfragen, weil die Strecke dem Betrieb gehört: Money Page, Server-Side-Tracking, Vorqualifizierung, CRM-Übergabe und Datenbasis. Der [Methodik-Case E3 New Energy](https://hasimuener.de/e3-new-energy/) zeigt, dass die Cost per Lead durch ein eigenes System von 150 € auf 22 € gesenkt werden können – bei 12 % Abschlussquote, über 1.750 qualifizierten Anfragen und 9 Monaten Optimierungszeit.

Das ist kein allgemeines Versprechen. Es zeigt aber den Unterschied zwischen Lead-Miete und Ownership: Bei einer eigenen Infrastruktur verbessert jeder Lernzyklus das eigene Betriebs-Asset.

## Wann das Portal-Modell trotzdem Sinn ergibt

In drei Situationen können Portal-Leads übergangsweise sinnvoll sein:

1. **Kurzfristiger Engpass**: Auslastungslücke im Vertrieb, die in zwei Wochen geschlossen sein soll
2. **Markteintritt ohne Marke**: neue Region, in der noch keine eigene Sichtbarkeit aufgebaut ist
3. **Validierung**: Erprobung einer neuen Zielgruppe oder eines neuen Produkts vor dem Aufbau eigener Strecken

Als alleinige Dauerstrategie ist Portal-Lead-Einkauf im 2026er Markt für PV/Wärmepumpe riskant. Eine ausführliche Gegenüberstellung finden Sie in der [Vergleichsmatrix eigene Leadgenerierung vs. Lead-Portale](https://hasimuener.de/eigene-leadgenerierung-vs-portale/).

## Nächster Schritt: Solar-Marktcheck

Wenn Sie prüfen möchten, ob Ihr Betrieb wirtschaftlich besser mit Portal-Leads, einer Mischstrategie oder einem eigenen Anfrage-System fährt, starten Sie mit dem kostenfreien Marktcheck.

→ [Solar-Marktcheck starten](https://hasimuener.de/solar-waermepumpen-leadgenerierung/#marktcheck)
MD,
		],
		[
			'title'            => 'DAA Photovoltaik Leads: Markteinordnung des Branchen-Portal-Modells',
			'slug'             => 'daa-photovoltaik-leads-einordnung',
			'seo_title'        => 'DAA Photovoltaik Leads: Markteinordnung für Solarteure',
			'seo_description'  => 'DAA Photovoltaik Leads: Spezialisierung, Verteilungslogik, Wirtschaftlichkeit und Alternative durch eigene Anfrage-Systeme.',
			'excerpt'          => 'Sachliche Markteinordnung zu DAA im PV-Segment: Spezialisierung, Vorqualifizierung, Verteilungsmodell und Ownership-Frage.',
			'tags'             => [ 'Solar Leads', 'DAA', 'Photovoltaik', 'Lead-Anbieter', 'Erneuerbare Energien' ],
			'markdown_content' => <<<'MD'
> Hinweis: Dieser Beitrag ist eine eigene Markteinordnung aus Sicht eines Systemarchitekten für Anfrage-Systeme im Solar-/SHK-Markt. Er ist kein Erfahrungsbericht, keine Rechtsberatung und keine abschließende Bewertung einzelner Vertragsbedingungen. Alle Angaben beruhen auf öffentlich zugänglichen Informationen, marktüblichen Mechaniken und wirtschaftlicher Systemanalyse. Markenrechte verbleiben bei den jeweiligen Inhabern.

# DAA Photovoltaik Leads: Markteinordnung des Branchen-Portal-Modells

## Kernthese

DAA gilt als spezialisierter Lead-Anbieter für **Erneuerbare Energien** im deutschen Markt mit Schwerpunkt auf Photovoltaik und Wärmepumpe. Wer das Modell wirtschaftlich einordnen will, sollte vier Dimensionen prüfen: **Spezialisierung**, **Verteilungslogik**, **Cost per Auftrag** und **Ownership der Datenbasis**.

Der Vorteil eines spezialisierten Branchen-Portals liegt in der thematischen Nähe. Die zentrale Frage bleibt aber: Baut der Betrieb damit eigene Nachfrage-Kompetenz auf oder bleibt er von fremder Reichweite und fremder Datenbasis abhängig?

## Wer sucht das?

- Geschäftsführer von Solarteur-Betrieben, die einen branchenspezifischen Lead-Anbieter evaluieren
- Vertriebsleiter, die ihre aktuellen Lead-Quellen gegenüberstellen
- Marketing-Verantwortliche im PV-/SHK-Mittelstand, die Branchen-Portale mit Vergleichsportalen vergleichen
- Inhaber, die branchenspezifische Lead-Quellen mit eigener Anfrage-Infrastruktur vergleichen

## Wie das Branchen-Portal-Modell funktioniert

Branchenspezialisierte Lead-Anbieter sammeln Endkunden-Interessenten gezielt über themenbezogene Inhalte wie Förderrechner, Kostenrechner oder Anlagen-Konfiguratoren und leiten die Datensätze an angeschlossene Fachbetriebe weiter. Mögliche Vorteile gegenüber generischen Vergleichsportalen:

- Endkunden kommen mit höherem Themen-Verständnis
- Vorqualifizierung oft technisch tiefer (Dachfläche, Heizart, EEG-Status)
- Inhaltliche Reichweite ist im jeweiligen Segment hoch

Mögliche Nachteile gegenüber eigenen, exklusiven Strecken:

- Datensätze können je nach Modell an mehrere Fachbetriebe verteilt werden
- Preisspanne pro Stück liegt im marktüblichen Bereich und muss gegen Abschlussquote gerechnet werden
- Markenwirkung und Datenbasis verbleiben überwiegend beim Portal

## Drei Bewertungs-Dimensionen

### 1. Spezialisierung als Vorteil

Ein Branchen-Portal kann besser qualifizierte Anfragen liefern als ein generisches Vergleichsportal, weil der Endkunde bereits durch einen themenspezifischen Trichter gegangen ist. Ob sich das positiv auf die Abschlussquote auswirkt, hängt aber von Übergabegeschwindigkeit, technischer Tiefe und Vertriebsprozess ab.

### 2. Mehrfachverkauf relativiert den Vorteil

Sobald derselbe Datensatz an mehrere Fachbetriebe verteilt wird, entsteht wieder Angebots- und Reaktionswettbewerb. Die höhere thematische Qualifizierung kann dann durch Wettbewerbsdruck relativiert werden.

### 3. Cost per Auftrag (CPO) prüfen

Wirtschaftlich relevant ist der **Cost per Auftrag**, nicht der reine Stückpreis. Bei einem CPL von 100 € und einer Abschlussquote von 4 % liegen die reinen Lead-Kosten bei rund 2.500 € pro Auftrag.

Bei geteilten Datensätzen kann ein fachlich guter Lead trotzdem teuer werden, wenn Reaktionsdruck, parallele Angebote und Vertriebsaufwand die Abschlussquote senken. Deshalb sollte jeder Lead-Kanal nicht nur nach CPL, sondern nach CPO, Marge und Vertriebszeit bewertet werden.

### 4. Ownership und Asset Drift

Ein spezialisierter Anbieter kann ein guter Kanal sein. Strategisch bleibt aber entscheidend, ob Website, Tracking, Zielgruppen-Learnings und CRM-Daten im Betrieb bleiben. Ohne eigene Datenbasis verbessert jeder Optimierungszyklus vor allem den externen Kanal, nicht das eigene Anfrage-Asset. Genau hier entsteht Asset Drift: Budget fließt in Nachfrage, aber der langfristige Wertaufbau findet nicht im eigenen Unternehmen statt.

## Warnsignale, auf die Solarteure achten sollten

- **Lange Lieferzeit** zwischen Endkundenklick und Anfrage-Eingang
- **Fehlende technische Vorqualifizierung** (Dachfläche, Heizart)
- **Mengen-Verträge** ohne Qualitätsgarantie
- **Generische Endkunden-Anfragen** ohne erkennbaren Kaufintent
- **Unklare Exklusivität** des Datensatzes
- **Kein Zugriff auf Performance- und Abschlussdaten**

## Die strategische Alternative: Ownership statt Lead-Miete

Ein eigenes Anfrage-System mit Money Page, Server-Side-Tracking, Vorqualifizierung und CRM-Übergabe erzeugt Anfragen, die per Definition **exklusiv** sind. Der [Methodik-Case E3 New Energy](https://hasimuener.de/e3-new-energy/) zeigt eine CPL-Senkung von 150 € auf 22 € bei 12 % Abschlussquote, über 1.750 qualifizierten Anfragen und 9 Monaten Optimierungszeit.

Das ist kein allgemeines Versprechen, sondern ein Beleg für den Unterschied zwischen gemieteter Nachfrage und eigener Anfrage-Infrastruktur: Die Marke, die Daten und die Optimierung bleiben beim Betrieb.

## Wann das Branchen-Portal trotzdem Sinn ergibt

- **Ergänzung** zu einer funktionierenden eigenen Strecke (Volumen-Boost)
- **Kurzfristige Auslastungslücke** beim Vertrieb
- **Validierung** einer neuen Region oder eines neuen Produkts

Als alleinige Lead-Quelle ist auch das Branchen-Portal-Modell riskant, wenn keine eigene Strecke parallel aufgebaut wird. Die ausführliche System-Gegenüberstellung finden Sie im Vergleich [eigene Leadgenerierung vs. Lead-Portale](https://hasimuener.de/eigene-leadgenerierung-vs-portale/).

## Nächster Schritt: Solar-Marktcheck

Wenn Sie prüfen möchten, ob branchenspezialisierte Portale, eine Mischstrategie oder ein eigenes Anfrage-System für Ihren Betrieb wirtschaftlich sinnvoller sind, starten Sie mit dem kostenfreien Marktcheck.

→ [Solar-Marktcheck starten](https://hasimuener.de/solar-waermepumpen-leadgenerierung/#marktcheck)
MD,
		],
		[
			'title'            => 'Leadfluss für Solarteure: Markteinordnung des regionalen Videomarketing-Modells',
			'slug'             => 'leadfluss-pv-leads-einordnung',
			'seo_title'        => 'Leadfluss PV-Leads: Markteinordnung',
			'seo_description'  => 'Leadfluss PV-Leads: regionales Videomarketing, Eigenmarken-Effekt, Ownership-Frage und wirtschaftliche Einordnung.',
			'excerpt'          => 'Sachliche Einordnung des regionalen Videomarketing-Modells am Beispiel Leadfluss: Markenwirkung, Exklusivität, Datenbesitz und eigene Infrastruktur.',
			'tags'             => [ 'Solar Leads', 'Leadfluss', 'Photovoltaik', 'Regionalmarketing', 'Videomarketing' ],
			'markdown_content' => <<<'MD'
> Hinweis: Dieser Beitrag ist eine eigene Markteinordnung aus Sicht eines Systemarchitekten für Anfrage-Systeme im Solar-/SHK-Markt. Er ist kein Erfahrungsbericht, keine Rechtsberatung und keine abschließende Bewertung einzelner Vertragsbedingungen. Alle Angaben beruhen auf öffentlich zugänglichen Informationen, marktüblichen Mechaniken und wirtschaftlicher Systemanalyse. Markenrechte verbleiben bei den jeweiligen Inhabern.

# Leadfluss für Solarteure: Markteinordnung des regionalen Videomarketing-Modells

## Kernthese

Leadfluss positioniert sich abseits der klassischen Lead-Portal-Logik: statt Datensätze aus zentralen Vergleichsformularen zu verteilen, werden Anfragen über **regional produzierte Werbevideos im Namen des Fachbetriebs** erzeugt. Damit liegt das Modell strukturell näher an der Eigenmarken-Logik als am klassischen Lead-Brokerage.

Für Solarteure ist das strategisch interessanter als reine Lead-Miete, weil die Anfrage stärker unter der eigenen Marke entsteht. Die wichtigste Prüf-Frage bleibt trotzdem: Gehört die Anfrage-Strecke dauerhaft dem Betrieb oder bleibt sie operativ beim Dienstleister?

## Wer sucht das?

- Solarteure, die regional Sichtbarkeit aufbauen wollen, ohne komplettes eigenes Marketing-Team
- Vertriebsleiter, die zwischen Portal-Leads und einer Eigenmarken-Strategie abwägen
- Marketing-Verantwortliche, die regionale Videoproduktion als Lead-Quelle prüfen
- Inhaber, die Agenturleistung und eigene Anfrage-Infrastruktur sauber trennen wollen

## Wie das regionale Videomarketing-Modell funktioniert

Im Unterschied zu zentralen Vergleichsportalen arbeitet das regionale Videomarketing-Modell vereinfacht mit:

- Werbevideos im Namen und Branding des einzelnen Fachbetriebs
- Schaltung dieser Videos in einer regional definierten Zielgruppe
- Anfragen, die stärker unter dem Markennamen des Fachbetriebs entstehen

Das kann strukturelle Vorteile gegenüber klassischen Portalen haben:

- **Markenwirkung beim Fachbetrieb** – der Endkunde assoziiert die Anfrage mit dem Solarteur, nicht mit einem Portal
- **Faktische Exklusivität** – die Anfrage landet nicht parallel bei drei Wettbewerbern
- **Regionale Steuerbarkeit** – Region und Zielgruppe sind definiert

## Drei Bewertungs-Dimensionen

### 1. Markeneffekt

Da die Anfragen stärker unter dem eigenen Branding kommen, kann sich Wiedererkennung aufbauen. Das ist der wichtigste Vorteil gegenüber klassischen Lead-Portalen, bei denen der Endkunde seine erste Beziehung meist zur Plattform aufbaut.

### 2. Ownership der Strecke

Wem gehören Landingpage, Werbe-Account, Tracking, Zielgruppen-Learnings und CRM-Anbindung? Wenn zentrale Bestandteile beim Dienstleister liegen, entsteht ein Kündigungsrisiko: Der Anfrage-Strom kann wegbrechen, obwohl die Marke im Markt gelernt hat.

### 3. Skalierbarkeit

Regionale Videokampagnen sind im Volumen durch Region, Angebot und Zielgruppe begrenzt. Für überregional skalierende Betriebe muss das Modell entsprechend multipliziert und sauber gemessen werden.

## Warnsignale, auf die Solarteure achten sollten

- **Kein Zugriff auf den Werbe-Account** – Ownership-Frage offen
- **Kein Zugriff auf Tracking-/CRM-Daten** – Datenbasis bleibt außerhalb des Betriebs
- **Pauschal-Verträge ohne Volumen-Klausel** – wirtschaftliches Risiko liegt beim Fachbetrieb
- **Generische Video-Templates** – Markenwirkung reduziert
- **Keine dokumentierte Übergabe** von Kampagnenstruktur, Learnings und Anfrageprozess

## Alternative: Eigene Anfrage-Infrastruktur in vollständigem Eigentum

Der nächstgrößere Schritt vom regionalen Videomarketing-Modell ist der Aufbau eines vollständig eigenen Anfrage-Systems: eigene Money Page, eigener Werbe-Account, eigene Tracking-Strecke, eigenes CRM und eigene Vorqualifizierung. Vorteil: Code, Daten und Strecke bleiben dauerhaft im Betrieb.

Der [Methodik-Case E3 New Energy](https://hasimuener.de/e3-new-energy/) demonstriert das mit einer CPL-Senkung von 150 € auf 22 €, über 1.750 qualifizierten Anfragen, 12 % Abschlussquote und 9 Monaten Optimierungszeit. Das ist kein pauschales Ergebnisversprechen, aber ein belastbarer Beleg für den Wert eigener Anfrage-Infrastruktur.

## Wann das Videomarketing-Modell trotzdem Sinn ergibt

- **Erstaufbau einer regionalen Sichtbarkeit** ohne eigenes Marketing-Team
- **Übergangsphase** während des Aufbaus eigener Anfrage-Strecken
- **Volumen-Booster** in definierten Regionen

Strategisch lohnt es sich, parallel zum Videomarketing-Vertrag die eigene Infrastruktur aufzubauen, damit die Strecke langfristig im Eigentum des Betriebs liegt. Den Systemvergleich finden Sie in der Analyse [eigene Leadgenerierung vs. Lead-Portale](https://hasimuener.de/eigene-leadgenerierung-vs-portale/).

## Nächster Schritt: Solar-Marktcheck

Wenn Sie prüfen möchten, welches Modell für Ihren Betrieb regional und wirtschaftlich am besten passt – Videomarketing-Dienstleister, eigene Infrastruktur oder eine Mischform –, starten Sie mit dem kostenfreien Marktcheck.

→ [Solar-Marktcheck starten](https://hasimuener.de/solar-waermepumpen-leadgenerierung/#marktcheck)
MD,
		],
	];
}

/**
 * Convert the constrained draft markdown format into post HTML.
 *
 * @param string $markdown Markdown body.
 * @return string
 */
function hu_lead_provider_markdown_to_html( $markdown ) {
	$lines       = preg_split( '/\r\n|\r|\n/', trim( (string) $markdown ) );
	$html        = '';
	$paragraph   = [];
	$list_items  = [];
	$list_type   = '';
	$flush_list  = static function() use ( &$html, &$list_items, &$list_type ) {
		if ( empty( $list_items ) || '' === $list_type ) {
			return;
		}

		$html .= '<' . $list_type . '>';
		foreach ( $list_items as $item ) {
			$html .= '<li>' . hu_lead_provider_format_inline_markdown( $item ) . '</li>';
		}
		$html       .= '</' . $list_type . '>' . "\n";
		$list_items = [];
		$list_type  = '';
	};
	$flush_para  = static function() use ( &$html, &$paragraph, $flush_list ) {
		$flush_list();

		if ( empty( $paragraph ) ) {
			return;
		}

		$html     .= '<p>' . hu_lead_provider_format_inline_markdown( implode( ' ', $paragraph ) ) . '</p>' . "\n";
		$paragraph = [];
	};

	foreach ( $lines as $line ) {
		$line = trim( (string) $line );

		if ( '' === $line ) {
			$flush_para();
			$flush_list();
			continue;
		}

		if ( hu_lead_provider_starts_with( $line, '# ' ) ) {
			$flush_para();
			$flush_list();
			continue;
		}

		if ( hu_lead_provider_starts_with( $line, '## ' ) ) {
			$flush_para();
			$flush_list();
			$html .= '<h2>' . hu_lead_provider_format_inline_markdown( substr( $line, 3 ) ) . '</h2>' . "\n";
			continue;
		}

		if ( hu_lead_provider_starts_with( $line, '### ' ) ) {
			$flush_para();
			$flush_list();
			$html .= '<h3>' . hu_lead_provider_format_inline_markdown( substr( $line, 4 ) ) . '</h3>' . "\n";
			continue;
		}

		if ( hu_lead_provider_starts_with( $line, '> ' ) ) {
			$flush_para();
			$flush_list();
			$html .= '<blockquote><p>' . hu_lead_provider_format_inline_markdown( substr( $line, 2 ) ) . '</p></blockquote>' . "\n";
			continue;
		}

		if ( hu_lead_provider_starts_with( $line, '- ' ) ) {
			if ( 'ol' === $list_type ) {
				$flush_list();
			}

			$list_type    = 'ul';
			$list_items[] = substr( $line, 2 );
			continue;
		}

		if ( preg_match( '/^\d+\.\s+(.+)$/', $line, $match ) ) {
			if ( 'ul' === $list_type ) {
				$flush_list();
			}

			$list_type    = 'ol';
			$list_items[] = $match[1];
			continue;
		}

		$paragraph[] = $line;
	}

	$flush_para();
	$flush_list();

	return trim( $html );
}

/**
 * PHP-version-safe starts-with helper.
 *
 * @param string $haystack Full string.
 * @param string $needle   Prefix.
 * @return bool
 */
function hu_lead_provider_starts_with( $haystack, $needle ) {
	return 0 === strpos( (string) $haystack, (string) $needle );
}

/**
 * Format constrained inline markdown for links and bold text.
 *
 * @param string $text Plain markdown text.
 * @return string
 */
function hu_lead_provider_format_inline_markdown( $text ) {
	$links = [];
	$text  = preg_replace_callback(
		'/\[([^\]]+)\]\((https?:\/\/[^)]+)\)/',
		static function( $matches ) use ( &$links ) {
			$key           = '%%HU_PROVIDER_LINK_' . count( $links ) . '%%';
			$links[ $key ] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( $matches[2] ),
				esc_html( $matches[1] )
			);

			return $key;
		},
		(string) $text
	);

	$text = esc_html( (string) $text );
	$text = preg_replace( '/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text );

	foreach ( $links as $key => $link_html ) {
		$text = str_replace( esc_html( $key ), $link_html, $text );
	}

	return $text;
}

/**
 * Ensure a taxonomy term exists and return its ID.
 *
 * @param string $name     Term name.
 * @param string $slug     Term slug.
 * @param string $taxonomy Taxonomy name.
 * @return int
 */
function hu_lead_provider_ensure_term_id( $name, $slug, $taxonomy ) {
	$term = term_exists( $slug, $taxonomy );

	if ( is_array( $term ) && ! empty( $term['term_id'] ) ) {
		return (int) $term['term_id'];
	}

	if ( is_int( $term ) ) {
		return $term;
	}

	$created = wp_insert_term(
		$name,
		$taxonomy,
		[
			'slug' => $slug,
		]
	);

	if ( is_wp_error( $created ) || empty( $created['term_id'] ) ) {
		return 0;
	}

	return (int) $created['term_id'];
}

/**
 * Return an administrator author ID for seeded posts.
 *
 * @return int
 */
function hu_lead_provider_seed_author_id() {
	$users = get_users(
		[
			'role__in' => [ 'administrator' ],
			'number'   => 1,
			'fields'   => 'ID',
			'orderby'  => 'ID',
			'order'    => 'ASC',
		]
	);

	return ! empty( $users ) ? (int) $users[0] : 1;
}

/**
 * Find a post by slug across public and editor statuses.
 *
 * @param string $slug Post slug.
 * @return int
 */
function hu_lead_provider_find_post_id_by_slug( $slug ) {
	$post_ids = get_posts(
		[
			'name'                   => (string) $slug,
			'post_type'              => 'post',
			'post_status'            => [ 'publish', 'draft', 'pending', 'future', 'private' ],
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		]
	);

	return ! empty( $post_ids ) ? (int) $post_ids[0] : 0;
}

/**
 * Publish the lead-provider markteinordnung posts once.
 *
 * @return void
 */
function hu_maybe_seed_lead_provider_markteinordnung_posts() {
	if ( wp_installing() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	$version    = hu_get_lead_provider_posts_seed_version();
	$option_key = 'hu_lead_provider_posts_seed_version';

	if ( get_option( $option_key ) === $version ) {
		return;
	}

	$category_ids = array_filter(
		[
			hu_lead_provider_ensure_term_id( 'Markteinordnung', 'markteinordnung', 'category' ),
			hu_lead_provider_ensure_term_id( 'Solar-/Wärmepumpen Anfrage-Systeme', 'solar-waermepumpen-anfrage-systeme', 'category' ),
		]
	);

	if ( empty( $category_ids ) ) {
		return;
	}

	$author_id = hu_lead_provider_seed_author_id();
	$all_done  = true;

	foreach ( hu_get_lead_provider_posts_seed_data() as $post ) {
		$slug        = sanitize_title( (string) $post['slug'] );
		$existing_id = hu_lead_provider_find_post_id_by_slug( $slug );
		$content     = hu_lead_provider_markdown_to_html( (string) $post['markdown_content'] );
		$post_data   = [
			'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_title'    => (string) $post['title'],
			'post_name'     => $slug,
			'post_content'  => $content,
			'post_excerpt'  => (string) $post['excerpt'],
			'post_author'   => $author_id,
			'comment_status' => 'closed',
			'ping_status'   => 'closed',
		];

		if ( $existing_id ) {
			$is_seeded     = '1' === (string) get_post_meta( $existing_id, '_hu_lead_provider_seeded', true );
			$should_update = $is_seeded || 'publish' !== (string) get_post_status( $existing_id );

			if ( $should_update ) {
				$post_data['ID'] = $existing_id;
				$result          = wp_update_post( wp_slash( $post_data ), true );
			} else {
				$result = $existing_id;
			}
		} else {
			$result = wp_insert_post( wp_slash( $post_data ), true );
		}

		if ( is_wp_error( $result ) || ! $result ) {
			$all_done = false;
			continue;
		}

		$post_id = (int) $result;

		wp_set_post_terms( $post_id, $category_ids, 'category', false );
		wp_set_post_terms( $post_id, array_map( 'sanitize_text_field', (array) $post['tags'] ), 'post_tag', false );

		update_post_meta( $post_id, 'seo_title', sanitize_text_field( (string) $post['seo_title'] ) );
		update_post_meta( $post_id, 'seo_description', sanitize_text_field( (string) $post['seo_description'] ) );
		update_post_meta( $post_id, '_hu_lead_provider_seeded', '1' );
		update_post_meta( $post_id, '_hu_lead_provider_seed_version', $version );
	}

	if ( $all_done ) {
		update_option( $option_key, $version, false );
	}
}
add_action( 'init', 'hu_maybe_seed_lead_provider_markteinordnung_posts', 31 );

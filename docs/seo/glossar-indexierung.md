# Glossar: Umfang, Indexierung und Suchintention

Entscheidung vom 24. September 2026. Die Glossar-Registry ist die technische
Quelle; `query-ownership.csv` hält die redaktionell vorgesehenen Suchintentionen
fest. Diese Zuordnung ist keine Behauptung über Suchvolumen oder Rankings.

## Umfang

| Öffentlicher Bereich | Bisherige Definitionen | Neue Definitionen | Gesamt |
| --- | ---: | ---: | ---: |
| Anfragen | 1 | 11 | 12 |
| Ladezeit & Technik | 4 | 8 | 12 |
| Tracking | 1 | 11 | 12 |
| SEO | 1 | 11 | 12 |
| Conversion | 3 | 9 | 12 |

Die 60 Erklärseiten werden nicht mit Alias-Verweisen verrechnet. Vier sichtbare
Themenverweise kommen in der Übersicht hinzu; der lokale Agentur-Alias bleibt
ausgeblendet. Insgesamt enthält das Register 65 Einträge, die Übersicht 64.

Neue Erklärungen enthalten eine Definition, einen eigenen praktischen Kontext,
ein Beispiel und typische Fehler. Die neuen Kerntexte umfassen ungefähr
130–165 Wörter pro Seite. Technische und produktspezifische Aussagen verweisen
auf Primärquellen. Bei Zahlenbeispielen handelt es sich um gekennzeichnete
Rechenbeispiele, nicht um Kundenresultate oder Branchenbenchmarks.

## Indexierung

Die Live-Prüfung vor dieser Erweiterung ergab: `/glossar/` und neun bestehende
Detailseiten erlauben die Indexierung und haben selbstreferenzierende Canonicals.
`/glossar/cta-hierarchie/` liefert `noindex, follow` und fehlt korrekt in der
Glossar-Sitemap. Die robots.txt sperrt den Bereich nicht.

Die 50 neuen Definitionen erhalten `index`. Zusammen mit den neun bereits
freigegebenen Definitionen ergeben sich 59 indexierbare Detailseiten. Die
bisherige Noindex-Entscheidung für CTA-Hierarchie bleibt bestehen: Die Seite
erklärt die Gewichtung mehrerer Handlungsaufforderungen als unterstützendes
Detail; für diese Erweiterung wird kein zusätzliches Ranking-Ziel festgelegt.
Der neue Begriff „Call to Action“ erklärt dagegen die Handlungsaufforderung
selbst. Beide Inhalte bleiben über normale Links nutzbar.

Eigenständige Definitionen behalten ihre eigene kanonische URL. Ein Canonical
auf eine Leistungsseite wäre für diese unterschiedlichen Inhalte keine passende
Verknüpfung. Alias-Verweise erhalten keine zusätzlichen indexierbaren
Definitionsseiten. Die Sitemap enthält nur die freigegebenen Detail-URLs.

Technische Indexierbarkeit bedeutet nicht tatsächliche Aufnahme in Google.
Für diese Entscheidung liegt keine aktuelle authentifizierte GSC-Auswertung
der Glossar-URLs vor. Es werden weder Indexaufnahme noch Rankinggewinn behauptet.

## Verhältnis zu den Hauptseiten

Das Glossar beantwortet eng umrissene Definitionsfragen. Leistungsseiten
beantworten Fragen nach Umsetzung, Anbieter und Projektumfang. Die neuen
Registry-Zeilen sichern deshalb ausdrücklich Begriffsfragen wie
„utm parameter bedeutung“ oder „caching bedeutung“, keine pauschale Übernahme
des gesamten Themengebiets.

| Definitionsinhalt | Passender Weg zur Umsetzung |
| --- | --- |
| Tracking-Ereignisse, Parameter, Tags, Consent Mode | `/ga4-tracking-setup/` |
| WordPress-Technik und Ladezeit | `/` |
| Technische SEO-Grundbegriffe | `/wordpress-agentur-hannover/#zusammenarbeit` |
| Anfragen, Landingpages und Conversion | `/#angebot-funnel` |

Diese Links sind Leserwege, keine Änderung der bestehenden Query-Owner.
Insbesondere bleiben Server-Side-Tracking-Anbieterfragen auf
`/server-side-tracking-b2b/`, lokale Agenturfragen auf der Agenturseite und
Freelancerfragen auf der Startseite. Der bestehende Relaunch-Artikel
`/website-relaunch/` bekommt keine konkurrierende Glossarseite. Der allgemeine
CPL-Begriff erklärt eine Rechnung; der Artikel `/cost-per-lead-photovoltaik/`
bleibt für die branchenspezifische Frage zuständig.

Auch einige Begriffsfragen teilen Wörter, erfüllen aber verschiedene Aufgaben.
Diese Abgrenzungen begründen die entsprechenden `distinct_from`-Einträge:

| Begriff | Abgrenzung zum verwandten Begriff |
| --- | --- |
| Sitzung | Definiert einen Besuch; „Sitzung mit Interaktion“ erklärt die zusätzlichen GA4-Aktivitätskriterien. |
| Conversion | Definiert eine Zielhandlung; „Conversion-Rate“ erklärt deren Verhältnis zu einer Bezugsgruppe. |
| Lead | Definiert einen Interessentenkontakt; „Lead Scoring“ erklärt seine Bewertung mit Punkten. |
| Lead | Definiert einen Interessentenkontakt; „Lead-Qualifizierung“ erklärt die Prüfung auf Projekteignung. |
| Lead | Definiert einen Interessentenkontakt; „Lead Nurturing“ erklärt dessen weitere Begleitung. |

Jede Definition ist aus der Übersicht ohne JavaScript erreichbar. Verwandte
Begriffe und ein passender Umsetzungslink helfen beim Weiterlesen. Neue
Suchsynonyme stehen ausschließlich in `search_terms`; die automatischen
Blog-Verlinkungen über `keywords_match` werden nicht erweitert.

Die Empfehlung lautet, diese eigenständigen hilfreichen Definitionen
indexierbar zu halten. Ihr Nutzen liegt in verständlichen Antworten und
passenden Wegen zu weiteren Inhalten. Die bloße Anzahl zusätzlicher URLs
garantiert keine Stärkung der Hauptseiten.

## Bewertung nach dem Release

Nach ausreichender Crawl- und Beobachtungszeit sollten GSC-URL-Prüfung,
Indexierungsberichte und Suchanfragen zeigen, welche Definitionen tatsächlich
aufgenommen wurden und zu welchen Fragen sie erscheinen. Bei Überschneidungen
mit Leistungsseiten ist die konkrete Suchintention zu prüfen; gemeinsame
Fachwörter allein belegen keine schädliche Konkurrenz.

Zusätzlich sind vorhandene Daten zu Übergängen auf Leistungsseiten und zur
Qualität eingegangener Anfragen hilfreich. Dafür wurden weder neue Tracker
noch automatische Verlinkungen eingebaut. Überarbeitungen oder Zusammenlegungen
sollten sich auf solche Befunde und inhaltliche Überschneidungen stützen.

## Primärquellen

- [Google: Hilfreiche, verlässliche Inhalte](https://developers.google.com/search/docs/fundamentals/creating-helpful-content)
- [Google: Noindex](https://developers.google.com/search/docs/crawling-indexing/block-indexing)
- [Google: Interne Links und Linktexte](https://developers.google.com/search/docs/crawling-indexing/links-crawlable)
- [Google: Kanonische URLs für doppelte Inhalte](https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls)

Weitere technische Quellen stehen direkt beim jeweiligen Glossarbegriff.

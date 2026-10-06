# SEO CONTENT SYSTEM – FINAL CUT

Stand: 2026-10-06

## Ziel

Das Content-System baut nicht möglichst viel Traffic auf, sondern die
kommerziell passende Autorität der aktuellen Positionierung:

- WordPress-Entwicklung
- technisches SEO
- Tracking & Attribution
- Conversion-Optimierung
- Landingpages
- White-Label für Agenturen

Solar/Wärmepumpe bleibt eine spezialisierte Vertikale mit eigenem Cluster und
eigenem Proof. Historische Solar-Sichtbarkeit ist ein vorhandenes Asset, aber
nicht das globale Zielbild.

## 1. Architektur vor Content

Jede neue Idee durchläuft zuerst das Query-Ownership-Gate:

1. Welche Suchintention liegt vor?
2. Welche bestehende URL besitzt sie?
3. Gibt es GSC-/SERP-Signale für einen bestehenden Owner?
4. Kann die bestehende URL den Intent besser bedienen?
5. Nur wenn kein sinnvoller Owner existiert, darf eine neue URL entstehen.

Autoritative Zuordnung: `docs/seo/query-ownership.csv`.

## 2. Content-Ebenen

### Service Content

Commercial / transactional.

Beispiele:

- WordPress-Website erstellen lassen
- Landingpage erstellen lassen
- Conversion Tracking
- Server-Side Tracking
- Conversion-Optimierung
- White-Label

Ziel: Entscheidung und Anfrage.

Service-Seiten erklären nicht jede Grundlage selbst. Sie verlinken auf
passende Definitionen, Leitfäden und Belege.

### Pillar / Expertise Content

Informational oder investigational.

Ziel: eine fachliche Frage so gut beantworten, dass die Seite als Referenz,
interne Autoritätsquelle und potenziell zitierfähige Quelle für generative
Systeme funktioniert.

Priorität haben originäre Themen:

- technische Entscheidungen
- QA- und Abnahmeprotokolle
- Relaunch-Architektur
- Tracking-Testmethoden
- Übergabe/Handover
- eigene Messungen und nachvollziehbare Modelle

Kein Commodity-Content nur zur Keyword-Abdeckung.

### Proof Content

Validation / Bottom Funnel.

Ziel: Methode und eigene Arbeit prüfbar machen.

Bevorzugt:

- reale Projektanteile
- dokumentierte Methodik
- Vorher/Nachher nur bei belastbaren Daten
- öffentliche Referenzen
- technische Abnahme- oder Messprotokolle

Keine Erfolgsbehauptung, wenn nur der Prozess belegt ist.

### Glossar

Definitorischer Supporting Layer.

Ziel:

- Begriffe eindeutig erklären
- interne Verlinkung erleichtern
- Service-/Pillar-Seiten entlasten

Glossar ist kein Volumenprogramm und darf keine kommerziellen Query-Owner
kannibalisieren.

## 3. Interner Authority Graph

Der kommerzielle Kern wird in
`blocksy-child/inc/final-cut-authority.php` kuratiert.

Grundregeln:

- maximal drei kontextuelle Ziele je kommerziellem Owner
- exakte, beschreibende Anker statt „Mehr erfahren“
- Supporting Content stärkt Money Pages
- Service-Seiten verbinden angrenzende, aber getrennte Intents
- Solar bleibt in seinem eigenen Cluster
- Footer-Navigation und fachlicher Kontextgraph sind zwei verschiedene Ebenen

## 4. GEO / Generative Search

GEO ist eine zusätzliche Qualitätsprüfung, kein separates Tricksystem.

Eine Seite ist GEO-stark, wenn sie:

- eine klare Entität und einen klaren Autor besitzt
- originäre oder prüfbare Aussagen liefert
- konkrete Fragen direkt beantwortet
- Quellen und Methodik sauber trennt
- intern eindeutig einem Thema/Service zugeordnet ist
- crawlbar und indexierbar ist
- Person-, Organization- und Service-Schema nicht widerspricht

`llms.txt` und AI-Crawler-Regeln unterstützen Retrieval, ersetzen aber weder
klassische SEO noch Entity- und Content-Qualität.

## 5. Kannibalisierungs-Schutz

- ein primärer Intent = ein Owner
- lokale Vergleichsseite ist keine zweite globale Money Page
- breites Conversion Tracking und Server-Side Tracking bleiben getrennte Owner
- Website-Relaunch bleibt informational; Website-Angebot bleibt transactional
- White-Label-/Agentur-Intent bleibt vom Direktkundengeschäft getrennt
- Energie-Cluster darf seine historische Sichtbarkeit behalten, ohne globale
  WordPress-/Tracking-Seiten semantisch zurück in Solar zu ziehen

## 6. Priorisierungsmodell

Neue SEO-Arbeit wird in dieser Reihenfolge bewertet:

1. technischer/indexatorischer Fehler
2. falsche Query Ownership oder Cannibalization
3. schwacher kommerzieller Owner mit bestehendem Nachfrage-Signal
4. fehlender Supporting Content für einen strategischen Owner
5. fehlender Proof
6. Glossar-/Longtail-Erweiterung

Business Impact, Datenlage, Aufwand und Reversibilität bestimmen die Reihenfolge.

## 7. Messung

Classic Search:

- Impressionen
- Klicks
- CTR
- Position
- Query-Verteilung je Owner
- Mehrfachranking

Generative Search:

- AI-/Generative-Impressionen nach URL, soweit Search Console verfügbar
- zitierte/ausgewählte URLs
- Themen, bei denen die Domain als Quelle erscheint

Business:

- Projektanfragen
- Anfrageart
- Qualität/Fit
- Übergang von Content zu Service
- interne Klicks auf den Authority Graph

## 8. Arbeitsregel

Keine Seite wird geändert, nur weil eine allgemeine SEO-Best-Practice existiert.

Jede Änderung braucht:

- Befund
- Beleg
- Ursache
- strategische Bedeutung
- konkrete Maßnahme
- Priorität
- Messkriterium
- Revisionsbedingung

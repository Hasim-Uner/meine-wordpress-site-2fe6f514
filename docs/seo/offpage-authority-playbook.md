# Off-Page Authority Playbook — Solar-First (90 Tage)

Stand: 2026-05-30. Verantwortlich: Haşim Üner (manuelle Umsetzung außerhalb des Repos).

> **Warum dieses Dokument existiert.** Die On-Page-Architektur für den Solar-Cluster ist
> umgesetzt und sauber: topische Fokussierung (Startseite, Nav, llms.txt), Autoritätsfluss
> von Startseite und Money-Page in alle acht Sub-Pages, Cluster-Quervernetzung, keyword-scharfe
> Titles/Meta, Provider-Vergleichsposts live, Schema vollständig. Trotzdem ranken alle URLs auf
> Position 13–58. Das ist **kein On-Page-Defizit**, sondern das typische Muster einer **jungen
> Domain ohne externe Autoritätssignale**. Der limitierende Faktor ist Off-Page. Dieses Dokument
> ist der priorisierte Maßnahmenplan dafür.

## Diagnose in einem Satz

347 Impressionen, 0 Keywords in den Top 10, Ø-Position ~34 → Google hält die Inhalte für
**relevant** (deshalb die Impressionen), aber die Domain für **nicht vertrauenswürdig genug**
(deshalb keine Top-10-Platzierung). Vertrauen entsteht nicht im Code, sondern durch externe
Belege: wer verlinkt dich, wer erwähnt dich, existierst du als Entität.

## Die drei echten Hebel (nach Wirkung sortiert)

1. **Themen-relevante Backlinks** — der mit Abstand stärkste Hebel für eine junge Domain.
2. **Entitäts- & Brand-Signale** — Google muss „Haşim Üner" + „E3 New Energy" als reale Akteure
   im Solar-Leadgen-Kontext erkennen.
3. **Lokale & Branchen-Citations** — konsistente NAP-Daten + Google Business Profil als Fundament.

Reihenfolge ist Absicht: ohne Hebel 1 bewegt sich die Position nicht.

---

## Phase 1 — Fundament (Woche 1–4)

Ziel: konsistente Entität etablieren, erste vertrauenswürdige Citations, GBP live.

### 1.1 Google Business Profil

- Profil anlegen/verifizieren. Kategorie primär: „Marketingagentur" bzw. „Internetmarketing-Dienst".
- NAP exakt wie im Impressum (`/impressum/`) — zeichengenau identisch halten.
- Leistungsbeschreibung mit Solar-/Wärmepumpen-Leadgen-Fokus, Link auf
  `/solar-waermepumpen-leadgenerierung/`.
- Auch ohne Laufkundschaft relevant: GBP ist ein Entitäts- und Trust-Signal, kein reines Local-Pack-Tool.

### 1.2 Konsistente Citations (NAP)

Identische Firmierung + Adresse + URL in:

- Wer liefert was (`wlw.de`) — B2B-Standard im DACH-Raum
- Gelbe Seiten / 11880 / Das Örtliche
- LinkedIn-Unternehmensseite (+ persönliches Profil mit „Solar-Anfrage-Systeme" in der Headline)
- Xing (im DACH-B2B noch relevant)
- North Data / Companyhouse (Entitäts-Bestätigung über Handelsregister-Daten)

> **NAP-Disziplin:** Eine einzige kanonische Schreibweise. Abweichungen (mal „GmbH", mal ohne;
> mal „Str.", mal „Straße") verwässern das Entitätssignal.

### 1.3 Entitäts-Anker setzen

- `sameAs` in `org-schema.php` prüfen: LinkedIn, Xing, GitHub, GBP-URL hinterlegen (sobald live).
- E3 New Energy als referenzierbare Entität: wenn E3 öffentlich auf dich verlinkt
  (z. B. „Umgesetzt von …" im Footer oder einer Case-Page), ist das der **wertvollste
  einzelne Backlink**, den du kurzfristig bekommen kannst — themenrelevant, echt, im Solar-Umfeld.

---

## Phase 2 — Relevanz-Links (Woche 4–8)

Ziel: themenrelevante Verlinkungen aus dem Solar-/SHK-/B2B-Marketing-Umfeld.

### 2.1 Branchen- & Verbandsumfeld

- Photovoltaikforum.com — fachlich hochrelevant; Profil mit echtem Mehrwert, kein Spam.
- Relevante Solar-/Energie-Newsletter & Blogs für Gastbeiträge (Angle: „Warum Portal-Leads die
  Marge fressen" — deine TCO-/CPL-Argumentation ist genuin einzigartig und zitierfähig).
- Handwerks-/SHK-nahe Plattformen, wenn Gastbeiträge möglich sind.

### 2.2 Referenz- & Beziehungs-Links

- Eingesetzte Tools/Partner mit „Build-with"- oder Referenz-Programmen (Hosting HostPress,
  Cal.com, eingesetzte SaaS) → Kundenreferenz-Listen sind oft do-follow und themennah.
- Bestehende Kontakte/Kunden: um eine ehrliche Referenz-Erwähnung mit Link bitten.

### 2.3 Digital-PR-Angle (skalierbar)

Dein stärkster zitierfähiger Asset ist **Daten**: die CPL-/TCO-Rechnung (Portal-Lead 60–120 €,
Mehrfachverkauf bis 5×, E3 CPL-Senkung > 85 %). Daraus eine kleine **Daten-/Marktstudie** machen
(„Was Solar-Leads im DACH-Markt 2026 wirklich kosten") → solche Datenstücke ziehen organische
Links von Branchenmedien. Der CPO-Rechner (`cpo-calculator.php`) kann als interaktiver,
linkwürdiger Asset ausgebaut werden.

---

## Phase 3 — Content-getriebene Links & Velocity (Woche 8–12)

Ziel: bestehende Assets aktiv für Linkaufbau nutzen, Crawl-Frequenz erhöhen.

- Provider-Vergleichsposts (Aroundhome, Wattfox, DAA, Leadfluss — bereits live) gezielt dort
  platzieren, wo Solarteure über diese Anbieter diskutieren (Foren, LinkedIn-Gruppen, Kommentare).
  Diese Posts fangen „[Anbieter] Erfahrungen/Alternative"-Suchen ab und sind Outreach-Aufhänger.
- Content-Kadenz: 1–2 neue Cluster-/Provider-Posts pro Monat → konstante Frische- und Crawl-Signale.
- LinkedIn als Distributionskanal: jede neue Analyse als Post, der auf die Money-Page verlinkt
  (Brand- + Referral-Traffic-Signal, das Google indirekt liest).

---

## Lokaler Track — „WordPress Agentur Hannover" (parallel zum Solar-Track)

Für das lokale Commercial-Keyword **„wordpress agentur hannover"** (aktuell Pos ~32) wirkt ein anderer Hebel als bei den Solar-Begriffen: nicht topische Solar-Links, sondern **lokale Entitäts- und Citation-Signale**. Die Money-Page (`/wordpress-agentur-hannover/`) ist on-page fertig (LocalBusiness-Schema mit Geo/NAP, H1 trifft den Term, Standort-Sektion). Es fehlt die **externe lokale Bestätigung**.

### L1 — Google Business Profil (lokal, höchste Priorität)

- Dasselbe GBP wie in Phase 1.1, aber **lokal optimiert**: Primärkategorie „Webdesigner" bzw. „Internetmarketing-Dienst", Sekundär „Werbeagentur".
- Als Service-Area-Business: **Hannover + Region Hannover** explizit als Einzugsgebiet hinterlegen (deckt sich mit `areaServed` im Schema).
- Beschreibung mit „WordPress-Agentur in Hannover für B2B-Anfrage-Systeme", Link auf `/wordpress-agentur-hannover/` (nicht nur die Solar-Page).
- Erste 3–5 echte Kunden-/Partner-Bewertungen — lokaler Trust-Faktor Nr. 1.

### L2 — Lokale & Branchen-Citations (NAP zeichengenau aus `/impressum/`)

Dieselbe kanonische NAP-Schreibweise (Firmierung · Pattensen/Region Hannover · `/wordpress-agentur-hannover/`) in:

- **Regional:** Branchenbuch Region Hannover, Das Örtliche / Gelbe Seiten Hannover, regionale B2B-/Mittelstandsverzeichnisse.
- **IHK Hannover:** Mitglieds-/Firmenverzeichnis-Eintrag (starkes lokales Entitätssignal).
- **B2B-Standard DACH:** wlw.de, 11880, Cylex, Yelp DE — mit identischer NAP.
- **NAP-Disziplin gilt absolut:** eine Schreibweise überall, identisch zum Impressum (siehe Phase 1.2). Solar- und Agentur-Citations nutzen dieselbe Entität — **nicht** zwei konkurrierende Profile anlegen.

### L3 — Lokale Relevanz-Links

- Hannover-/Niedersachsen-Unternehmensnetzwerke, lokale Gründer-/Mittelstands-Initiativen, Co-Working-/Startup-Verzeichnisse der Region.
- Lokale Kunden/Partner um eine Referenz-Erwähnung mit Link auf `/wordpress-agentur-hannover/` bitten (ein einziger themen- **und** ortsrelevanter Link wiegt schwer).
- Gastbeitrag/Interview in einem regionalen Wirtschafts-/Gründer-Medium (Angle: „B2B-Website als Anfrage-System statt Schaufenster" — die E3-Datenstory trägt auch lokal).

### Erwartung & Messung (lokal)

- Frühindikator: GBP live + 5 konsistente Citations → „wordpress agentur hannover" sollte als Erstes aus Pos 30+ Richtung Pos 10–20 wandern (lokale Terme reagieren schneller auf Citations als breite Solar-Terme auf topische Links).
- Monatlich in der Search Console: Position „wordpress agentur hannover" + „wordpress hannover", lokale Impressionen, Brand-Suchen „haşim üner".

---

## Was NICHT tun (Anti-Pattern für junge Domains)

- **Keine** gekauften Link-Pakete / PBNs / Massenverzeichnisse — bei junger Domain
  Vertrauens-Gift, kein -Aufbau.
- **Keine** weiteren Money-/Cluster-Pages bauen, bevor die bestehenden Autorität gesammelt haben.
  Mehr Seiten ohne Links = mehr Verdünnung, nicht mehr Sichtbarkeit.
- **Kein** Snippet-Microtuning als Ranking-Strategie erwarten — Titles sind bereits scharf;
  CTR-Optimierung greift erst ab Top 10.

## Messpunkte (monatlich, nicht wöchentlich)

> 7-Tage-Vergleiche bei 2 Klicks sind statistisches Rauschen. Monatlich messen.

| Kennzahl | Quelle | Zielrichtung 90 Tage |
| --- | --- | --- |
| Verweisende Domains | Search Console / Backlink-Tool | von ~0 auf 5–10 themennahe |
| URLs in Top 20 | Search Console | mind. `solar-leads-kaufen-alternative` in Top 10 |
| Impressionen-Trend | Search Console | weiter steigend (Relevanz-Bestätigung) |
| Brand-Suchen „haşim üner / e3" | Search Console | erste Marken-Impressionen |

## Realistische Erwartung

Junge Domain + competitive Solar-Leadgen-Nische: spürbare Bewegung in **3–6 Monaten**, nicht in
Wochen. Der Pos.-13-Kandidat (`/solar-leads-kaufen-alternative/`) ist der erste, der bei
2–3 relevanten Backlinks in die Top 10 kippen kann — er ist der Frühindikator, ob der Off-Page-Motor anspringt.

## Schnittstelle zum Repo

Was hier in den Code zurückspielt (separat umsetzbar):

- `sameAs`-Profile in `org-schema.php` ergänzen, sobald GBP/Social live sind.
- CPO-Rechner zu einem eigenständig linkwürdigen Asset ausbauen (Phase 2.3).
- Daten-/Marktstudie als eigene Pillar-Page, wenn die Studie steht (Phase 2.3).

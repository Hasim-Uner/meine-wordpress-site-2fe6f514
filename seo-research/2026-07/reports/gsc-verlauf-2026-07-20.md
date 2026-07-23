# GSC-Verlaufskontrolle & SEO-Voll-Audit (SEO-Cockpit-Exporte 2026-07-20)

Datenbasis: Uploads `seocockpitexport7d-2026-07-20` und `seocockpitexport28d-2026-07-20`.
Baseline für den Verlauf: `gsc-abgleich.md` (Stand 2026-07-03). Dieser Report ist eine
**Verlaufskontrolle** — er misst, welche Vorhersagen des 07-03-Sprints eingetroffen sind, nicht nur
eine Momentaufnahme.

> **Methodik-Hinweis (korrigiert 2026-07-23):** Die SCF-/SEO-Editorfelder werden **nicht** genutzt (leer) —
> die Money-Page-Title/Descriptions kommen aus einer **Code-Map**: `hu_get_forced_singular_seo_map()`
> (`inc/seo-meta.php:172-292`). Diese greift VOR dem SCF-Feld und ist die Quelle der GSC-Export-Titel.
> **→ Money-Page-Title/Meta = Repo-Edit in dieser Map, KEINE WP-Admin-Aufgabe.** Homepage/Blog-Index/
> Cornerstone/Marktcheck/Kontakt/WGOS-Titel werden ebenfalls im Code erzwungen. Blog-Post-Titel/-Metas
> liegen in den Seed-Registries (`inc/blog-pillar-posts.php`, `inc/blog-provider-posts.php`) → Repo, gehen
> aber erst nach Seeder-Lauf/Neuspeichern live.

## Gesamtbild

- **28 Tage: ~1.299 Impressionen, 4 Klicks (CTR 0,31 %).** 7 Tage: 259 Impressionen, **0 Klicks**.
- Das Muster ist unverändert: **viele Impressionen, fast keine Klicks.** Ursache ist doppelt —
  überwiegend Position Seite 2–7 **und** schwache Snippets auf den wenigen Seite-1-Rankings.
- Die 4 Klicks (28 T): `wordpress agentur hannover` → Hannover; `checkfox erfahrungen` → Checkfox;
  `b2b photovoltaik` → b2b-solar-leads; `wattfox` → solar-leads-kaufen-alternative.

### Verlauf vs. Baseline 2026-07-03
| Seite / Query | 07-03 | 07-20 | Befund |
|---|---|---|---|
| Hannover · „wordpress agentur hannover" | 342 Impr., Pos. → 11,5 (7d) | 274 Impr., Pos. 17,6 (28d) / **32,1 (7d)** | **Rückschritt** — Aufwärtstrend gekippt |
| b2b-solar-leads · „pv termine b2b" | 86 Impr., Pos. 9,65 | 45 Impr., Pos. 9,69 | Seite 1; **dichtester Klick-Kandidat**; Impr. gesunken |
| waermepumpen-leads · „wärmepumpen leads" | erwartete Übernahme | 28 Impr., Pos. 28,7 | Teilerfolg — rankt jetzt, aber noch kannibalisiert |
| **checkfox-…-einordnung** · „checkfox seriös" + Var. | (Post neu) | **~120 Impr. gesamt, Pos. ~7–8** | **Neuer Seite-1-Cluster** — Portal-Post-Strategie bestätigt |
| server-side-tracking-b2b · „server side tracking agentur" | Pos. 60–90 | 92 Impr., Pos. 56,8 | Nachfrage da, nicht konkurrenzfähig |

---

## Critical

1. **Hannover-Ranking abgesackt.** `/wordpress-agentur-hannover/` war am 07-03 auf dem Sprung auf Seite 1
   (Pos. 11,5 im 7d-Fenster), steht jetzt bei Pos. 17,6 (28d) und **32,1 im letzten 7d-Fenster**. Das ist
   das reichweitenstärkste Asset der Domain (274 Impr. auf einer Query) — der Rückschritt kostet am meisten.
   → **Positions-Problem, kein Snippet-Problem.** Hebel: Content-Tiefe + interne Links (siehe Repo-/Admin-Tasks).
2. **Homepage-Titel im Export veraltet — aber vermutlich nur Daten-Hygiene.** Der Export zeigt weiter die
   alte Positionierung („WordPress Growth Architect…"). Der Code erzwingt aber bereits den neuen Titel
   („Anfrage-Systeme für Solar & Wärmepumpe | Haşim Üner", `seo-meta.php:30-35,937-939`), **unabhängig vom
   ACF-Feld**. → Live-`<title>` per View-Source verifizieren; danach das veraltete ACF-Feld der Startseite
   leeren, damit künftige Exporte stimmen. (Offen seit 07-03.)

## High-Leverage

3. **Checkfox = der beste Snippet-Hebel der Domain.** ~120 Impressionen auf Seite 1 (Pos. ~7–8) für
   `checkfox seriös`, `checkfox erfahrungen`, `ist checkfox seriös`, `checkfox bewertung`. **Repo-Edit** (Blog-Post).
   Zusätzlicher Befund: die aktuelle Meta-Description ist **~172 Zeichen → über dem 160-Limit** und wird
   im SERP abgeschnitten. → In Phase 1 gekürzt + geschärft (siehe unten).
4. **„PV-Termine B2B" — dichtester Klick-Kandidat.** `/b2b-solar-leads/` rankt Pos. 9,7 (45 Impr., 0 Klicks)
   für `pv termine b2b`. Die Seite bediente das bisher nur in einer FAQ. → In Phase 1 eigener Abschnitt
   „PV-Termine im B2B" ergänzt (Repo).
5. **Kannibalisierung „wärmepumpen leads".** Vier Seiten konkurrieren; die dedizierte `/waermepumpen-leads/`
   (Pos. 28,7) soll die Query allein besitzen. Kernproblem: `/solar-leads-kaufen-alternative/` (Pos. 55)
   verlinkte **gar nicht** auf die dedizierte Seite. → In Phase 1 interner Anker gesetzt (Repo).

## Polish

6. **Server-Side-Cluster differenzieren.** `/server-side-tracking-gtm/` (2.227 W.) sammelt nur 4 Impr.,
   während `/server-side-tracking-b2b/` (793 W.) 238 Impr. holt. USPs aus dem Ratgeber in die Landingpage
   ziehen, den Ratgeber klar als Info-Support abgrenzen + interlinken. **Der GTM-Post ist nicht im Repo
   geseedet → WP-Admin-Aufgabe** (301 nur, falls sich der Guide nicht sauber abgrenzen lässt).
7. **Non-Target-Rauschen bewusst ignorieren:** `woocommerce agentur hannover` (63 Impr., bereits als
   non-target markiert), `elevar server side tracking`, `server side tracking regensburg`, retirete
   Wartungs-Queries. Keine Optimierung, keine neuen Seiten.

## Bestätigte Stärke — nicht anfassen

**Core Web Vitals / Performance sind exzellent** (PageSpeed 2026-07-23, Startseite mobil: Leistung 99,
Barrierefreiheit 100, Best Practices 100, SEO 100). Das ist ein Wettbewerbsvorteil und bleibt so.
→ Keine Snippet-/Content-/CRO-Maßnahme darf CWV regressieren; Homepage-Assets (JS/CSS) werden nicht
angefasst, solange die Werte stehen. Die „Asset-Slimming"-Empfehlung des fremden Prompts ist gegenstandslos.

## Kannibalisierungs-Karte (2026-07-20)

| Query | Seiten (Impr./Pos.) | Empfehlung |
|---|---|---|
| **wärmepumpen leads** | /waermepumpen-leads/ (28/28,7) · /solar-leads-kaufen-alternative/ (25/55) · /…-lohnt-sich/ (5/81) · /…-kosten-studie/ (3/69) | /waermepumpen-leads/ **besitzt** die Query → Anker + interne Links dorthin; Wärmepumpen-Entität auf den Sekundärseiten nicht weiter ausbauen |
| **leadgenerierung photovoltaik** | /b2b-solar-leads/ (24/46,7) · Money-Page | Money-Page besitzt die Query → Anker „Leadgenerierung Photovoltaik" auf Money-Page |
| **leads kaufen / leadgenerator solar** | /b2b-solar-leads/ ↔ /solar-leads-kaufen-alternative/ | Intents trennen: b2b = Gewerbe/Transactional, kaufen-alternative = Commercial Investigation (Portal-Vergleich) |
| **server side tracking** | /server-side-tracking-b2b/ (238) ↔ /server-side-tracking-gtm/ (4) | differenzieren + interlinken (Polish #6) |

## Löschen? — Verdikt

**Kein Löschen der jungen Solar-/WP-Cluster-Seiten.** Kannibalisierung ≠ Überflüssigkeit: die Seiten
bedienen unterschiedliche Intents und stützen die Money-Pages über interne Links. Der 07-03-Report empfahl
aus denselben Gründen „Keine Seite löschen". Insbesondere **NICHT** `/lead-funnel-solar/` (Pillar-Page,
struktureller Hub) oder `/qualifizierte-pv-anfragen/` (junges Support-Asset) de-indexieren/301'en — das
zerstört interne Linkkraft. Einzig echter Konsolidierungs-Kandidat: das Server-Side-Paar (Merge/Abgrenzung,
kein Blind-Delete). Optional `noindex` für echte Altlasten mit ~0 Signal (`/ga4-tracking-setup/`,
`/design-ist-mehr-als-aesthetik/`). `/e3-new-energy/` ist bereits sauberer Legacy-301.

---

## Repo-Aufgaben (umgesetzt)

- **Checkfox-Snippet** (`inc/blog-provider-posts.php`): Title in Frage-Format, Description auf ≤160 Z. gekürzt.
- **„PV-Termine im B2B"-Abschnitt** (`page-b2b-solar-leads.php`): neuer Compare-Abschnitt „gekauft vs. selbst erzeugt".
- **Anchor-Disziplin** (`page-solar-leads-kaufen-alternative.php`): kontextueller interner Link
  „exklusive Wärmepumpen-Leads" → `/waermepumpen-leads/`.
- **b2b-solar-leads Title/Meta** (`inc/seo-meta.php`, Forced-Map): Title/Description auf „pv termine b2b"
  geschärft — `Photovoltaik B2B Leads & PV-Termine: Gewerbe statt Masse`. **Repo-Edit statt WP-Admin.**

## Manuelle WordPress-Aufgaben (nur diese — Rest ist Repo)

- **Checkfox live schalten:** Der neue Snippet liegt in der Seed-Registry → Beitrag im WP-Admin einmal
  neu speichern (oder Seeder laufen lassen), sonst bleibt die alte Version live.
- **Homepage-Daten-Hygiene:** View-Source auf hasimuener.de prüfen (Titel wird per Code erzwungen, sollte
  bereits „Anfrage-Systeme für Solar & Wärmepumpe…" sein). SCF-Feld leer lassen ist korrekt.
- **Server-Side-Konsolidierung:** Entscheidung Abgrenzung vs. 301 für `/server-side-tracking-gtm/`
  (WP-DB-Post, nicht Repo); USP-Inhalte in `/server-side-tracking-b2b/` übernehmen.
- **Nach Deploy:** GSC-Recrawl der geänderten URLs (Checkfox, b2b-solar-leads) anfordern.

## Repo-Follow-ups (kein Admin nötig, optional als nächster Schritt)

- **`/wordpress-agentur-hannover/` (Rückschritt):** Positions-Problem, kein Snippet — Hebel sind interne Links.
  Title steht bereits code-seitig in der Forced-Map (`seo-meta.php:207-209`). Nächster Repo-Schritt bei Bedarf:
  kontextuelle interne Links von thematisch nahen Seiten auf den Hannover-Hub (CWV-neutral).
- **`/waermepumpen-leads/`:** Snippet ok; Ownership kommt aus der Anker-Disziplin (bereits gesetzt).

## Wirkungsmessung

Nächster GSC-Export in **2–4 Wochen**. Erfolgskriterien: CTR-Lift auf Checkfox + b2b-solar-leads
(Seite-1-Rankings holen Klicks), Position von `/waermepumpen-leads/` steigt, `wärmepumpen leads`
konsolidiert sich auf eine Seite, Hannover zurück Richtung Seite 1.

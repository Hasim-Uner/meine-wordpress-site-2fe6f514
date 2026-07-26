# Seiten-Inventar & Ownership-Audit (2026-07-26)

Anlass: Prüfung, ob das öffentlich diskutierte Muster „KI produziert Seiten schneller,
als jemand sie steuern kann" auf dieses Repo zutrifft — und ob Seiten gelöscht werden
sollten.

Datenbasis: `blocksy-child/` (Templates, `inc/seo-meta.php`, `inc/helpers.php`,
`inc/wgos/wgos-cluster-pages.php`), `seo-research/2026-07/data/gsc/` (Export 28 T,
Stand 2026-07-03) und die Vorreports `gsc-abgleich.md` / `gsc-verlauf-2026-07-20.md`.
Keine geschätzten Volumina, keine WP-Datenbank-Einsicht.

## Methodischer Vorbehalt

Aus dem Repo ist **nicht** ableitbar, welche WordPress-Seiten tatsächlich veröffentlicht
sind. Nur `page-<slug>.php` ohne `Template Name:`-Header ist per WP-Konvention an eine
URL gebunden; Dateien mit `Template Name:` sind im Editor zuweisbar und ihr Dateiname
sagt nichts über die URL. Von 45 Template-Dateien sind **9 URL-gebunden** und
**34 zuweisbar** (plus `front-page.php` / `home.php`).

Das ist keine Formalie: Die bisherige Orphan-Prüfung hat diese Unterscheidung nicht
gemacht und deshalb 12 Orphans für Pfade gemeldet, die es nie gab (`/about/`,
`/template-portal/`, `/home/` …). Korrigiert in diesem Branch → 2 verbleibende, beide
erklärbar.

---

## Critical

**1. Das Problem ist nicht Überproduktion, sondern Ertrag pro Fläche.**

28 Tage, gesamte Domain: **1.299 Impressionen, 4 Klicks.** Nur **18 URLs** haben
überhaupt ein Signal. Die Verteilung:

| URL | Impr. (28 T) | Klicks |
|---|---:|---:|
| /wordpress-agentur-hannover/ | 715 | 0 |
| /server-side-tracking-b2b/ | 288 | 0 |
| /b2b-solar-leads/ | 207 | 0 |
| /solar-leads-kaufen-alternative/ | 34 | 1 |
| / | 16 | 1 |
| **/solar-waermepumpen-leadgenerierung/ (Money-Page)** | **11** | **0** |
| restliche 12 URLs | je ≤ 8 | 0 |

Die Money-Page ist mit 1.362 Zeilen die größte Seite des Themes und holt 11
Impressionen — weniger als 1 % der Domain-Sichtbarkeit. Drei Support-Seiten sind
sichtbarer als das Ziel, auf das sie einzahlen sollen.

Das ist die Umkehrung des LinkedIn-Vorwurfs: nicht zu viele Seiten pro Thema, sondern
**zu wenig Nachfrage pro gebauter Seite**. Löschen behebt das nicht — es ist ein
Nachfrage- und Positions-Problem, kein Bestandsproblem.

## High Leverage

**2. Ownership ist ab jetzt prüfbar, nicht nur dokumentiert.**

Die Regeln existierten bereits (`seo-content-system.md` §8, `BRAND_AND_COPY.md`), aber
nur als Fließtext. Neu in diesem Branch:

- `docs/seo/query-ownership.csv` — 21 Queries auf 13 URLs, jede Zeile mit Belegstelle.
- `agents/skills/seo-agent/scripts/intent-gate.sh` — `check` blockiert eine neue Seite,
  deren Ziel-Query bereits einer anderen URL gehört (Exit 1); `audit` prüft die
  Registry auf Doppel-Owner und nahe Intentionen.

Der Audit-Lauf ist grün. Die drei bekannten Konflikte aus `gsc-verlauf-2026-07-20.md`
(`wärmepumpen leads`, `leadgenerierung photovoltaik`, `server side tracking`) sind als
Owner-Zeilen abgebildet; die bewusst getrennten Paare stehen in `distinct_from` — eine
Entscheidung, die man jetzt sehen kann, statt sie jedes Mal neu zu treffen.

**3. Tote Template-Stubs — die einzigen echten Löschkandidaten.**

`page-cro.php`, `page-cwv.php` und `page-seo.php` sind 11-Zeilen-Stubs, die
`page-wgos-pillar.php` einbinden. Deren Cluster-Daten wurden bereits entfernt
(`inc/wgos/wgos-cluster-pages.php:26-29`), und die zugehörigen öffentlichen Pfade
`/conversion-rate-optimization/`, `/core-web-vitals/`, `/seo/` liefern **410 Gone**
(`nexus_get_retired_gone_paths`, `inc/helpers.php:1412-1428`).

Ergebnis: drei im Editor wählbare Templates, deren Backing-Daten weg sind und deren
Ziel-URLs bewusst tot sind. Würde man eines zuweisen, rendert es den leeren
Fallback-Zweig. **Kein SEO-Risiko beim Löschen, aber ein WP-Check vorher** (siehe
Manuelle Aufgaben) — deshalb in diesem Branch noch **nicht** gelöscht.

## Polish

**4. `link-map.sh` meldete erfundene Pfade.** Slug-Ableitung aus dem Dateinamen ohne
`Template Name:`-Prüfung. Behoben: Ausgabe trennt jetzt URL-gebunden von zuweisbar,
Orphan-Prüfung läuft nur noch auf URL-gebundenen Seiten. Ein Audit, das falsche Orphans
meldet, erzeugt genau die Aufräumaktionen, die es verhindern soll.

**5. `route.sh` empfahl `sh <script>`,** obwohl alle Skripte `set -o pipefail` nutzen —
unter dash bricht das sofort ab. Auf `bash` korrigiert.

**6. `/wgos-pillar/` ist slug-gebunden benannt,** obwohl die Datei ein reiner Renderer
ist. Kosmetisch; kein Handlungsdruck, aber die Orphan-Warnung bleibt bis zur Umbenennung.

---

## Löschen? — Verdikt

**Keine Content-Seite löschen.** Das Verdikt aus `gsc-verlauf-2026-07-20.md` hält der
erneuten Prüfung stand: die Solar-/WP-Cluster-Seiten bedienen unterschiedliche Intents
und tragen interne Linkkraft. Bei 4 Klicks in 28 Tagen ist Bestandsabbau nicht der
Engpass.

Löschbar ist **Code, nicht Content**: die drei Stubs aus Punkt 3.

Offener Konsolidierungsfall bleibt unverändert das Server-Side-Paar
(`/server-side-tracking-b2b/` 288 Impr. vs. `/server-side-tracking-gtm/` 8 Impr.) —
Abgrenzung oder Merge, kein Blind-Delete. Der GTM-Beitrag liegt in der WP-DB, nicht im Repo.

## Antwort auf die Ausgangsfrage

Das im LinkedIn-Beitrag beschriebene Muster — unkontrollierter Output, doppelte
Suchintentionen, niemand weiß, welche Seite ranken soll — trifft hier **nicht** zu.
Kannibalisierung existiert, ist aber seit dem 07-03-Sprint benannt, gemappt und mit
Ownership-Entscheidungen versehen.

Die reale Lücke war eine andere: Die Ownership-Regel stand in Markdown und wurde von
keinem Schritt erzwungen. Genau diese Lücke schließt das Gate. Der zweite Befund ist
unbequemer und hat mit KI nichts zu tun — für den Umfang des Bestands ist die
Nachfrage zu klein.

## Manuelle WordPress-Aufgaben

- **Vor dem Löschen der Stubs prüfen:** Sind `CRO Landing`, `Core Web Vitals` oder
  `SEO Landing (Hannover)` im Editor noch einer Seite zugewiesen? Wenn ja: Seite auf ein
  gepflegtes Template umstellen oder in den 410-Pfad überführen, dann Datei löschen.
- **Offen aus dem Vorreport:** Checkfox-Beitrag einmal neu speichern (Snippet liegt in
  der Seed-Registry), Homepage-ACF-Feld leeren, Server-Side-Konsolidierung entscheiden.

## Agent-/Repo-Aufgaben

- `intent-gate.sh check` vor jeder neuen Seite (in `seo-agent` und
  `pillar-cornerstone-writer` als Hard Rule verankert).
- Registry bei jedem neuen Owner ergänzen — mit Belegstelle.
- Nächster GSC-Export: prüfen, ob die Money-Page gegenüber ihren Support-Seiten aufholt.
  Wenn nicht, ist die Frage nicht „welche Seite löschen", sondern ob die Money-Page die
  richtige Query besitzt.

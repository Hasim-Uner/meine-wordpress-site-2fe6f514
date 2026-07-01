# TCO-Pillar Go-Live & Verifikation

Stand: 2026-07-01 (Rewrite auf Handwerker-Klartext, neue Überschrift, Slug-Wechsel + 301, Rechner entschlackt). Verantwortlich: Haşim Üner (Go-Live im WordPress-Editor, außerhalb des Repos — kein Admin-Zugang, siehe `docs/decisions/0006-kein-admin-zugang-policy.md`).

> **Zweck.** Der Beitrag *„Solar-Leads kaufen: Warum die billigen Anfragen am Ende die teuersten sind"*
> (neuer Slug `solar-leads-kaufen-lohnt-sich`, alter Slug `photovoltaik-leads-tco-rechnung` → 301)
> ist das **kanonische Off-Page-Anker-Asset** für die SHK-/Solar-Backlink-Strecke (u. a. SBZ). Bevor
> ein Pitch rausgeht, muss dieser Pillar live, indexiert und sauber sein. Dieses Dokument hält fest,
> was repo-seitig verifiziert wurde und welche Schritte manuell im WordPress-Editor + Search Console
> folgen.
>
> **Update 2026-07-01:** Der Beitrag wurde von Marketer-Sprache (TCO/CPO/CPL) auf Handwerker-Klartext
> umgeschrieben, die Überschrift auf Such-/Schmerz-Ebene gebracht, der Slug auf `solar-leads-kaufen-lohnt-sich`
> geändert (301 via `hu_blog_pillar_redirect_legacy_slugs`), und der CPO-Rechner von 18 auf 3 sichtbare
> Felder pro Seite reduziert. Go-Live läuft über den Seed-Version-Bump (`2026-07-01-1`).

## 1. Publish-Readiness-Audit (repo-seitig verifiziert)

| Prüfpunkt | Status | Beleg |
| --- | --- | --- |
| Tiefe & Argumentation (CPL→CPO, Mehrfachverkauf, GHL-Falle, Tracking, E3, §248 HGB) | ✓ | `assets/content/blog/photovoltaik-leads-tco-rechnung.md` |
| Alle 7 internen Links lösen auf echte Public-URLs auf | ✓ | gegen `llms.txt` geprüft |
| Externe Links gültig (HGB, HostPress [als „Werbung" deklariert], Raidboxes) | ✓ | Body-Quellenblock |
| SEO-Title 53 Zeichen (≤ 60) · Meta-Description ~148 Zeichen (≤ 155) | ✓ | Seed `blog-pillar-posts.php` |
| BlogPosting-Schema (headline, Autor, datePublished, Bild, publisher) | ✓ | `org-schema.php` (zentral, ab Z. 1255) |
| Autoren-E-E-A-T (Byline, Bio, Rolle, Avatar) | ✓ | `single.php` (ab Z. 383) |
| CPO-Rechner-Shortcode `[hu_cpo_calculator]` registriert | ✓ | `inc/cpo-calculator.php` |
| Hero-Bild vorhanden | ✓ | `assets/img/blog/photovoltaik-leads-kaufen-alternative-hero.png` |
| Inline-CTA-Asides rendern (Konverter erkennt „**Stopp." / „**Marktcheck-Filter:") | ✓ | `blog-pillar-posts.php` (Z. 108) |

**Repo-Fixes in diesem Durchgang** (Publish-Pack-Header an den real laufenden Seed angeglichen):
- Kategorie: jetzt beide Kategorien (Leadgenerierung + Solar-/Wärmepumpen Anfrage-Systeme).
- Hero-Bild-Pfad: korrigiert auf `assets/img/blog/...` (Seed-Wahrheit) statt Draft-Pfad.
- Status: „Entwurf" → „Freigegeben zur Veröffentlichung" mit Verweis auf dieses Dokument.

## 2. Go-Live (manuell im WordPress-Editor)

1. **Live-Status prüfen.** Ist der Beitrag bereits aus dem Pillar-Seed (`blog-pillar-posts.php`,
   Seed-Version `2026-05-25-2`) veröffentlicht? Falls ja → Schritt 2–4 als Kontrolle. Falls Entwurf
   → auf **Veröffentlicht** setzen.
2. **Beitragsbild** setzen: `photovoltaik-leads-kaufen-alternative-hero.png`, Alt-Text exakt wie im
   Seed (`featured_alt_text`).
3. **Kategorien & Tags** gegen den Seed gegenprüfen (2 Kategorien, 6 Tags).
4. **Autor = Haşim Üner** zuweisen (E-E-A-T-Schema zieht den Beitragsautor). Autor-Bio im
   Profil muss gefüllt sein (Byline rendert sie).

## 3. Indexierung (Search Console)

- URL-Prüfung für die **neue** URL (`…/solar-leads-kaufen-lohnt-sich/`) → **Indexierung beantragen**.
- Alte URL (`…/photovoltaik-leads-tco-rechnung/`) prüfen: muss **301** auf die neue URL liefern.
- BlogPosting- + (optional) FAQ-Schema im **Rich-Results-Test** gegenprüfen.
- In der Sitemap auftauchen lassen, intern bereits verlinkt (Cluster-Quervernetzung vorhanden).

## 4. Optionale Verbesserung — FAQPage-Schema

Der Pillar hat einen 5-teiligen FAQ-Block, aber der Blog-Single-Pfad in `org-schema.php` emittiert
**kein** FAQPage-Schema (die Money-Page-Templates tun das). Nachrüstbar **zentral** in `org-schema.php`,
analog zu den `page-*.php`-Vorlagen.

> **Ehrliche Erwartung:** Google hat FAQ-Rich-Results 2023 für nahezu alle Nicht-Behörden-/
> Nicht-Gesundheitsseiten abgeschaltet. Der Nutzen heute liegt v. a. in sauberer strukturierter
> Daten + AI-Overviews/LLM-Parsing, nicht in sichtbaren Sternchen-Snippets. Daher: nice-to-have,
> kein Go-Live-Blocker. Separat umsetzen, wenn gewünscht.

## 5. Brücke zur SBZ-/Off-Page-Strecke

Dieser Pillar ist der **Byline-Ziel-Link**, nicht der Pitch-Text. Reihenfolge (siehe auch
`docs/seo/offpage-authority-playbook.md`, Phase 2.1):

1. Pillar live + indexiert (dieses Dokument).
2. **Eigenständige, ent-marketete SBZ-Redaktionsfassung** als separater Draft — ohne Marktcheck-CTA,
   ohne HostPress-Affiliate, Link nur in der Autoren-Byline. Niemals den kanonischen Pillar 1:1 an
   ein Fachmedium geben (Exklusivität + Duplicate-Vermeidung).
3. Datengetriebener Pitch an die **Fachredaktion** (Heizung/Solar) zuerst; Anzeigenverkauf nur, falls
   „nur bezahlt möglich". Aktuelles Impressum/Mediadaten selbst verifizieren (Redaktionen wechseln).

# Artikel-Bausteine — Shortcodes und Klassen für Fachartikel

Wiederkehrende Elemente eines Cornerstone-Artikels. Code:
`blocksy-child/inc/editorial-bausteine.php`, Gestaltung:
`assets/css/editorial-bausteine.css`, Verhalten:
`assets/js/editorial-bausteine.js`. Vorlage im Einsatz:
`blocksy-child/assets/content/blog/website-relaunch.html`.

## Grundregeln

- **Additiv.** CSS und JS laden nur auf Beiträgen, die einen Shortcode unten
  oder eine `hu-…`-Klasse unten enthalten. Alle anderen Beiträge bleiben
  unverändert.
- **Keine Zahl als Literal.** Preise über `[hu_price key="…"]`, fremde
  Marktzahlen über `[hu_markt key="…"]`, Fallzahlen im Partial über
  `hu_e3_metric()`. Regel und Kanon: `docs/standards/BRAND_AND_COPY.md`.
- **Titel, Vorspann, Titelbild, Autor und Lesezeit** liefert der Reader
  (`template-parts/single-reader.php`), nicht der Inhalt. Der Inhalt beginnt
  mit `[hu_kurz]`.
- **Überschriften im Inhalt** sind H2 (Kapitel, mit `id`) und H3. Die
  Bausteine selbst setzen keine Überschriften, damit das Inhaltsverzeichnis
  nur die Gliederung des Artikels zeigt.
- **FAQ-Schema.** Notiz, Abbildung, Fall und Quellen tragen
  `data-hu-schema-skip`; `hu_extract_faq_schema_entities_from_content()`
  entfernt sie vor `wp_strip_all_tags()`. Eine Notiz direkt unter einer
  Frage-Überschrift ist deshalb unbedenklich. Übrige Regeln:
  `references/aeo-answer-patterns.md`.
- **Gestaltung** nur mit Tokens aus `assets/css/system.css`, dunkle Flächen
  über `.tafel`. Kein neuer Farbwert, Radius höchstens 3 px, keine Schatten.
- **Raster.** Ab 1280 px bekommt der Reader auf diesen Beiträgen eine
  Marginalspalte. Notizen schweben hinein, breite Elemente reichen hinein.
  Darunter steht alles im Satzspiegel.

## Shortcodes

### `[hu_kurz]` — Kurz beantwortet

Nummerierte Kernaussagen auf `--zone`, ganz oben im Artikel. Optional
`label="…"` (Standard „Kurz beantwortet“).

```html
[hu_kurz]
<ol>
<li>Erste Kernaussage in einem Satz.</li>
<li>Zweite Kernaussage.</li>
</ol>
[/hu_kurz]
```

### `[hu_notiz label="…"]…[/hu_notiz]` — Randnotiz

Quelle, Begriff, Datenlage, Einstellung. Ab 1280 px in der Marginalspalte
(`float:right` mit negativem Rand), darunter als Block im Fluss. Inline-HTML
(`<b>`, `<i>`, `<a>`) ist erlaubt. Die Notiz schwebt neben dem Element, das
**nach** ihr steht: für eine Quelle neben einem bestimmten Absatz die Notiz
vor den Absatz setzen.

```html
[hu_notiz label="Quelle"]Google Search Central, <a href="https://developers.google.com/search/docs/crawling-indexing/site-move-with-url-changes" target="_blank" rel="noopener">Websites mit URL-Änderungen verschieben</a>.[/hu_notiz]
```

### `[hu_abb id="…" nr="1"]` — Abbildung

Lädt `template-parts/editorial/abb-{id}.php`. `nr` ist die Nummer in
„Abb. 1“. Vorhanden: `relaunch-kette` (acht Stationen, fünf Bruchstellen,
Rückkanal 05 → 01; waagerecht ab 820 px Abbildungsbreite, darunter senkrecht
per Container Query; zeichnet sich einmal beim ersten Sichtkontakt).

```html
[hu_abb id="relaunch-kette"]
```

Neue Abbildung: Partial `abb-{id}.php` anlegen, `$args['dom_id']` und
`$args['nr']` übernehmen, Wurzel ist eine `<figure>` mit `figcaption` als
erstem Kind. Keine Zahl als Literal.

### `[hu_fall id="…"]` — Fall aus der Praxis

Lädt `template-parts/editorial/fall-{id}.php`. Text im Partial, Bezeichnung
und jede Zahl aus dem Kanon, Link über `hu_e3_canon()['url']`. Vorhanden:
`pv-anfragesystem` (Bezeichnung `HU_E3_CASE_LABEL_ACCUSATIVE`, Zahlen
`cpl_before`, `cpl_after`, `lead_count`, `timeframe` im Dativ,
`sales_conversion`; `lead_conversion` bewusst nicht).

```html
[hu_fall id="pv-anfragesystem"]
```

### `[hu_pruefliste id="…" titel="…"]<ol>…</ol>[/hu_pruefliste]` — Prüfliste

Ohne JS eine normale nummerierte Liste. Mit JS: Checkboxen, „x von n
geprüft“, Fortschrittslinie, „Liste kopieren“ (Zwischenablage, sonst
Markieren) und „Zurücksetzen“. Nichts wird im Browser gespeichert.

```html
[hu_pruefliste id="relaunch-livegang" titel="Prüfliste Livegang"]
<ol>
<li>Kein <code>noindex</code> im Quelltext.</li>
<li>Jedes Formular ist einmal von außen abgeschickt.</li>
</ol>
[/hu_pruefliste]
```

### `[hu_abschluss …]Zusatzzeile[/hu_abschluss]` — Abschluss-Tafel

Dunkle Tafel am Artikelende. Intro, Button-Text, „Sie schicken / Sie
bekommen“ und Antwortzeit kommen aus `hu_first_assessment_text()` und
`hu_response_promise()`. Primär `hu_first_assessment_url()`, sekundär
`hu_get_contact_intake_url( 'project', $fokus )`. Ist der Versuch
Ersteinschätzung aus, bleibt nur die Projektanfrage als primärer Button.

| Attribut | Bedeutung |
|---|---|
| `variante` | `ersteinschaetzung` (einzige Variante) |
| `fokus` | Kontakt-Fokus der Projektanfrage, etwa `relaunch`, `tracking`, `conversion` |
| `kicker`, `titel` | Kopfzeile und Titel der Tafel |
| `projekt_label` | Text des Projekt-Buttons |
| `track` | Präfix der Hooks: `{track}_close_ersteinschaetzung`, `{track}_close_project` |

```html
[hu_abschluss variante="ersteinschaetzung" fokus="relaunch" kicker="Vor dem Relaunch" titel="Schicken Sie mir Ihre URL, bevor jemand das Layout anfasst." projekt_label="Relaunch-Projekt anfragen" track="blog_relaunch"]Für das vollständige Inventar gibt es den Übernahme-Check für [hu_price key="freelancer_takeover_check_price"] netto.[/hu_abschluss]
```

Neue Hooks in `docs/architecture/CONVERSION_ROUTING.md` eintragen.

### `[hu_quellen]<ol>…</ol>[/hu_quellen]` — Quellen

Nummerierte, kleine Quellenliste am Artikelende.

```html
[hu_quellen]
<ol>
<li>Google-Analytics-Hilfe, <a href="https://support.google.com/analytics/answer/7667196?hl=de" target="_blank" rel="noopener">Datenaufbewahrung</a>.</li>
</ol>
[/hu_quellen]
```

### Kanon-Shortcodes im Fließtext

| Shortcode | Quelle |
|---|---|
| `[hu_price key="freelancer_website_price"]` | `hu_pricing_canon()` — Schlüssel mit `price` werden als Betrag formatiert |
| `[hu_markt key="gartner_b2b_rep_share"]` | `hu_b2b_market_figures()` in `inc/canon/market-canon.php`; Quelle als `[hu_notiz]` daneben |

## Klassen für reines HTML

| Klasse | Element | Wirkung |
|---|---|---|
| `.hu-leitsatz` | `<p>` | Kernsatz in Display-Schrift |
| `.hu-aufgaben` | `<ol>` | nummerierte Aufgaben, `<strong>` am Anfang wird zur Zeilenüberschrift |
| `.hu-praxis` | `<aside>` mit `<p class="hu-praxis__label">` | Erfahrungsabsatz mit Mono-Label |
| `.hu-verweis` | `<p>` direkt vor einer H3 | Rückbezug auf eine Abbildung, z. B. „Abb. 1 · Station 01“ |
| `.hu-phase` | `<span>` in einer H3 | Zeitmarke, z. B. „T − 4 Wochen“ |
| `.hu-liste` | `<ul>` | Liste mit Strich und Haarlinien |
| `.hu-tabelle` | `<div>` um die Tabelle | Tabellenhülle; `--breit` reicht ab 1280 px in die Marginalspalte |
| `.hu-tabelle--stapel` | `<table>` | Zeilen stapeln unter 700 px Tabellenbreite; jede `<td>` außer der ersten trägt `data-l` mit der Spaltenüberschrift |
| `.hu-tabelle--risse` | `<table>` | letzte Spalte als Bruchstelle mit Stempelpunkt |
| `.hu-tabelle--gruppen` | `<table>` | erste Spalte trägt Gruppenmarken |
| `.hu-gruppe--traegt`, `--stuetzt`, `--ballast` | `<span class="hu-gruppe hu-gruppe--…">` | Marke: gefüllt, offen, gestrichelt |

```html
<p class="hu-leitsatz">Ein Ranking ist kein Vermögenswert, eine Anfrage schon.</p>

<p class="hu-verweis">Abb. 1 · Station 01</p>
<h3>Tragende Seiten ohne Weiterleitung</h3>

<h3><span class="hu-phase">T − 4 Wochen</span> Vier Wochen vorher</h3>
<ul class="hu-liste">
<li>Die Daten der Search Console exportieren.</li>
</ul>

<div class="hu-tabelle hu-tabelle--breit">
<table class="hu-tabelle__t hu-tabelle--stapel hu-tabelle--gruppen">
<thead><tr><th scope="col">Gruppe</th><th scope="col">Woran Sie sie erkennen</th></tr></thead>
<tbody>
<tr><td><span class="hu-gruppe hu-gruppe--traegt">Trägt</span></td><td data-l="Woran Sie sie erkennen">bringt Anfragen</td></tr>
</tbody>
</table>
</div>
```

## Neuer Artikel auf den Bausteinen

1. Inhalt als HTML nach `blocksy-child/assets/content/blog/{slug}.html`.
2. Seed nach dem Muster von `inc/article-website-relaunch.php`: einmal
   versioniert, danach im Editor gepflegt, fremder Inhalt am selben Slug wird
   nie überschrieben. FAQ-Flag, SEO-Titel, Meta-Description und
   `og_image` samt SCF-Feldreferenz über `meta_input` bzw. nach dem Titelbild.
3. Prüfen: keine Zahl als Literal im Inhalt und in Partials (`rg`),
   `scripts/canon-guard.sh`, FAQPage-Schema lesbar und ohne Notiztext.

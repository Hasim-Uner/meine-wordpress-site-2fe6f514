# Auftrag: Blogartikel „Website-Relaunch“ und Artikel-Bausteine

Stand 2026-09-24 · Auftraggeber Haşim · erstellt von Argos

## Worum es geht

Ein neuer Cornerstone-Artikel unter `/website-relaunch/`. Er ist zugleich die **Vorlage für alle künftigen Artikel**. Deshalb entstehen die wiederkehrenden Elemente als allgemeine Bausteine und nicht als Sonderlösung für diese eine Route.

Primärer Skill laut `AGENTS.md`: `editorial-seo`, delegiert an `pillar-cornerstone-writer`.

## Dateien in diesem Ordner

| Datei | Rolle |
|---|---|
| `artikel.html` | **Freigegebene Copy** als post_content, mit Shortcodes an allen Stellen, an denen Kanon-Werte oder Bausteine stehen. Nicht umformulieren. |
| `referenz-vorschau.html` | **Gestaltungsreferenz**, eigenständiges HTML mit eingebetteten Schriften, im Browser öffnen. Gilt für das Aussehen, nicht für die Struktur. Kopf, Leiste und Fuß darin sind Attrappe, sie kommen aus dem Theme. |
| `website-relaunch-hero.png` | Titelbild 2400 × 1260 px. Dient als Beitragsbild, Titelbild im Artikel und og:image. |

## Durchlauf 1 (ein PR)

### A. Bausteine, allgemein und wiederverwendbar

Ein neues Modul `blocksy-child/inc/editorial-bausteine.php` mit den Shortcodes, dazu `assets/css/editorial-bausteine.css` und `assets/js/editorial-bausteine.js`. Die Assets laden nur auf Beiträgen, die einen der Bausteine enthalten (`has_shortcode` oder Klassenprüfung).

| Shortcode | Was er ausgibt |
|---|---|
| `[hu_kurz]…[/hu_kurz]` | Block „Kurz beantwortet“: Mono-Label, nummerierte Aussagen, Fläche `--zone` |
| `[hu_notiz label="…"]…[/hu_notiz]` | Marginalie. Ab ca. 1280 px schwebt sie in den freien Raum rechts neben dem Satzspiegel (`float:right` mit negativem Rand), darunter steht sie als Block im Fluss. |
| `[hu_abb id="…"]` | lädt `template-parts/editorial/abb-{id}.php`. Die erste Abbildung ist `relaunch-kette`, portiert aus der Referenz („Abb. 1 · Die Kette …“ auf `.tafel`, acht Stationen, fünf Bruchstellen, Rückkanal 05 → 01, unter 820 px Containerbreite senkrecht per Container Query) |
| `[hu_fall id="…"]` | Fall-Baustein. Text im Partial, **alle Zahlen und die Bezeichnung aus `inc/canon/e3-proof-canon.php`** (siehe unten). Der Link geht auf `hu_e3_canon()['url']`. |
| `[hu_pruefliste id="…" titel="…"]<ol>…</ol>[/hu_pruefliste]` | Ohne JS eine normale nummerierte Liste. Mit JS: Checkboxen, Zähler „x von n geprüft“, Fortschrittslinie, „Liste kopieren“ (Clipboard mit Fallback auf Markieren) und „Zurücksetzen“. Kein localStorage. |
| `[hu_abschluss variante="ersteinschaetzung" fokus="…" kicker="…" titel="…" projekt_label="…" track="…"]Zusatzzeile[/hu_abschluss]` | Abschluss-Tafel. Intro, Button-Text, „Sie schicken / Sie bekommen“ und Antwortzeit kommen aus `hu_first_assessment_text()` und `hu_response_promise()`. Primär `hu_first_assessment_url()`, sekundär `hu_get_contact_intake_url( 'project', $fokus )`. `data-track-action` lautet `{track}_ersteinschaetzung` bzw. `{track}_projekt`; die Benennung an die bestehende Konvention angleichen. |
| `[hu_quellen]…[/hu_quellen]` | Quellenliste am Artikelende, nummeriert, klein |

Klassen für reines HTML im Inhalt: `.hu-leitsatz`, `.hu-aufgaben`, `.hu-praxis`, `.hu-verweis`, `.hu-phase`, `.hu-liste`, `.hu-tabelle` (`--breit`, `--stapel` mit `data-l`-Labels unter 700 px, `--risse`, `--gruppen`) und `.hu-gruppe--traegt/--stuetzt/--ballast`.

Gestaltung:
- Ausschließlich Tokens aus `assets/css/system.css`: Papier, Zone, Tinte, Grau, Matt, Stempel, Haar und Strich, die Abstandsskala `--s0` bis `--s6`, die Mono-Stufen und `.tafel` für dunkle Flächen.
- Keine neuen Farbwerte, kein Dark Mode, Radius höchstens 3 px, keine Schatten.
- Bewegung gibt es nur an zwei Stellen: Die Kette zeichnet sich einmal, wenn sie ins Bild kommt, und das aktive Kapitel wird markiert. Beides ohne JS sofort fertig und bei `prefers-reduced-motion` ruhig.

**FAQ-Schema:** Die Ausgabe von `[hu_notiz]`, `[hu_abb]`, `[hu_fall]` und `[hu_quellen]` darf nie in einer `acceptedAnswer` landen. `hu_extract_faq_schema_entities_from_content()` wendet `do_shortcode` an. Deshalb bekommen die Bausteine ein Marker-Attribut, und der Parser entfernt so markierte Elemente vor `wp_strip_all_tags()`. Betroffen ist schon die H2 „Was ist eine B2B-Website?“, denn nach ihr folgt eine Notiz.

### B. Kanon

- `hu_pricing_canon()` bekommt `'freelancer_website_price' => HU_FREELANCER_WEBSITE_MIN`. Der Schlüssel enthält „price“, damit `[hu_price]` ihn als Betrag formatiert.
- **Fall-Baustein `pv-anfragesystem`:** Die Bezeichnung kommt aus `HU_E3_CASE_LABEL_ACCUSATIVE`, die Zahlen aus `hu_e3_metric()`: `cpl_before`, `cpl_after`, `lead_count`, `timeframe` (Dativ-Fassung) und `sales_conversion`. **Nicht verwenden:** `lead_conversion` (12 %). Text wörtlich:

  > **Aus der Praxis** · Die Kette, Station für Station gebaut
  >
  > Für einen {Bezeichnung Akkusativ} habe ich diese Kette aufgebaut. Zugekaufte Anfragen waren dort ein wesentlicher Teil der Leadversorgung, zu {cpl_before} pro Anfrage. Neu entstanden Landingpages für konkrete Bedürfnisse, ein mehrstufiges Formular, das Bedarf und Objekt vor dem ersten Gespräch erfasst, eine strukturierte Übergabe ins CRM und ein Rückkanal, über den die Conversion-Signale zurück in Messung und Kampagnensteuerung flossen.
  >
  > Zahlenreihe: {cpl_before} → {cpl_after} · Kosten pro Anfrage | {lead_count} · qualifizierte Anfragen in {timeframe Dativ} | {sales_conversion} · Abschlussquote auf Auftrag
  >
  > Das war kein Relaunch, sondern ein Neuaufbau. Er zeigt trotzdem, worum es hier geht: Das Ergebnis kam nicht aus einem einzelnen Hebel, sondern aus Übergängen, die jeweils eine Aufgabe und einen Messpunkt hatten. An der Abschlussquote hatte der Vertrieb des Betriebs einen wesentlichen Anteil. → Zur Fallstudie

  Keine Begründung für die Anonymisierung, kein Firmenname, kein Satz wie „Belege im Gespräch“.
- **Fremdzahl 17 % (Gartner):** Nach `BRAND_AND_COPY.md` gehört sie in `inc/canon/market-canon.php`. Den Scope-Kommentar der Datei (bisher nur Solar/SHK) um allgemeine B2B-Marktzahlen erweitern, die Zahl mit Quelle anlegen und über `[hu_markt key="gartner_b2b_rep_share"]` ausgeben. Die sichtbare Quelle steht als `[hu_notiz]` direkt daneben.

### C. Beitrag anlegen

- Den Inhalt aus `artikel.html` nach `blocksy-child/assets/content/blog/website-relaunch.html` legen. Seeden nach dem Muster von `wordpress-projekte-auslagern` (`inc/article-agency-outsourcing.php`): einmal versioniert, danach im Editor gepflegt, andere Beiträge unberührt.
- Metadaten:
  - Titel: `Website-Relaunch: Was Ihre B2B-Website verlieren darf – und was nicht`
  - Slug: `website-relaunch`
  - SEO-Title: `Website-Relaunch: Was verloren gehen darf – und was nicht`
  - Meta-Description: `Ein Relaunch darf Rankings kosten, die nie eine Anfrage gebracht haben. Was er nie kosten darf und wie Sie es vorher messen: Methode und Prüfliste.`
  - Excerpt (Vorspann im Reader): `Ein Relaunch darf Rankings kosten, die nie eine Anfrage gebracht haben. Er darf nie die Anfragen selbst kosten und auch nicht die Messung, an der Sie sehen, woher sie kommen. Dieser Artikel zeigt, wie Sie eine B2B-Website vor dem Relaunch in ihre kleinsten Einheiten zerlegen, damit klar ist, was geschützt werden muss und was weg kann.`
  - Kategorie: Dossier `cro`
  - Tags: Website-Relaunch, WordPress Relaunch, Relaunch Checkliste, Conversion-Tracking, Weiterleitungen, Lastenheft
- Titelbild: `assets/img/blog/website-relaunch-hero.png`, Alt-Text `Titelbild: Was ein Relaunch verlieren darf – und was nicht. Darunter die Kette von der Suche bis zum Auftrag mit vier markierten Bruchstellen und dem Rückkanal zu Google Ads.` Prüfen, dass es als og:image mit 2400 × 1260 ausgeliefert wird.
- Kontextbrücke im Reader: Für diesen Beitrag führt der primäre Link zur Projektanfrage mit Fokus `relaunch`, nicht auf `/wordpress-agentur-hannover/`. Das folgt dem Override-Muster von `$is_agency_outsourcing`.
- FAQ-Schema aktivieren (`enable_faq_schema`). Erwartet werden **fünf** Fragen: „Was ist eine B2B-Website?“ und die vier Fragen im Block „Häufige Fragen“.
- `docs/seo/query-ownership.csv`: `website relaunch;/website-relaunch/;informational;owned;Google Keyword Planner Export 2026-09-23, DE, Stufe 500/Monat;`. Vorher `intent-gate.sh check "website relaunch" "website-relaunch"` ausführen (Stand 24.09.: FREI).
- `docs/experimente/ersteinschaetzung.md`: Der Beitrag ist ein zusätzlicher Einstieg. Die Zählung läuft weiter über das Betreff-Präfix, der Beitrag ist über die Einstiegs-URL im CRM getrennt auswertbar.
- `pillar-cornerstone-writer`: eine Referenz `references/bausteine.md` mit allen Shortcodes, Klassen und je einem Beispiel. Künftige Artikel bauen darauf auf.
- Nach dem Deploy `docs/architecture/LIVE_STATUS.md` und, falls der Blog dort gelistet ist, `llms.txt` nachziehen.

## Nicht tun

- Die Copy umschreiben, kürzen oder „verbessern“. Einzige Ausnahme sind Kanon-Ersetzungen.
- Zahlen als Literal in den Inhalt oder in Partials schreiben.
- Das Layout anderer Beiträge ändern. Die Bausteine sind additiv.
- Farben, Schriften, Radien oder Schatten außerhalb der System-Tokens einführen, Bibliotheken oder Drittskripte einbinden.

## Abnahme

1. `npm run lint:architecture`, `npm run lint:php` und `scripts/canon-guard.sh` sind grün.
2. Im Inhalt stehen keine Literale für 3.400 €, 390 €, 150 €, 22 €, 1.750+, 15 % oder 17 %. Prüfen per `rg` in der Content-Datei und den Partials.
3. Darstellung bei 360, 768, 1280 und 1440 px: kein horizontales Scrollen. Tabellen stapeln unter 700 px, die Kette steht senkrecht, sobald ihr Container schmaler als 820 px ist. Marginalien schweben erst dort, wo rechts Platz ist.
4. Mit JS aus ist der ganze Inhalt sichtbar und die Prüfliste eine normale Liste. Mit `prefers-reduced-motion` bewegt sich nichts.
5. Das FAQPage-Schema enthält fünf Fragen, jede `acceptedAnswer` ist lesbarer Fließtext ohne Notiz-, Abbildungs- oder Quellentext.
6. Zwei andere Beiträge zur Stichprobe sehen unverändert aus.
7. Screenshots des Artikels bei 1440 und 390 px liegen im PR.

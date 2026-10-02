# CSS Architecture

Stand: 2026-09-14

## Zielbild

Neue Oberflächen sollen nicht mehr ihr eigenes Designsystem erfinden. Die Website hat derzeit mehrere historisch gewachsene Systeme; sie werden kontrolliert migriert statt in einem Big-Bang zusammengelegt.

Die Schichten sind verbindlich:

1. `style.css` — WordPress-/Blocksy-Basis und wenige globale Kompatibilitätsregeln.
2. `assets/css/system.css` — kanonischer Gutachten-Core für neue und migrierte Oberflächen.
3. Route-/Komponenten-CSS — nur das Delta, das der Core nicht abbildet.
4. Legacy-Systeme — klar begrenzt, keine neuen Komponenten oder Token-Familien.

## 1. Kanonischer Core: `system.css`

`system.css` ist die einzige dauerhafte Quelle für die Gutachten-Tokens:

- Farbe: `--papier`, `--zone`, `--zone2`, `--tinte`, `--grau`, `--matt`, `--stempel`, `--haar`, `--strich`
- Typografie: `--serif`, `--serif-display`, `--mono`
- Layout: `--rand`, `--marg`, `--satz`
- Abstand: `--s0` bis `--s6`
- Mono-Stufen: `--mono-s`, `--mono-m`, `--mono-l`
- Radius: `--r0` (0, für alles), `--r1` (3 px, nur Tafel und Eingabefelder); `50%` nur für Punkte
- Schriftgrade: `--grad-h1`, `--grad-h2`, `--grad-h2-leise`, `--grad-text`, `--grad-klein`
- Tür (Kopf und Fuß): `--tuer-h`, `--tuer-schrift`
- Motion: `--ease-aus`, `--ease-weich`, `--t-mikro`, `--t-norm`, `--t-gross`

Radius, Schriftgrade und Tür-Maße gehören `system.css` allein; keine andere Datei darf sie neu definieren (`CANONICAL_TOKENS` in `audit-css-architecture.py`).

Die dunkle `.tafel` ist Teil desselben Systems. Sie überschreibt die semantischen Tokens lokal; sie ist kein zweites Theme.

Neue Seiten verwenden diese Tokens. Sie definieren weder eine zweite Abstandsskala noch einen zweiten Satz an Farben unter anderem Namen, wenn die vorhandene Semantik passt.

## 2. Solar-Anfragestrecke: `anfragestrecke.css`

`anfragestrecke.css` konsumiert `system.css` direkt. Der frühere, wertgleiche Token-Spiegel (39 Custom Properties unter `.strecke-doc` und `.strecke-doc .tafel`) ist seit 2026-10-01 auch aus dem Authoring-Source entfernt; der Deployment-Build hatte ihn ohnehin schon entfernt, der Build-Output ist deshalb unverändert. Mit ihm entfielen `TRANSITIONAL_MIRRORS` in `audit-css-architecture.py` und `scripts/collapse-gutachten-token-mirror.py`; der Guard bricht jetzt bei jeder Neudefinition der Canon-Tokens in dieser Datei (auch bei einzeiligen Deklarationen). Die Regel `.strecke-doc .tafel`, die `system.css` (`.tafel`) wertgleich wiederholte, entfällt ebenfalls. Die Datei zählt in `css-values.tsv` 0 in allen drei Kategorien.

## 3. Legacy Compatibility: `design-system.css`

`design-system.css` ist derzeit noch notwendig, weil aktive Alt-Routen `--nx-*` konsumieren. Es ist **nicht** der Design-Core für neue Seiten.

Der Ausgangsstand vom 14.09.2026 betrug **39 CSS-Dateien mit NX-Namen** außerhalb des Providers. Davon waren **34 tatsächlich an Tokens gekoppelt, die `design-system.css` deklariert**; fünf Dateien trugen lediglich historische NX-Namen, deren Token der Provider gar nicht besitzt.

Der provider-unabhängige NX-Abbau ist abgeschlossen. `b2b-solar-leads-page.css` und `solar-leads-kaufen-alternative-page.css` verwenden jetzt die kanonischen Typografie-Rollen. Die Anfragestrecke besitzt für ihre von JavaScript gemessene Energy-Header-Höhe den route-lokalen Token `--strecke-header-height` statt eines global benannten NX-Tokens.

Die shrink-only Baseline steht aktuell bei **17 NX-Verbraucherdateien**. `site-header.css` und `site-header.js` sind mit dem retired Growth-Audit-Sonderheader entfernt; das verwaiste `audit.css` ebenfalls. `style.css` konsumiert keine `--nx-*`-Variablen mehr und ist aus der Baseline entfernt. Jeder verbleibende Verbraucher nutzt mindestens ein Token des Legacy-Providers; die Zahl darf nur sinken.

Die ehemalige Meta-Ads-Service-Route ist ebenfalls retired: `page-meta-ads.php` und `meta-ads.css` sind entfernt. Der aktive redaktionelle Beitrag `meta-ads-fuer-b2b` läuft über die normale Single-/Editorial-Architektur und benötigt dieses Service-CSS nicht.

Regeln:

- keine neue Route auf `--nx-*` aufbauen;
- keine neue CSS-Datei als `--nx-*`-Verbraucher hinzufügen;
- bestehende Verbraucher einzeln auf `system.css` migrieren oder stilllegen;
- ein bereinigter Verbraucher wird sofort aus der Baseline entfernt;
- keine neuen generischen Komponenten in `design-system.css` erfinden;
- `design-system.css` ist auf der kanonischen Startseite, der Personenseite, dem Ergebnisse-Hub, den Glossarseiten, der White-Label-Seite, der Kontaktseite, der Server-Side-Tracking-Seite und der Fallstudie bereits aus dem Enqueue genommen; weitere Routen folgen erst nach eigenem Provider-Audit.

Das unmittelbare Ziel ist daher nicht, `design-system.css` mit `system.css` zu verschmelzen. Beide Systeme haben unterschiedliche historische Semantik und Theme-Annahmen; ein blindes Zusammenlegen würde Cascade- und Kontrastfehler erzeugen.

### 3a. Server-Side Tracking: eine Datei

`/server-side-tracking-b2b/` lädt `system.css`, `contact.css`, `sticky-cta.css` und `server-side-tracking.css` — weder `design-system.css` noch `solar-leads-kaufen-alternative.css`. Die Datei ersetzt die fünf früheren Schichten (`-base`, `-cro`, `-funnel`, `-protocol`, `-contrast`), die per `@import` zusammengesetzt wurden und eigene Token-Familien (`--sst-*`, `--vp-*`) mitbrachten. Die Reihenfolge der Abschnitte ist die alte Kaskadenreihenfolge, damit die Spezifität-Verhältnisse unverändert bleiben. Der Deployment-Build bündelt nichts mehr; er minifiziert die Datei wie jede andere und bricht ab, wenn sie ein `@import` enthält.

Farben stehen nur noch als Token: Kupferstufen sind `--stempel`, Flächen `--papier`/`--zone`/`--zone2`, Text `--tinte`/`--grau`/`--matt`, Linien `--haar`, Transparenzen `color-mix(in srgb, <Token> N%, transparent)`. Radien sind `--r0` (Formularfelder `--r1`), Schatten und Verläufe als Fläche entfallen. Die Autorenzeile (`.hu-intercept__byline`) trägt die Route selbst, weil ihre Grundregeln auf dunklem Grund gesetzt waren.

Die gemeinsame Sticky-CTA-Leiste (`sticky-cta.css`, `template-parts/seo-subpage-sticky-cta.php`) trägt die Klasse `.tafel` und steht damit auf der dunklen Fläche für Handlung. Die Money-Page-Hülle (`.hu-money-*`, Rail und mobiles Inhaltsverzeichnis) lag bis dahin in `sticky-cta.css` und gehört jetzt zur SST-Datei, weil nur diese Route sie nutzt.

### 3b. Portal-Einordnungen: Beitragsmodule auf Tokens

Die vier Portal-Einordnungen (Checkfox, Aroundhome, Wattfox, DAA) laufen im gemeinsamen Leser (`template-parts/single-reader.php`, `article-reader-body.css`, `single-reader-unified.css`). Seit 2026-10-01 stehen ihre beitragsspezifischen Module auf `system.css`: `checkfox-decision.css`, `aroundhome-decision.css` und `cpo-calculator.css` enthalten weder Farbwert noch Radius noch `--nx-*`. Die Modulfarben (`--checkfox-*`, `--ah-*`) sind aufgelöst; die toten Regeln auf `[data-theme='light']`/`[data-nx-theme='light']` entfallen, weil `nexus-core.js` die Wurzel fest auf `dark` setzt. Die Seite ist hell; wo ein Baustein den Stempel als Fläche trägt (Knöpfe, CPO-Ergebnis), setzt es den Text ausdrücklich auf `--papier`, weil der Leser Links und `strong` im Artikel sonst dunkel färbt.

**Offen:** Die Beiträge laden weiter den Legacy-Provider, denn der Leser-Unterbau (`single.css`, `single-editorial.css`, `related-content.css`, `footer-cta.css`, `post-visual.css`, `blog-notify.css`, `wgos-bridge.css`) ist für alle Beiträge gemeinsam. Ihn auf Tokens zu ziehen ändert jeden Artikel des Blogs und ist deshalb ein eigener Auftrag (Cluster 4 unten). `single-reader-unified.css` neutralisiert ihn heute mit `!important`.

### Gemessene Legacy-Cluster

Die verbleibenden Verbraucher verteilen sich nicht gleichmäßig. Für die Migration gelten diese Cluster:

1. **Globale Shell / Provider:** `style.css` ist von `--nx-*` entkoppelt; der frühere Audit-Sonderheader samt `site-header.css` ist entfernt. `design-system.css` wird auf der kanonischen Startseite, der Personenseite, dem Ergebnisse-Hub und den Glossarseiten nicht mehr geladen. Auf allen übrigen Routen bleibt der Provider vorerst aktiv, weil er neben NX-Tokens auch unpräfixierte Tokens sowie globale `body`-/Heading-/Kompatibilitätsregeln bereitstellt.
2. **Schwere Legacy-Oberflächen:** `homepage.css`, `wgos.css`, `wgos-assets.css`. `ergebnisse.css` ist bereits vollständig entkoppelt.
3. **Service-Routen:** `cro.css`, `ga4.css`, `cwv.css`, `seo-cornerstone.css`, `seo.css`. `performance.css` ist entkoppelt (siehe unten).
4. **Blog / Editorial:** `single.css`, `single-editorial.css`, `related-content.css`, `footer-cta.css`, Provider-Decision-Layer.
5. **Solar / Intercepts:** `solar-marketcheck-compact.css`, `solar-leads-kaufen-alternative.css` und die Solar-SEO-Deltas. `server-side-tracking.css` und `sticky-cta.css` stehen auf `system.css` (siehe Abschnitt 3a).
6. **Kleine Restverbraucher:** Dateien mit nur wenigen oder provider-unabhängigen NX-Verwendungen werden bevorzugt entfernt, sofern die Semantik eindeutig ist.

Die Anzahl allein entscheidet nicht über die Reihenfolge. Global geladene Verbraucher haben Vorrang, weil sie die Abschaltung des Providers blockieren.

### Source-Bereinigung von `style.css`

Der historische `NEXUS SINGLE PAGE LAYOUT`-Block und sein altes Share-Finishing sind jetzt auch aus dem Authoring-Source entfernt. `assets/css/single.css` besitzt die produktiven Single-/SEO-Cornerstone-Selektoren vollständig; der frühere Deployment-Pruner ist deshalb entfallen.

Zusätzlich wurden die serverseitig nicht mehr renderbaren Blocksy-Menü-/CTA- und Mega-Menü-Blöcke aus `style.css` entfernt. Der Parent-Header ist über `blocksy:builder:header:enabled` deaktiviert; die aktuelle Standardnavigation wird als `.leiste` gerendert. Der verbliebene Blocksy-Shell-Bestand (`.ct-header`, `.ct-panel`, Mega-Menü und Flight-Mode) ist anschließend ebenfalls entfernt worden: der Parent-Header ist serverseitig deaktiviert und diese Strukturen werden nicht mehr gerendert. Der zugehörige `initHeaderFlight()`-Fallback in `nexus-core.js` ist damit ebenfalls entfallen. Der Archive-Block ist anschließend aus `style.css` in `assets/css/archive.css` ausgelagert worden und wird nur auf sonstigen Archiv-Routen geladen. Die neue Datei verwendet keine `--nx-*`-Variablen. Der Kundenportal-Block ist ebenfalls aus `style.css` in `assets/css/client-portal.css` ausgelagert und wird ausschließlich über `template-portal.php` geladen; auch diese Datei ist NX-frei. Die globalen Cockpit-/Header-CTA-Regeln und die verbliebenen globalen Focus-Ringe konsumieren inzwischen ebenfalls direkt die Gutachten-Tokens statt `--nx-*`; `transition: all` wurde dort durch eigenschaftsspezifische Canon-Motion ersetzt und Reduced Motion ergänzt. Der alte Homepage-/Shortcode-Kompatibilitätsblock liegt jetzt bei seinem Legacy-Owner `homepage.css`, der Single-Light-Safety-Net bei `single.css`. Der nicht mehr gerenderte `.ft`-Light-Footer-Bridge ist entfernt. Damit enthält `style.css` **0 `var(--nx-...)`-Verwendungen** und blockiert den NX-Abbau nicht mehr selbst. Der Legacy-Provider bleibt auf den noch nicht entkoppelten Routen aktiv, bis deren nicht-NX-benannte Rollen und generischen Regeln einzeln auditiert sind.

### Ergebnisse-Hub

`ergebnisse.css` verwendet seit dem Seitenumbau ausschließlich `system.css`
und dessen Tokens. Der alte NX-Verbraucher entfällt aus der shrink-only
Baseline; das verbleibende Stylesheet enthält nur Projekt-, Beleg- und
Übergabelayouts. Der Legacy-Provider wird auf dieser Route nicht mehr geladen.
Die aktuelle Anzahl der Legacy-Verbraucher liefert der Guard.

### Glossar

Übersicht und Detailseiten verwenden `system.css` plus `glossary.css` für
Suchfeld, Filter, Begriffszeilen und Lesebreite. `design-system.css`,
`homepage.css` und `wgos.css` werden auf diesen Routen nicht mehr geladen.
Die Beispiel-Tafel verwendet die vorhandene `.tafel`-Komponente. Das Glossar
ist aus der NX-Baseline entfernt; es definiert keine eigenen Design-Tokens.

### White-Label

`/whitelabel-retainer/` steht seit dem Relaunch am 2026-09-25 auf dem System
der Startseite: Die Route lädt `system.css`, `startseite-strecke.css` als
Basis (Abschnitt, Linie, Marken, Typo-Skala, Tafel, Angebotszeilen, Fragen,
Bewegungsregeln) und `whitelabel.css` als Delta für Abnahmeprotokoll,
Ablauf-Stationen, Margenblock, Formular und Fuß. Das Delta definiert keinen
Farbwert und keine Abstandsskala; `whitelabel.css` ist aus der NX-Baseline
entfernt, der Legacy-Provider lädt hier nicht mehr.

Die Messflächen von Homepage und White-Label teilen `.st-messflaeche`,
Messraster und Maskenaufstieg aus `startseite-strecke.css`. `.st-messkopf`
setzt beide Köpfe auf die vorhandenen `.tafel`-Tokens. `whitelabel.css`
enthält dafür keine zweite Flächen-, Kopf- oder Maskendefinition mehr;
Stempel, Scan, Messmarken und Abnahmeprotokoll bleiben im White-Label-Delta.
Homepage-Etikett und Signalbahn sind eigene Bausteine der gemeinsamen Basis,
aktiviert nur durch das Startseiten-Markup.

### Kontakt

`/kontakt/` steht seit 2026-10-01 ohne Legacy-Provider: `design-system.css`
wird auf der Route nicht mehr geladen, `contact.css` hängt an
`nexus-system-css`. Belegt ist das durch einen Vorher-Nachher-Vergleich des
echten Templates (`scripts/tests/render-contact.php`, alle drei Zustände bei
390, 768 und 1440 px): pixelgleich, und `contact.css` nutzt nur eigene oder
`system.css`-Tokens. Übrig bleibt der Unterschied, dass der Provider
`text-rendering: optimizeLegibility` setzte. `contact.css` ist weiter ein
gemeinsamer Verbraucher: die Server-Side-Route lädt sie mit dem Provider.
Die Kontaktseiten-Regeln, die früher als `<style>` im Fuß-Template standen,
stehen am Ende von `contact.css`.

### Performance Marketing

`performance.css` ist seit dem Umbau von `/performance-marketing/` am
2026-09-22 nur noch ein Delta von wenigen Zeilen auf `system.css`: Abstände im
Dokumentkopf und im Umbruch der Ausgänge. Alle Bausteine kommen aus dem Core;
die Datei hängt an `nexus-system-css`, steht nicht mehr in der Legacy-Baseline,
und das alte `cluster-pillar.css` ist entfernt.

### Landingpage erstellen lassen

`/landingpage-erstellen-lassen/` läuft auf `system.css`. `landingpage-offer.css`
ist ein Delta für das Angebotsblatt im Abschnitt „Umfang“ (Preiskarte,
Leistungsraster, Grenze „Nicht dazu“). Es definiert keinen Farbwert und keine
Abstandsskala und hängt an `nexus-system-css`; das Layout reagiert per
Container Query auf die Breite der `.tafel`, nicht auf das Fenster. Das
Template lädt die Datei selbst, wie `navigation-ecosystem.css`.

## 4. Bewusste lokale Systeme

### Fallstudie: `e3-case-v2.css` statt `energy-systems.css`

`energy-systems.css` (der alte Editorial-Solar-Layer unter `.solar-page`, mit eigenem `--serif`/`--mono`) ist seit 2026-10-01 entfernt. Auf der Fallstudien-Route traf die Datei zwei Regeln (Reveal-on-scroll); `.solar-page` kommt in keinem Template und keinem Skript vor. Das Reveal liegt jetzt in `e3-case-v2.css`, und die eingefrorene Kollision in `audit-css-architecture.py` ist gelöscht (`SCOPED_LEGACY_COLLISIONS` bleibt als leerer Mechanismus: eine künftige Ausnahme muss selektor- und wertgenau eingetragen werden).

`e3-case-v2.css` liest Farbe, Radius und Schrift aus `system.css`; die vier dunklen Flächen (Ergebniskarte, Diagnose, Verlauf, CTA) tragen im Template `.tafel`. Das Template hängt das Blatt selbst an, mit `nexus-system-css` als Abhängigkeit, damit `.tafel` vor dem Seitenblatt lädt. Die Route lädt den Legacy-Provider nicht mehr. Der Slug `website-fuer-solar-und-waermepumpen-anbieter` hat kein Template im Theme und behält Provider, Sticky-Leiste und Hero-Skript.

### `homepage-redesign.css`

Das `.hu-hp`-Kit bleibt nur für die noch davon abhängigen WGOS-/Case-/WOW-Oberflächen. Die aktuelle Startseite verwendet es nicht mehr. Es ist ein Migrationsbestand, kein zweiter globaler Core.

## 5. Route-CSS-Vertrag

Route-CSS soll enthalten:

- route-spezifische Layouts;
- Komponenten, die nur dort existieren;
- begründete lokale Overrides;
- responsive Anpassungen für diese Route.

Route-CSS soll **nicht** enthalten:

- Kopien globaler Farb-/Spacing-/Motion-Tokens;
- eine zweite `.tafel`, Button-, Fokus- oder Typografie-Grundfamilie;
- ungescopte `:root`-Themes;
- neue `--nx-*`-Abhängigkeiten;
- globale Regeln, die nur zufällig von einer Route gebraucht werden.

## 6. Migrationsreihenfolge

1. **Globale Shell (NX erledigt):** Die retired Audit-Header-Schicht ist vollständig entfernt; `style.css` ist von `--nx-*` entkoppelt. Der Provider-Audit hat unpräfixierte Legacy-Tokens und globale Selektorwirkungen bestätigt; deshalb ist `design-system.css` zunächst auf Startseite, Personenseite und Ergebnisse-Hub abgeschaltet. Weitere Routen werden einzeln entkoppelt.
2. **Provider-unabhängige NX-Reste (erledigt):** reine Typografie-Aliase sind auf Canon-Tokens migriert; die Anfragestrecke nutzt für ihre gemessene Energy-Header-Höhe einen route-lokalen Token.
3. **Solar-Anfragestrecke (Token-Spiegel erledigt 2026-10-01):** gemeinsame Gutachten-Primitives aus `system.css` konsumieren, nur echte Solar-Deltas behalten. Das JS-gemessene Header-/Register-Token `--strecke-header-height` sucht noch einen Energy-Header, den es nicht mehr gibt (Rückfall 64 px); es wird gemeinsam mit seinen CSS-Verbrauchern migriert, nicht isoliert umbenannt.
4. **Editorial-Solar-Legacy (erledigt 2026-10-01):** `energy-systems.css` ist entfernt, die eingefrorene Guard-Ausnahme für `.solar-page` gelöscht; die Fallstudie steht auf `e3-case-v2.css` und `system.css`.
5. **Service-Routen:** aktive `cro.css`, `ga4.css`, `seo-cornerstone.css` nach Nutzung und Geschäftswert einzeln migrieren oder stilllegen (`performance.css` erledigt).
6. **Blog / Editorial:** `single.css` und die verbleibenden Reader-/CTA-Layer auf Gutachten-Primitives ziehen; neue Blog-Schichten bauen bereits auf dem neuen System auf und dürfen nicht zurück auf NX driften.
7. **Schwere Legacy-Familien:** `homepage.css`, WGOS und Case-Routen nur nach realer Routennutzung weiterführen oder abbauen.
8. **Agentur (erledigt):** Die Route läuft auf `system.css` plus `agentur-decision.css`; das frühere `agentur.css` mit `--ag-*` ist entfernt.
9. **Legacy-Core:** `design-system.css` nicht mehr global laden; danach Restverbraucher migrieren und Datei entfernen.
10. **Alte `.hu-hp`-Familie:** nur noch behalten, wenn reale aktive Routen sie benötigen.

## 7. Guards

Lokale Checks:

```bash
python3 scripts/audit-css-architecture.py
python3 scripts/audit-legacy-nx-css.py
python3 scripts/audit-css-values.py
bash scripts/lint-css-spacing.sh
bash scripts/lint-css-motion.sh
```

`npm run lint:css-architecture` führt `audit-css-architecture.py` und `audit-css-values.py` nacheinander aus; `npm run build:theme` ebenfalls, damit kein Deploy am Wächter vorbeigeht.

`audit-css-architecture.py` schützt die Canon-Token-Familie (jede Neudefinition außerhalb von `system.css` scheitert, auch einzeilig), sperrt die abgelösten `--sst-*`/`--vp-*`-Seitentokens und verlangt `server-side-tracking.css` ohne `@import`.

`audit-legacy-nx-css.py` schützt den Abbaupfad: Neue NX-Verbraucher scheitern, und sobald eine Datei bereinigt ist, wird ihre Baseline-Zeile als veraltet gemeldet. Die Baseline ist damit kein Zielwert, sondern eine Obergrenze, die nur sinken darf. Zusätzlich zeigt der Audit, welche Dateien den Legacy-Provider wirklich benötigen und welche lediglich alte NX-Namen tragen.

`audit-css-values.py` zählt je Stylesheet drei Kategorien außerhalb von `:root` und `.tafel`: literale Farbwerte (Hex, `rgb()`/`hsl()` ohne `var()`), Radiuswerte außer `var(--r0)`, `var(--r1)`, `0` und `50%`, und `font-family`-Literale statt `--serif`/`--serif-display`/`--mono`. Gezählt wird je Vorkommen, nicht je verschiedenem Wert. `scripts/baselines/css-values.tsv` ist eine Schrumpf-Baseline mit derselben Mechanik wie die NX-Baseline: Ein Anstieg bricht den Build; sinkt ein Wert, muss die Baseline nachgezogen werden (`python3 scripts/audit-css-values.py --write-baseline`, das keinen Anstieg schreibt). Eine Datei, die nicht in der Baseline steht, zählt in jeder Kategorie als 0. Ausnahmen gibt es nicht über die Baseline, sondern über einen Token in `system.css`.

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
- Motion: `--ease-aus`, `--ease-weich`, `--t-mikro`, `--t-norm`, `--t-gross`

Die dunkle `.tafel` ist Teil desselben Systems. Sie überschreibt die semantischen Tokens lokal; sie ist kein zweites Theme.

Neue Seiten verwenden diese Tokens. Sie definieren weder eine zweite Abstandsskala noch einen zweiten Satz an Farben unter anderem Namen, wenn die vorhandene Semantik passt.

## 2. Übergang: `anfragestrecke.css`

`anfragestrecke.css` enthält noch einen historischen, wertgleichen Spiegel des Gutachten-Token-Satzes. Das ist die letzte bekannte wertgleiche produktive Doppeldefinition dieser Canon-Tokens.

Dieser Spiegel ist **transitional**, nicht eine zweite Quelle der Wahrheit:

- `scripts/audit-css-architecture.py` vergleicht alle Werte mit `system.css` und bricht bei Drift ab.
- Der Deployment-Build entfernt die gespiegelten Custom Properties aus `anfragestrecke.css`, nachdem ihre Gleichheit validiert wurde.
- Die nächste physische Migration entfernt den Spiegel auch aus dem Authoring-Source und lässt die Route den Core direkt konsumieren.

Bis dahin dürfen dort keine abweichenden Werte entstehen.

## 3. Legacy Compatibility: `design-system.css`

`design-system.css` ist derzeit noch notwendig, weil aktive Alt-Routen `--nx-*` konsumieren. Es ist **nicht** der Design-Core für neue Seiten.

Der Ausgangsstand vom 14.09.2026 betrug **39 CSS-Dateien mit NX-Namen** außerhalb des Providers. Davon waren **34 tatsächlich an Tokens gekoppelt, die `design-system.css` deklariert**; fünf Dateien trugen lediglich historische NX-Namen, deren Token der Provider gar nicht besitzt.

Der provider-unabhängige NX-Abbau ist abgeschlossen. `server-side-tracking-cro.css`, `b2b-solar-leads-page.css`, `server-side-tracking-protocol.css` und `solar-leads-kaufen-alternative-page.css` verwenden jetzt die kanonischen Typografie-Rollen. Die Anfragestrecke besitzt für ihre von JavaScript gemessene Energy-Header-Höhe den route-lokalen Token `--strecke-header-height` statt eines global benannten NX-Tokens.

Die shrink-only Baseline steht damit aktuell bei **34 NX-Verbraucherdateien**. Jeder verbleibende Verbraucher nutzt mindestens ein Token des Legacy-Providers; die Zahl darf nur sinken.

Regeln:

- keine neue Route auf `--nx-*` aufbauen;
- keine neue CSS-Datei als `--nx-*`-Verbraucher hinzufügen;
- bestehende Verbraucher einzeln auf `system.css` migrieren oder stilllegen;
- ein bereinigter Verbraucher wird sofort aus der Baseline entfernt;
- keine neuen generischen Komponenten in `design-system.css` erfinden;
- erst wenn globale Shell und aktive Routen entkoppelt sind, wird `design-system.css` aus dem globalen Enqueue genommen.

Das unmittelbare Ziel ist daher nicht, `design-system.css` mit `system.css` zu verschmelzen. Beide Systeme haben unterschiedliche historische Semantik und Theme-Annahmen; ein blindes Zusammenlegen würde Cascade- und Kontrastfehler erzeugen.

### Gemessene Legacy-Cluster

Die verbleibenden Verbraucher verteilen sich nicht gleichmäßig. Für die Migration gelten diese Cluster:

1. **Globale Shell / Blocker:** `style.css` und `site-header.css`. Solange diese beiden breit `--nx-*` konsumieren, kann der Legacy-Provider nicht sicher global abgeschaltet werden.
2. **Schwere Legacy-Oberflächen:** `homepage.css`, `wgos.css`, `wgos-assets.css`, `case-study.css`, `ergebnisse.css`.
3. **Service-Routen:** `cro.css`, `ga4.css`, `meta-ads.css`, `cwv.css`, `performance.css`, `seo-cornerstone.css`, `seo.css`.
4. **Blog / Editorial:** `single.css`, `single-editorial.css`, `related-content.css`, `footer-cta.css`, Provider-Decision-Layer.
5. **Solar / Tracking / Intercepts:** `energy-systems.css`, SST-Quellen, `solar-marketcheck-compact.css`, `sticky-cta.css` und die Solar-SEO-Deltas.
6. **Kleine Restverbraucher:** Dateien mit nur wenigen oder provider-unabhängigen NX-Verwendungen werden bevorzugt entfernt, sofern die Semantik eindeutig ist.

Die Anzahl allein entscheidet nicht über die Reihenfolge. Global geladene Verbraucher haben Vorrang, weil sie die Abschaltung des Providers blockieren.

### Deployment-Deduplizierung von `style.css`

`style.css` enthält im Authoring-Source noch einen historischen Block `NEXUS SINGLE PAGE LAYOUT`. Die gleichen Kernselektoren werden auf Single-Posts und der SEO-Cornerstone-Route bereits vom später geladenen `assets/css/single.css` neu besessen.

Der Deployment-Build entfernt deshalb ausschließlich diesen klar markierten Single-Block über `scripts/prune-style-single-legacy.py`. Der Guard prüft vorher, dass `single.css` weiterhin die kritischen Selektoren besitzt und dass der folgende `NEXUS ARCHIVE LAYOUT`-Block nicht berührt wird.

Gemessener Stand der ersten Deduplizierung:

- 131 Source-Zeilen weniger im Deployment-`style.css`;
- 2.972 Bytes unminifizierter Doppelcode entfernt;
- 19 Legacy-NX-Verwendungen weniger im ausgelieferten `style.css`.

Der Archive-Block bleibt bestehen, weil `archive.php` dessen Markup weiterhin aktiv verwendet. Langfristig soll der Single-Block auch aus dem Authoring-Source verschwinden; der Build-Prune ist eine gesicherte Übergangsstufe, kein zweites dauerhaftes Quellsystem.

## 4. Bewusste lokale Systeme

### `energy-systems.css`

Der alte Editorial-Solar-Layer unter `.solar-page` besitzt ein eigenes Paper/Ink-System. Darin kollidieren zwei historische generische Namen mit dem Gutachten-Core: `--serif` und `--mono`. Die Werte sind **nicht** identisch mit `system.css` und werden von den `.solar-*`-Komponenten aktiv benutzt.

Diese Kollision wird deshalb nicht als zweiter Canon akzeptiert, sondern im Guard als wert- und selektorgenau eingefrorene Legacy-Ausnahme behandelt:

- nur `energy-systems.css`;
- nur unter `.solar-page`;
- nur `--serif` und `--mono`;
- nur mit den heute bekannten Werten.

Jede Verschiebung nach `:root`, jeder weitere Core-Token und jede Wertänderung bricht den Guard. Die spätere Migration benennt diese beiden lokalen Variablen eindeutig (`--solar-*`) oder ersetzt sie durch passende Core-Rollen; danach wird die Ausnahme entfernt.

### `agentur.css`

Die Agentur-Seite hat aktuell ein eigenes Light-System unter `.wp-agentur-page-wrapper` und `--ag-*`. Das bleibt vorerst lokal, weil Farben, Kategorie-Akzente und Komponenten nicht 1:1 dem Gutachten-Core entsprechen.

Neue gemeinsame Primitive sollen jedoch nicht erneut als `--ag-*` erfunden werden, wenn `system.css` dieselbe Rolle bereits abdeckt. Die Route wird später komponentenweise migriert.

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

1. **Globale Shell:** `style.css` und `site-header.css` inventarisieren und ihre wirklich global benötigten Primitive von den alten Seiten-/Blocksy-Regeln trennen. Das ist der Hauptblocker für einen bedingten Legacy-Provider.
2. **Provider-unabhängige NX-Reste (erledigt):** reine Typografie-Aliase sind auf Canon-Tokens migriert; die Anfragestrecke nutzt für ihre gemessene Energy-Header-Höhe einen route-lokalen Token.
3. **Solar-Anfragestrecke:** Token-Spiegel physisch entfernen; gemeinsame Gutachten-Primitives aus `system.css` konsumieren, nur echte Solar-Deltas behalten. Das JS-gemessene Header-/Register-Token wird gemeinsam mit seinen CSS-Verbrauchern migriert, nicht isoliert umbenannt.
4. **Editorial-Solar-Legacy:** die generischen `.solar-page`-Variablen `--serif`/`--mono` eindeutig umbenennen oder auf Core-Rollen migrieren; eingefrorene Guard-Ausnahme danach löschen.
5. **Service-Routen:** aktive `cro.css`, `ga4.css`, `meta-ads.css`, `performance.css`, `seo-cornerstone.css` nach Nutzung und Geschäftswert einzeln migrieren oder stilllegen.
6. **Blog / Editorial:** `single.css` und die verbleibenden Reader-/CTA-Layer auf Gutachten-Primitives ziehen; neue Blog-Schichten bauen bereits auf dem neuen System auf und dürfen nicht zurück auf NX driften.
7. **Schwere Legacy-Familien:** `homepage.css`, WGOS, Case-/Ergebnisse-Routen nur nach realer Routennutzung weiterführen oder abbauen.
8. **Agentur:** gemeinsame Primitive übernehmen, `--ag-*` nur für echte Marken-/Kategorie-Semantik behalten.
9. **Legacy-Core:** `design-system.css` nicht mehr global laden; danach Restverbraucher migrieren und Datei entfernen.
10. **Alte `.hu-hp`-Familie:** nur noch behalten, wenn reale aktive Routen sie benötigen.

## 7. Guards

Lokale Checks:

```bash
python3 scripts/audit-css-architecture.py
python3 scripts/audit-legacy-nx-css.py
bash scripts/lint-css-spacing.sh
bash scripts/lint-css-motion.sh
```

`audit-css-architecture.py` schützt die neue Canon-Token-Familie und die exakt eingefrorenen Übergangsausnahmen.

`audit-legacy-nx-css.py` schützt den Abbaupfad: Neue NX-Verbraucher scheitern, und sobald eine Datei bereinigt ist, wird ihre Baseline-Zeile als veraltet gemeldet. Die Baseline ist damit kein Zielwert, sondern eine Obergrenze, die nur sinken darf. Zusätzlich zeigt der Audit, welche Dateien den Legacy-Provider wirklich benötigen und welche lediglich alte NX-Namen tragen.

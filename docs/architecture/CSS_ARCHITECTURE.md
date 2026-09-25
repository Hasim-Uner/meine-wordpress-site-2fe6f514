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

Die shrink-only Baseline steht nach der Entkopplung der globalen Shell aktuell bei **27 NX-Verbraucherdateien**. `site-header.css` und `site-header.js` sind mit dem retired Growth-Audit-Sonderheader entfernt; das verwaiste `audit.css` ebenfalls. `style.css` konsumiert keine `--nx-*`-Variablen mehr und ist aus der Baseline entfernt. Jeder verbleibende Verbraucher nutzt mindestens ein Token des Legacy-Providers; die Zahl darf nur sinken.

Die ehemalige Meta-Ads-Service-Route ist ebenfalls retired: `page-meta-ads.php` und `meta-ads.css` sind entfernt. Der aktive redaktionelle Beitrag `meta-ads-fuer-b2b` läuft über die normale Single-/Editorial-Architektur und benötigt dieses Service-CSS nicht.

Regeln:

- keine neue Route auf `--nx-*` aufbauen;
- keine neue CSS-Datei als `--nx-*`-Verbraucher hinzufügen;
- bestehende Verbraucher einzeln auf `system.css` migrieren oder stilllegen;
- ein bereinigter Verbraucher wird sofort aus der Baseline entfernt;
- keine neuen generischen Komponenten in `design-system.css` erfinden;
- `design-system.css` ist auf der kanonischen Startseite, der Personenseite, dem Ergebnisse-Hub, den Glossarseiten und der White-Label-Seite bereits aus dem Enqueue genommen; weitere Routen folgen erst nach eigenem Provider-Audit.

Das unmittelbare Ziel ist daher nicht, `design-system.css` mit `system.css` zu verschmelzen. Beide Systeme haben unterschiedliche historische Semantik und Theme-Annahmen; ein blindes Zusammenlegen würde Cascade- und Kontrastfehler erzeugen.

### Gemessene Legacy-Cluster

Die verbleibenden Verbraucher verteilen sich nicht gleichmäßig. Für die Migration gelten diese Cluster:

1. **Globale Shell / Provider:** `style.css` ist von `--nx-*` entkoppelt; der frühere Audit-Sonderheader samt `site-header.css` ist entfernt. `design-system.css` wird auf der kanonischen Startseite, der Personenseite, dem Ergebnisse-Hub und den Glossarseiten nicht mehr geladen. Auf allen übrigen Routen bleibt der Provider vorerst aktiv, weil er neben NX-Tokens auch unpräfixierte Tokens sowie globale `body`-/Heading-/Kompatibilitätsregeln bereitstellt.
2. **Schwere Legacy-Oberflächen:** `homepage.css`, `wgos.css`, `wgos-assets.css`. `ergebnisse.css` ist bereits vollständig entkoppelt.
3. **Service-Routen:** `cro.css`, `ga4.css`, `cwv.css`, `seo-cornerstone.css`, `seo.css`. `performance.css` ist entkoppelt (siehe unten).
4. **Blog / Editorial:** `single.css`, `single-editorial.css`, `related-content.css`, `footer-cta.css`, Provider-Decision-Layer.
5. **Solar / Tracking / Intercepts:** `energy-systems.css`, SST-Quellen, `solar-marketcheck-compact.css`, `sticky-cta.css` und die Solar-SEO-Deltas.
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

### Performance Marketing

`performance.css` ist seit dem Umbau von `/performance-marketing/` am
2026-09-22 nur noch ein Delta von wenigen Zeilen auf `system.css`: Abstände im
Dokumentkopf und im Umbruch der Ausgänge. Alle Bausteine kommen aus dem Core;
die Datei hängt an `nexus-system-css`, steht nicht mehr in der Legacy-Baseline,
und das alte `cluster-pillar.css` ist entfernt.

## 4. Bewusste lokale Systeme

### `energy-systems.css`

Der alte Editorial-Solar-Layer unter `.solar-page` besitzt ein eigenes Paper/Ink-System. Darin kollidieren zwei historische generische Namen mit dem Gutachten-Core: `--serif` und `--mono`. Die Werte sind **nicht** identisch mit `system.css` und werden von den `.solar-*`-Komponenten aktiv benutzt.

Diese Kollision wird deshalb nicht als zweiter Canon akzeptiert, sondern im Guard als wert- und selektorgenau eingefrorene Legacy-Ausnahme behandelt:

- nur `energy-systems.css`;
- nur unter `.solar-page`;
- nur `--serif` und `--mono`;
- nur mit den heute bekannten Werten.

Jede Verschiebung nach `:root`, jeder weitere Core-Token und jede Wertänderung bricht den Guard. Die spätere Migration benennt diese beiden lokalen Variablen eindeutig (`--solar-*`) oder ersetzt sie durch passende Core-Rollen; danach wird die Ausnahme entfernt.

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
3. **Solar-Anfragestrecke:** Token-Spiegel physisch entfernen; gemeinsame Gutachten-Primitives aus `system.css` konsumieren, nur echte Solar-Deltas behalten. Das JS-gemessene Header-/Register-Token wird gemeinsam mit seinen CSS-Verbrauchern migriert, nicht isoliert umbenannt.
4. **Editorial-Solar-Legacy:** die generischen `.solar-page`-Variablen `--serif`/`--mono` eindeutig umbenennen oder auf Core-Rollen migrieren; eingefrorene Guard-Ausnahme danach löschen.
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
bash scripts/lint-css-spacing.sh
bash scripts/lint-css-motion.sh
```

`audit-css-architecture.py` schützt die neue Canon-Token-Familie und die exakt eingefrorenen Übergangsausnahmen.

`audit-legacy-nx-css.py` schützt den Abbaupfad: Neue NX-Verbraucher scheitern, und sobald eine Datei bereinigt ist, wird ihre Baseline-Zeile als veraltet gemeldet. Die Baseline ist damit kein Zielwert, sondern eine Obergrenze, die nur sinken darf. Zusätzlich zeigt der Audit, welche Dateien den Legacy-Provider wirklich benötigen und welche lediglich alte NX-Namen tragen.
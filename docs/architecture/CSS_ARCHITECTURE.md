# CSS Architecture

Stand: 2026-09-14

## Zielbild

Neue Oberflaechen sollen nicht mehr ihr eigenes Designsystem erfinden. Die Website hat derzeit mehrere historisch gewachsene Systeme; sie werden kontrolliert migriert statt in einem Big-Bang zusammengelegt.

Die Schichten sind verbindlich:

1. `style.css` — WordPress-/Blocksy-Basis und wenige globale Kompatibilitaetsregeln.
2. `assets/css/system.css` — kanonischer Gutachten-Core fuer neue und migrierte Oberflaechen.
3. Route-/Komponenten-CSS — nur das Delta, das der Core nicht abbildet.
4. Legacy-Systeme — klar begrenzt, keine neuen Komponenten oder Token-Familien.

## 1. Kanonischer Core: `system.css`

`system.css` ist die einzige dauerhafte Quelle fuer die Gutachten-Tokens:

- Farbe: `--papier`, `--zone`, `--zone2`, `--tinte`, `--grau`, `--matt`, `--stempel`, `--haar`, `--strich`
- Typografie: `--serif`, `--serif-display`, `--mono`
- Layout: `--rand`, `--marg`, `--satz`
- Abstand: `--s0` bis `--s6`
- Mono-Stufen: `--mono-s`, `--mono-m`, `--mono-l`
- Motion: `--ease-aus`, `--ease-weich`, `--t-mikro`, `--t-norm`, `--t-gross`

Die dunkle `.tafel` ist Teil desselben Systems. Sie ueberschreibt die semantischen Tokens lokal; sie ist kein zweites Theme.

Neue Seiten verwenden diese Tokens. Sie definieren weder eine zweite Abstandsskala noch einen zweiten Satz an Farben unter anderem Namen, wenn die vorhandene Semantik passt.

## 2. Uebergang: `anfragestrecke.css`

`anfragestrecke.css` enthaelt noch einen historischen, wertgleichen Spiegel des Gutachten-Token-Satzes. Das ist die letzte bekannte wertgleiche produktive Doppeldefinition dieser Canon-Tokens.

Dieser Spiegel ist **transitional**, nicht eine zweite Quelle der Wahrheit:

- `scripts/audit-css-architecture.py` vergleicht alle Werte mit `system.css` und bricht bei Drift ab.
- Der Deployment-Build entfernt die gespiegelten Custom Properties aus `anfragestrecke.css`, nachdem ihre Gleichheit validiert wurde.
- Die naechste physische Migration entfernt den Spiegel auch aus dem Authoring-Source und laesst die Route den Core direkt konsumieren.

Bis dahin duerfen dort keine abweichenden Werte entstehen.

## 3. Legacy Compatibility: `design-system.css`

`design-system.css` ist derzeit noch notwendig, weil aktive Alt-Routen `--nx-*` konsumieren. Es ist **nicht** der Design-Core fuer neue Seiten.

Regeln:

- keine neue Route auf `--nx-*` aufbauen;
- keine neuen generischen Komponenten dort erfinden;
- bestehende Routen einzeln auf `system.css` migrieren;
- sobald keine aktive Route den Legacy-Core mehr benoetigt, wird das Stylesheet aus dem globalen Enqueue genommen und spaeter entfernt.

Das unmittelbare Ziel ist daher nicht, `design-system.css` mit `system.css` zu verschmelzen. Beide Systeme haben unterschiedliche historische Semantik und Theme-Annahmen; ein blindes Zusammenlegen wuerde Cascade- und Kontrastfehler erzeugen.

## 4. Bewusste lokale Systeme

### `energy-systems.css`

Der alte Editorial-Solar-Layer unter `.solar-page` besitzt ein eigenes Paper/Ink-System. Darin kollidieren zwei historische generische Namen mit dem Gutachten-Core: `--serif` und `--mono`. Die Werte sind **nicht** identisch mit `system.css` und werden von den `.solar-*`-Komponenten aktiv benutzt.

Diese Kollision wird deshalb nicht als zweiter Canon akzeptiert, sondern im Guard als wert- und selektorgenau eingefrorene Legacy-Ausnahme behandelt:

- nur `energy-systems.css`;
- nur unter `.solar-page`;
- nur `--serif` und `--mono`;
- nur mit den heute bekannten Werten.

Jede Verschiebung nach `:root`, jeder weitere Core-Token und jede Wertaenderung bricht den Guard. Die spaetere Migration benennt diese beiden lokalen Variablen eindeutig (`--solar-*`) oder ersetzt sie durch passende Core-Rollen; danach wird die Ausnahme entfernt.

### `agentur.css`

Die Agentur-Seite hat aktuell ein eigenes Light-System unter `.wp-agentur-page-wrapper` und `--ag-*`. Das bleibt vorerst lokal, weil Farben, Kategorie-Akzente und Komponenten nicht 1:1 dem Gutachten-Core entsprechen.

Neue gemeinsame Primitive sollen jedoch nicht erneut als `--ag-*` erfunden werden, wenn `system.css` dieselbe Rolle bereits abdeckt. Die Route wird spaeter komponentenweise migriert.

### `homepage-redesign.css`

Das `.hu-hp`-Kit bleibt nur fuer die noch davon abhaengigen WGOS-/Case-/WOW-Oberflaechen. Die aktuelle Startseite verwendet es nicht mehr. Es ist ein Migrationsbestand, kein zweiter globaler Core.

## 5. Route-CSS-Vertrag

Route-CSS soll enthalten:

- route-spezifische Layouts;
- Komponenten, die nur dort existieren;
- begruendete lokale Overrides;
- responsive Anpassungen fuer diese Route.

Route-CSS soll **nicht** enthalten:

- Kopien globaler Farb-/Spacing-/Motion-Tokens;
- eine zweite `.tafel`, Button-, Fokus- oder Typografie-Grundfamilie;
- ungescopte `:root`-Themes;
- globale Regeln, die nur zufaellig von einer Route gebraucht werden.

## 6. Migrationsreihenfolge

1. **Solar-Anfragestrecke:** Token-Spiegel physisch entfernen; gemeinsame Gutachten-Primitives aus `system.css` konsumieren, nur echte Solar-Deltas behalten.
2. **Editorial-Solar-Legacy:** die generischen `.solar-page`-Variablen `--serif`/`--mono` eindeutig umbenennen oder auf Core-Rollen migrieren; eingefrorene Guard-Ausnahme danach loeschen.
3. **Header-/Legacy-Abhaengigkeiten:** `site-header.css` und sonstige globale Bausteine von nicht benoetigten `--nx-*` entkoppeln.
4. **Service-Routen:** aktive `cro.css`, `ga4.css`, `meta-ads.css`, `performance.css`, `seo-cornerstone.css` nach Nutzung und Geschaeftswert einzeln migrieren oder stilllegen.
5. **Agentur:** gemeinsame Primitive uebernehmen, `--ag-*` nur fuer echte Marken-/Kategorie-Semantik behalten.
6. **Legacy-Core:** `design-system.css` nicht mehr global laden; danach Restverbraucher migrieren und Datei entfernen.
7. **Alte `.hu-hp`-Familie:** nur noch dann behalten, wenn reale aktive Routen sie benoetigen.

## 7. Guard

Lokaler Check:

```bash
python3 scripts/audit-css-architecture.py
```

Der Check ist absichtlich eng: Er schuetzt zuerst die neue Canon-Token-Familie, statt historische Systeme pauschal umzubenennen. Bekannte Legacy-Kollisionen werden nur als exakte, migrationspflichtige Ausnahmen zugelassen. Weitere Ownership-Regeln werden erst aktiviert, wenn die jeweiligen Legacy-Routen bereinigt sind.

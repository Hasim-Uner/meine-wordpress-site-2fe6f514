from pathlib import Path

path = Path('docs/architecture/CSS_ARCHITECTURE.md')
text = path.read_text(encoding='utf-8')

replacements = {
"Die shrink-only Baseline steht nach der Entkopplung des Audit-Headers aktuell bei **33 NX-Verbraucherdateien**. `site-header.css` besitzt nur noch die tatsächlich gerenderte Audit-Variante, verwendet Canon-Motion plus lokale Audit-Farbrollen und wird zusammen mit `site-header.js` nur noch auf der Audit-Route geladen. Jeder verbleibende Verbraucher nutzt mindestens ein Token des Legacy-Providers; die Zahl darf nur sinken.":
"Die shrink-only Baseline steht nach der Entkopplung der globalen Shell aktuell bei **31 NX-Verbraucherdateien**. `site-header.css` besitzt nur noch die tatsächlich gerenderte Audit-Variante, verwendet Canon-Motion plus lokale Audit-Farbrollen und wird zusammen mit `site-header.js` nur noch auf der Audit-Route geladen. `style.css` konsumiert keine `--nx-*`-Variablen mehr und ist aus der Baseline entfernt. Jeder verbleibende Verbraucher nutzt mindestens ein Token des Legacy-Providers; die Zahl darf nur sinken.",

"1. **Globale Shell / Blocker:** `site-header.css` ist entkoppelt; `style.css` ist jetzt der verbleibende globale Shell-Blocker. Der Legacy-Provider kann erst bedingt geladen werden, wenn dessen aktive globale NX-Regeln isoliert oder migriert sind.":
"1. **Globale Shell:** `site-header.css` und `style.css` sind von `--nx-*` entkoppelt. Der nächste Schritt ist ein separater Audit der noch global konsumierten, nicht-NX-benannten Rollen und generischen Regeln aus `design-system.css`; erst danach darf der Legacy-Provider bedingt geladen werden.",

"Die globalen Cockpit-/Header-CTA-Regeln und die verbliebenen globalen Focus-Ringe konsumieren inzwischen ebenfalls direkt die Gutachten-Tokens statt `--nx-*`; `transition: all` wurde dort durch eigenschaftsspezifische Canon-Motion ersetzt und Reduced Motion ergänzt. Footer- und Homepage-Blöcke bleiben als nächste globale Migrationsziele bestehen.":
"Die globalen Cockpit-/Header-CTA-Regeln und die verbliebenen globalen Focus-Ringe konsumieren inzwischen ebenfalls direkt die Gutachten-Tokens statt `--nx-*`; `transition: all` wurde dort durch eigenschaftsspezifische Canon-Motion ersetzt und Reduced Motion ergänzt. Der alte Homepage-/Shortcode-Kompatibilitätsblock liegt jetzt bei seinem Legacy-Owner `homepage.css`, der Single-Light-Safety-Net bei `single.css`. Der nicht mehr gerenderte `.ft`-Light-Footer-Bridge ist entfernt. Damit enthält `style.css` **0 `var(--nx-...)`-Verwendungen** und blockiert den NX-Abbau nicht mehr selbst. Der globale Enqueue von `design-system.css` bleibt trotzdem bestehen, bis seine nicht-NX-benannten globalen Rollen und generischen Regeln separat auditiert sind.",

"1. **Globale Shell:** Audit-Header ist entkoppelt. `style.css` bleibt der letzte globale Blocker und wird als Nächstes in echte WordPress-/Font-Foundation versus alte Blocksy-/Route-Regeln zerlegt.":
"1. **Globale Shell (NX erledigt):** Audit-Header und `style.css` sind von `--nx-*` entkoppelt. Als Nächstes werden die noch global wirkenden, nicht-NX-benannten Provider-Rollen und generischen Regeln auditiert; erst dann wird `design-system.css` aus dem globalen Enqueue genommen oder bedingt geladen.",
}

for old, new in replacements.items():
    if text.count(old) != 1:
        raise SystemExit(f'Expected exactly one stale architecture paragraph, got {text.count(old)} for: {old[:80]}')
    text = text.replace(old, new)

if '**33 NX-Verbraucherdateien**' in text:
    raise SystemExit('Stale 33-consumer statement remains')
if '`style.css` ist jetzt der verbleibende globale Shell-Blocker' in text:
    raise SystemExit('Stale style.css blocker statement remains')
if 'Footer- und Homepage-Blöcke bleiben als nächste globale Migrationsziele bestehen.' in text:
    raise SystemExit('Stale footer/homepage target statement remains')

path.write_text(text, encoding='utf-8')
print('CSS architecture documentation updated to 31 consumers and NX-free style.css')

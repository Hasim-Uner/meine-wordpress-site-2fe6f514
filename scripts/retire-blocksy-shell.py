#!/usr/bin/env python3
from pathlib import Path


def remove_between(text: str, start: str, end: str, label: str):
    start_count = text.count(start)
    if start_count != 1:
        raise SystemExit(f'{label}: marker drift start={start_count}')
    a = text.index(start)
    try:
        b = text.index(end, a + len(start))
    except ValueError as exc:
        raise SystemExit(f'{label}: end marker missing after start') from exc
    if b <= a:
        raise SystemExit(f'{label}: invalid marker order')
    return text[:a] + text[b:], text[a:b]


style_path = Path('blocksy-child/style.css')
style = style_path.read_text(encoding='utf-8')
removed_style = ''

style, removed = remove_between(
    style,
    ":root[data-nx-theme='light'] .ct-header .site-logo,\n",
    '.site-logo--accent {\n',
    'retired Blocksy light wordmark override',
)
removed_style += removed

style, removed = remove_between(
    style,
    '.site-logo--accent {\n',
    '@media (max-width: 1023px) {\n',
    'retired Blocksy wordmark accent ornament',
)
removed_style += removed

style, removed = remove_between(
    style,
    '/* --- NEXUS MOBILE MENU SPACING FIX --- */',
    '/* --- NEXUS: Hide Customizer Shortcut X (only in Customizer) --- */',
    'retired Blocksy mobile panel',
)
removed_style += removed

style, removed = remove_between(
    style,
    '/* --- NEXUS HEADER: FLIGHT MODE --- */',
    '/* === ACCESSIBILITY FIX 1: Sichtbare Focus-States für CTAs und Navigation === */',
    'retired Blocksy flight header',
)
removed_style += removed

preserved = '''/* --- ACTIVE WORDMARK / SHORTCODE CTA STABILITY --- */\n.site-logo,\n.site-logo-container {\n    transition: color 0.3s ease, transform 0.3s ease;\n}\n\n.nexus-header-cta {\n    transform: none !important;\n    margin-top: 0 !important;\n}\n\n'''
marker = '/* === ACCESSIBILITY FIX 1: Sichtbare Focus-States für CTAs und Navigation === */'
if style.count(marker) != 1:
    raise SystemExit('accessibility marker drifted after Blocksy flight removal')
style = style.replace(marker, preserved + marker, 1)

style, removed = remove_between(
    style,
    ":root[data-nx-theme='light'] .ct-header.nexus-flight-mode {\n",
    ":root[data-nx-theme='light'] .ft {\n",
    'retired Blocksy light flight and mega-menu overrides',
)
removed_style += removed

for fragment in (
    'NEXUS MOBILE MENU SPACING FIX',
    'NEXUS HEADER: FLIGHT MODE',
    'nexus-flight-mode',
    '.ct-panel',
    '.menu-item.mega',
    '.site-logo--accent',
):
    if fragment in style:
        raise SystemExit(f'style.css: retired Blocksy fragment remains: {fragment}')

for active_marker in (
    'NEXUS ARCHIVE LAYOUT',
    'NEXUS CLIENT PORTAL',
    'LEGAL PAGE MODAL',
    'ACCESSIBILITY FIX 1',
):
    if active_marker not in style:
        raise SystemExit(f'style.css: active marker lost: {active_marker}')

if '.nexus-header-cta {' not in style or 'transform: none !important;' not in style:
    raise SystemExit('style.css: active nexus-header-cta stability rule was not preserved')
if '.site-logo,\n.site-logo-container {' not in style:
    raise SystemExit('style.css: active wordmark rule was not preserved')

print(f'Removed {removed_style.count(chr(10)) + 1} lines of unreachable Blocksy shell CSS')
style_path.write_text(style, encoding='utf-8')

js_path = Path('blocksy-child/assets/js/nexus-core.js')
js = js_path.read_text(encoding='utf-8')
js, removed_js = remove_between(
    js,
    '        /**\n         * 10. HEADER FLIGHT MODE\n',
    '        getPrimaryThemeToggle: function () {\n',
    'retired initHeaderFlight module',
)
invocation = '            // Header Flight Mode\n            this.initHeaderFlight();\n\n'
if js.count(invocation) != 1:
    raise SystemExit(f'nexus-core.js: header flight invocation count={js.count(invocation)}')
js = js.replace(invocation, '', 1)
if 'initHeaderFlight' in js or 'nexus-flight-mode' in js:
    raise SystemExit('nexus-core.js: retired header flight code remains')
print(f'Removed {removed_js.count(chr(10)) + 1} lines of unreachable header-flight JS')
js_path.write_text(js, encoding='utf-8')

doc_path = Path('docs/architecture/CSS_ARCHITECTURE.md')
doc = doc_path.read_text(encoding='utf-8')
old = 'Andere historische `.ct-header`-/`.ct-panel`-Regeln bleiben bewusst als separater Migrationsbestand bestehen und werden nicht durch einen pauschalen Selector-Guard mit diesem Schnitt vermischt. Archive- und Kundenportal-Blöcke bleiben ebenfalls bestehen, bis sie separat migriert oder ausgelagert sind.'
new = 'Der verbliebene Blocksy-Shell-Bestand (`.ct-header`, `.ct-panel`, Mega-Menü und Flight-Mode) ist anschließend ebenfalls entfernt worden: der Parent-Header ist serverseitig deaktiviert und diese Strukturen werden nicht mehr gerendert. Der zugehörige `initHeaderFlight()`-Fallback in `nexus-core.js` ist damit ebenfalls entfallen. Archive-, Kundenportal-, Footer- und Homepage-Blöcke bleiben bewusst bestehen und werden separat migriert.'
if doc.count(old) != 1:
    raise SystemExit(f'CSS_ARCHITECTURE.md: expected legacy shell sentence once, got {doc.count(old)}')
doc_path.write_text(doc.replace(old, new, 1), encoding='utf-8')

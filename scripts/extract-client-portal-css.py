#!/usr/bin/env python3
from pathlib import Path

STYLE = Path('blocksy-child/style.css')
ENQUEUE = Path('blocksy-child/inc/enqueue.php')
PORTAL = Path('blocksy-child/assets/css/client-portal.css')
DOC = Path('docs/architecture/CSS_ARCHITECTURE.md')


def replace_once(text: str, old: str, new: str, label: str) -> str:
    count = text.count(old)
    if count != 1:
        raise SystemExit(f'{label}: expected exactly one match, got {count}')
    return text.replace(old, new, 1)


def remove_between(text: str, start: str, end: str, label: str):
    if text.count(start) != 1:
        raise SystemExit(f'{label}: start marker count={text.count(start)}')
    a = text.index(start)
    try:
        b = text.index(end, a + len(start))
    except ValueError as exc:
        raise SystemExit(f'{label}: end marker missing after start') from exc
    return text[:a] + text[b:], text[a:b]


style = STYLE.read_text(encoding='utf-8')
style, dashboard_block = remove_between(
    style,
    '/* --- NEXUS CLIENT PORTAL --- */\n',
    '/* --- NEXUS HEADER BUTTON --- */',
    'client portal dashboard/login block',
)
style, portal_block = remove_between(
    style,
    '/* --- NEXUS PORTAL LAYOUT --- */\n',
    '/* --- NEXUS: Hide Customizer Shortcut X (only in Customizer) --- */',
    'client portal layout/upload block',
)

for fragment in (
    '.nexus-dashboard {', '.nexus-login {', '.nexus-login-card {',
    '.nexus-portal {', '.nexus-portal-container {', '.nd-upload-form {',
):
    if fragment in style:
        raise SystemExit(f'style.css: portal declaration remains: {fragment}')

# The global cockpit/login nav shortcode is intentionally NOT part of the
# route stylesheet. It is rendered by inc/snippets.php outside the portal page.
if '.nexus-nav-btn {' not in style:
    raise SystemExit('style.css: global nexus-nav-btn rule was lost')
if '.nexus-header-cta {' not in style:
    raise SystemExit('style.css: global nexus-header-cta rule was lost')

STYLE.write_text(style, encoding='utf-8')

portal_css = dashboard_block + '\n' + portal_block

# The second historical dashboard layer used unscoped helper selectors. The
# stylesheet is route-specific now, but scope them anyway so header/footer
# markup on the portal page cannot inherit dashboard helper styles.
for selector in (
    '.nd-grid', '.nd-card', '.nd-header', '.nd-welcome', '.nd-badge',
    '.nd-progress-wrap', '.nd-progress-bar', '.nd-item', '.muted', '.kpi-val',
    '.dot', '.status-active', '.status-done', '.span-2', '.span-full',
    '.nd-upload-form', '.nd-upload-list', '.nd-upload-item', '.nd-upload-note',
):
    portal_css = portal_css.replace('\n' + selector, '\n.nexus-dashboard ' + selector)
    portal_css = portal_css.replace('\n    ' + selector, '\n    .nexus-dashboard ' + selector)

# Remove the remaining dependency on design-system.css tokens from the portal
# route. The global provider still exists for other legacy consumers, but this
# stylesheet must not become one of them.
replacements = {
    'var(--nx-bg-glass-light)': 'color-mix(in srgb, var(--fg, #fff) 8%, transparent)',
    'var(--nx-bg-glass-hover)': 'color-mix(in srgb, var(--fg, #fff) 10%, transparent)',
    'var(--nx-border)': 'var(--line, rgba(255, 255, 255, 0.1))',
    'var(--nx-text-dim)': 'var(--text-dim, rgba(255, 255, 255, 0.6))',
    'var(--nx-text)': 'var(--fg, #fff)',
}
for old, new in replacements.items():
    portal_css = portal_css.replace(old, new)

# Portal wrapper gets stable local aliases instead of relying on historical
# provider names. Existing theme/brand variables remain the primary source.
portal_css = portal_css.replace(
    '/* --- NEXUS CLIENT PORTAL --- */\n',
    "/* Client Portal — route-specific, extracted from global style.css. */\n.nexus-portal {\n    --portal-bg: var(--bg, #090909);\n    --portal-fg: var(--fg, #f5f5f5);\n    --portal-line: var(--line, rgba(255, 255, 255, 0.1));\n    --portal-accent: var(--cta, var(--hasim-audit-orange, #b46a3c));\n}\n\n",
    1,
)
portal_css = portal_css.replace('var(--bg)', 'var(--portal-bg)')
portal_css = portal_css.replace('var(--fg)', 'var(--portal-fg)')
portal_css = portal_css.replace('var(--line)', 'var(--portal-line)')
portal_css = portal_css.replace('var(--cta)', 'var(--portal-accent)')

# The extracted source already avoids transition: all except the global header
# buttons, which were left in style.css. Add an explicit route-local motion
# fallback because dashboard cards and login actions still animate transforms.
portal_css += '''\n@media (prefers-reduced-motion: reduce) {\n    .nexus-dashboard .nd-card,\n    .nexus-dashboard .btn,\n    .nexus-login-card #nexus-login-form input[type="submit"] {\n        transition: none;\n        transform: none;\n    }\n\n    .nexus-dashboard .indicator {\n        animation: none;\n    }\n}\n'''

if '--nx-' in portal_css:
    raise SystemExit('client-portal.css must remain NX-free')
if 'transition: all' in portal_css:
    raise SystemExit('client-portal.css must not introduce transition: all')
for required in (
    '.nexus-dashboard', '.nexus-login-card', '.nexus-portal-container',
    '.nexus-dashboard .nd-upload-form', '.nexus-dashboard .nd-card',
):
    if required not in portal_css:
        raise SystemExit(f'client-portal.css missing required selector: {required}')
PORTAL.write_text(portal_css, encoding='utf-8')

enqueue = ENQUEUE.read_text(encoding='utf-8')
anchor = "\t$is_sst_route = is_page( 'server-side-tracking-b2b' ) || is_page_template( 'page-server-side-tracking-b2b.php' );\n"
enqueue = replace_once(
    enqueue,
    anchor,
    anchor + "\t$is_client_portal = is_page_template( 'template-portal.php' );\n",
    'client portal route flag',
)
insert_before = "\t// ── Audit header only ──────────────────────────────────────────\n"
portal_enqueue = "\t// ── Client Portal only ─────────────────────────────────────────\n\tif ( $is_client_portal ) {\n\t\thu_enqueue_css( 'nexus-client-portal-css', 'client-portal.css', [ 'blocksy-child-style' ] );\n\t}\n\n"
enqueue = replace_once(enqueue, insert_before, portal_enqueue + insert_before, 'client portal enqueue')
ENQUEUE.write_text(enqueue, encoding='utf-8')

doc = DOC.read_text(encoding='utf-8')
old = 'Die neue Datei verwendet keine `--nx-*`-Variablen; Kundenportal-, Footer- und Homepage-Blöcke bleiben als nächste globale Migrationsziele bestehen.'
new = 'Die neue Datei verwendet keine `--nx-*`-Variablen. Der Kundenportal-Block ist ebenfalls aus `style.css` in `assets/css/client-portal.css` ausgelagert und wird ausschließlich über `template-portal.php` geladen; auch diese Datei ist NX-frei. Footer- und Homepage-Blöcke bleiben als nächste globale Migrationsziele bestehen.'
doc = replace_once(doc, old, new, 'architecture client portal migration note')
DOC.write_text(doc, encoding='utf-8')

print(f'Extracted {dashboard_block.count(chr(10)) + portal_block.count(chr(10)) + 2} lines of portal CSS from global style.css')

#!/usr/bin/env python3
from pathlib import Path

STYLE = Path('blocksy-child/style.css')
ENQUEUE = Path('blocksy-child/inc/enqueue.php')
ARCHIVE = Path('blocksy-child/assets/css/archive.css')
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
style, old_archive_block = remove_between(
    style,
    '/* --- NEXUS ARCHIVE LAYOUT --- */\n',
    '/* NOTFALL-FIX: Versteckt den Hero auf allen Seiten, die KEIN Blogpost sind */',
    'archive base block',
)

# Remove archive-only selectors from late mixed light-theme compatibility rules.
style = replace_once(
    style,
    ":root[data-nx-theme='light'] .nexus-single-container,\n:root[data-nx-theme='light'] .nexus-archive-container {\n  background: var(--nx-body-gradient);\n  color: var(--nx-text);\n}\n",
    ":root[data-nx-theme='light'] .nexus-single-container {\n  background: var(--nx-body-gradient);\n  color: var(--nx-text);\n}\n",
    'mixed single/archive container light rule',
)
style = replace_once(
    style,
    ":root[data-nx-theme='light'] .nexus-article-hero,\n:root[data-nx-theme='light'] .nexus-archive-hero,\n:root[data-nx-theme='light'] .nexus-hero-footer {\n  border-bottom-color: var(--nx-border);\n  border-top-color: var(--nx-border);\n}\n\n:root[data-nx-theme='light'] .nexus-archive-hero {\n  background: linear-gradient(to bottom, #fbf7f1 0%, #f1e9dc 100%);\n}\n",
    ":root[data-nx-theme='light'] .nexus-article-hero,\n:root[data-nx-theme='light'] .nexus-hero-footer {\n  border-bottom-color: var(--nx-border);\n  border-top-color: var(--nx-border);\n}\n",
    'mixed archive hero light rule',
)
style = replace_once(
    style,
    ":root[data-nx-theme='light'] .nexus-meta-top,\n:root[data-nx-theme='light'] .nexus-author-info .by,\n:root[data-nx-theme='light'] .nexus-post-footer-cta p,\n:root[data-nx-theme='light'] .nexus-archive-desc,\n:root[data-nx-theme='light'] .nexus-card-date,\n:root[data-nx-theme='light'] .nexus-card-excerpt,\n:root[data-nx-theme='light'] .share-label,\n",
    ":root[data-nx-theme='light'] .nexus-meta-top,\n:root[data-nx-theme='light'] .nexus-author-info .by,\n:root[data-nx-theme='light'] .nexus-post-footer-cta p,\n:root[data-nx-theme='light'] .share-label,\n",
    'archive muted light selectors',
)
style = replace_once(
    style,
    ":root[data-nx-theme='light'] .nexus-title,\n:root[data-nx-theme='light'] .nexus-author-info .name,\n:root[data-nx-theme='light'] .nexus-article-content h2,\n:root[data-nx-theme='light'] .nexus-article-content h3,\n:root[data-nx-theme='light'] .nexus-post-footer-cta h3,\n:root[data-nx-theme='light'] .nexus-card-title,\n:root[data-nx-theme='light'] .nexus-pagination .page-numbers {\n",
    ":root[data-nx-theme='light'] .nexus-title,\n:root[data-nx-theme='light'] .nexus-author-info .name,\n:root[data-nx-theme='light'] .nexus-article-content h2,\n:root[data-nx-theme='light'] .nexus-article-content h3,\n:root[data-nx-theme='light'] .nexus-post-footer-cta h3 {\n",
    'archive title light selectors',
)
style = replace_once(
    style,
    ":root[data-nx-theme='light'] .nexus-post-footer-cta,\n:root[data-nx-theme='light'] .nexus-card,\n:root[data-nx-theme='light'] .selection-card,\n:root[data-nx-theme='light'] .nexus-share-btn,\n:root[data-nx-theme='light'] .nexus-pagination .page-numbers {\n",
    ":root[data-nx-theme='light'] .nexus-post-footer-cta,\n:root[data-nx-theme='light'] .selection-card,\n:root[data-nx-theme='light'] .nexus-share-btn {\n",
    'archive card light selectors',
)

for fragment in (
    '.nexus-archive-container', '.nexus-archive-hero', '.nexus-archive-desc',
    '.nexus-card-grid', '.nexus-card-date', '.nexus-card-title',
    '.nexus-card-excerpt', '.nexus-read-more', '.nexus-pagination',
):
    if fragment in style:
        raise SystemExit(f'style.css: archive fragment remains after extraction: {fragment}')

STYLE.write_text(style, encoding='utf-8')

archive_css = '''/* Archive route — extracted from global style.css.\n * Uses non-NX core roles so this route does not create a new legacy NX consumer.\n */\n.nexus-archive-container {\n    background-color: hsl(30 6% 6%);\n    min-height: 100vh;\n    color: var(--text-primary);\n}\n\n.nexus-archive-hero {\n    padding: 140px 20px 60px;\n    text-align: center;\n    background: linear-gradient(to bottom, var(--bg-base) 0%, hsl(30 6% 6%) 100%);\n    border-bottom: 1px solid var(--border-subtle);\n}\n\n.nexus-archive-desc {\n    max-width: 700px;\n    margin: 24px auto 0;\n    font-size: 1.25rem;\n    line-height: 1.6;\n    color: var(--text-tertiary);\n}\n\n.nexus-archive-desc p { margin-bottom: 0; }\n\n.nexus-grid-wrapper {\n    max-width: 1200px;\n    margin: 80px auto;\n    padding: 0 20px;\n}\n\n.nexus-card-grid {\n    display: grid;\n    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));\n    gap: 40px;\n}\n\n.nexus-card {\n    background: linear-gradient(180deg, hsl(30 5% 9% / 0.92), hsl(30 6% 6% / 0.98));\n    border: 1px solid var(--border-subtle);\n    border-radius: 8px;\n    transition: transform 0.3s ease, border-color 0.3s ease;\n    height: 100%;\n    display: flex;\n    flex-direction: column;\n}\n\n.nexus-card:hover {\n    transform: translateY(-5px);\n    border-color: hsl(var(--accent-hsl) / 0.28);\n}\n\n.nexus-card-link {\n    text-decoration: none;\n    padding: 32px;\n    display: flex;\n    flex-direction: column;\n    height: 100%;\n    color: inherit;\n}\n\n.nexus-card-date {\n    font-size: 12px;\n    color: var(--text-tertiary);\n    margin-bottom: 16px;\n    letter-spacing: 0.05em;\n}\n\n.nexus-card-title {\n    font-family: var(--theme-font-heading);\n    font-size: 1.5rem;\n    font-weight: 700;\n    color: var(--text-primary);\n    margin-bottom: 16px;\n    line-height: 1.3;\n}\n\n.nexus-card-excerpt {\n    font-size: 1rem;\n    color: var(--text-tertiary);\n    line-height: 1.6;\n    margin-bottom: 24px;\n    flex-grow: 1;\n}\n\n.nexus-read-more {\n    font-size: 13px;\n    font-weight: 700;\n    color: var(--accent);\n    text-transform: uppercase;\n    letter-spacing: 0.1em;\n}\n\n.nexus-pagination {\n    margin-top: 80px;\n    text-align: center;\n}\n\n.nexus-pagination .page-numbers {\n    display: inline-block;\n    padding: 10px 16px;\n    margin: 0 4px;\n    border: 1px solid var(--border-subtle);\n    color: var(--text-primary);\n    text-decoration: none;\n    border-radius: 4px;\n}\n\n.nexus-pagination .page-numbers.current,\n.nexus-pagination .page-numbers:hover {\n    background: var(--accent);\n    color: #000;\n    border-color: var(--accent);\n    font-weight: 700;\n}\n\n:root[data-nx-theme='light'] .nexus-archive-container {\n    background: linear-gradient(180deg, var(--bg-base) 0%, hsl(35 12% 92%) 100%);\n    color: var(--text-primary);\n}\n\n:root[data-nx-theme='light'] .nexus-archive-hero {\n    background: linear-gradient(to bottom, #fbf7f1 0%, #f1e9dc 100%);\n    border-bottom-color: var(--border-subtle);\n    border-top-color: var(--border-subtle);\n}\n\n:root[data-nx-theme='light'] .nexus-archive-desc,\n:root[data-nx-theme='light'] .nexus-card-date,\n:root[data-nx-theme='light'] .nexus-card-excerpt {\n    color: var(--text-secondary) !important;\n}\n\n:root[data-nx-theme='light'] .nexus-card-title,\n:root[data-nx-theme='light'] .nexus-pagination .page-numbers {\n    color: var(--text-primary) !important;\n}\n\n:root[data-nx-theme='light'] .nexus-card,\n:root[data-nx-theme='light'] .nexus-pagination .page-numbers {\n    background: linear-gradient(180deg, hsl(35 14% 100% / 0.98), hsl(35 10% 93% / 0.98));\n    border-color: var(--border-subtle) !important;\n    box-shadow: var(--shadow-sm);\n}\n\n@media (max-width: 600px) {\n    .nexus-card-grid { grid-template-columns: 1fr; }\n}\n\n@media (prefers-reduced-motion: reduce) {\n    .nexus-card { transition: none; }\n}\n'''
if '--nx-' in archive_css:
    raise SystemExit('archive.css must remain NX-free')
ARCHIVE.write_text(archive_css, encoding='utf-8')

enqueue = ENQUEUE.read_text(encoding='utf-8')
old = "\tif ( is_archive() && ! is_home() && ! is_category() ) {\n\t\thu_enqueue_css( 'nexus-home-css', 'homepage.css', [ 'nexus-design-system' ] );\n\t}\n"
new = "\tif ( is_archive() && ! is_home() && ! is_category() ) {\n\t\thu_enqueue_css( 'nexus-archive-css', 'archive.css', [ 'nexus-design-system' ] );\n\t\thu_enqueue_css( 'nexus-home-css', 'homepage.css', [ 'nexus-design-system' ] );\n\t}\n"
enqueue = replace_once(enqueue, old, new, 'archive enqueue block')
ENQUEUE.write_text(enqueue, encoding='utf-8')

doc = DOC.read_text(encoding='utf-8')
anchor = 'Archive-, Kundenportal-, Footer- und Homepage-Blöcke bleiben bewusst bestehen und werden separat migriert.'
replacement = 'Der Archive-Block ist anschließend aus `style.css` in `assets/css/archive.css` ausgelagert worden und wird nur auf sonstigen Archiv-Routen geladen. Die neue Datei verwendet keine `--nx-*`-Variablen; Kundenportal-, Footer- und Homepage-Blöcke bleiben als nächste globale Migrationsziele bestehen.'
doc = replace_once(doc, anchor, replacement, 'architecture archive migration note')
DOC.write_text(doc, encoding='utf-8')

print(f'Extracted {old_archive_block.count(chr(10)) + 1} lines of archive CSS from global style.css')

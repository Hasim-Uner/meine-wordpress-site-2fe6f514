#!/usr/bin/env python3
from pathlib import Path


def remove_between(source: str, start: str, end: str, label: str) -> tuple[str, str]:
    if source.count(start) != 1 or source.count(end) != 1:
        raise SystemExit(f'{label}: marker drift start={source.count(start)} end={source.count(end)}')
    a = source.index(start)
    b = source.index(end, a)
    if b <= a:
        raise SystemExit(f'{label}: invalid marker order')
    return source[:a] + source[b:], source[a:b]


style = Path('blocksy-child/style.css')
text = style.read_text(encoding='utf-8')
removals = [
    ('/* Menü-Links: Clean & Spaced */', '/* --- Homepage Optimization: Hero Stack + Section Order --- */', 'retired Blocksy menu and header CTA'),
    ('@media (min-width: 1024px) {\n/* --- 2. NEXUS MEGA MENÜ (VIEWPORT FIX) --- */', '/* --- 3. INLINE-PFEIL --- */', 'retired Blocksy mega menu'),
    ('/* --- NEXUS SINGLE PAGE LAYOUT --- */', '/* --- NEXUS ARCHIVE LAYOUT --- */', 'superseded single-post base'),
    ('/* --- NEXUS FEINSCHLIFF: LESEZEIT & SHARING --- */', '/* --- NEXUS CLIENT PORTAL --- */', 'superseded single-post share finishing'),
]
removed_total = ''
for start, end, label in removals:
    text, removed = remove_between(text, start, end, label)
    removed_total += removed
    print(f'Removed {label}: {removed.count(chr(10)) + 1} lines')

for fragment in ('.ct-header .ct-menu > li > a', 'NEXUS MEGA MENÜ (VIEWPORT FIX)', '.nexus-single-container', '.nexus-share-box'):
    if fragment not in removed_total:
        raise SystemExit(f'expected cleanup fragment was not removed: {fragment}')
for marker in ('/* Menü-Links: Clean & Spaced */', 'NEXUS MEGA MENÜ (VIEWPORT FIX)', 'NEXUS SINGLE PAGE LAYOUT', 'NEXUS FEINSCHLIFF: LESEZEIT & SHARING'):
    if marker in text:
        raise SystemExit(f'retired block marker remains: {marker}')
for marker in ('NEXUS ARCHIVE LAYOUT', 'NEXUS CLIENT PORTAL', 'LEGAL PAGE MODAL', 'Homepage Optimization: Hero Stack + Section Order'):
    if marker not in text:
        raise SystemExit(f'live marker was lost: {marker}')

single = Path('blocksy-child/assets/css/single.css').read_text(encoding='utf-8')
for selector in ('.nexus-single-container', '.nexus-article-hero', '.nexus-meta-top', '.nexus-title', '.nexus-hero-footer', '.nexus-share-box', '.nexus-share-btn'):
    if selector not in single:
        raise SystemExit(f'single.css no longer owns required selector: {selector}')
style.write_text(text, encoding='utf-8')

build = Path('scripts/build-theme-dist.sh')
text = build.read_text(encoding='utf-8')
text = text.replace('single_css="$sst_css_dir/single.css"\n', '')
start = '# The source still carries one historical single-post base block in style.css.\n'
end = 'if [ -f "$style_file" ]; then\n'
if text.count(start) != 1 or text.count(end) != 1:
    raise SystemExit('build-theme-dist.sh: single-prune block markers drifted')
a = text.index(start)
b = text.index(end, a)
text = text[:a] + text[b:]
if 'prune-style-single-legacy.py' in text:
    raise SystemExit('build-theme-dist.sh still references retired single pruner')
build.write_text(text, encoding='utf-8')

doc = Path('docs/architecture/CSS_ARCHITECTURE.md')
text = doc.read_text(encoding='utf-8')
start = '### Deployment-Deduplizierung von `style.css`\n'
end = '## 4. Bewusste lokale Systeme\n'
if text.count(start) != 1 or text.count(end) != 1:
    raise SystemExit('CSS_ARCHITECTURE.md: style cleanup section markers drifted')
a = text.index(start)
b = text.index(end, a)
replacement = '''### Source-Bereinigung von `style.css`\n\nDer historische `NEXUS SINGLE PAGE LAYOUT`-Block und sein altes Share-Finishing sind jetzt auch aus dem Authoring-Source entfernt. `assets/css/single.css` besitzt die produktiven Single-/SEO-Cornerstone-Selektoren vollständig; der frühere Deployment-Pruner ist deshalb entfallen.\n\nZusätzlich wurden die serverseitig nicht mehr renderbaren Blocksy-Menü-/CTA- und Mega-Menü-Blöcke aus `style.css` entfernt. Der Parent-Header ist über `blocksy:builder:header:enabled` deaktiviert; die aktuelle Standardnavigation wird als `.leiste` gerendert. Andere historische `.ct-header`-/`.ct-panel`-Regeln bleiben bewusst als separater Migrationsbestand bestehen. Archive- und Kundenportal-Blöcke bleiben ebenfalls bestehen, bis sie separat migriert oder ausgelagert sind.\n\n'''
doc.write_text(text[:a] + replacement + text[b:], encoding='utf-8')

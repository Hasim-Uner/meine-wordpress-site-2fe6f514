from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
STYLE = ROOT / 'blocksy-child/style.css'
HOME = ROOT / 'blocksy-child/assets/css/homepage.css'
SINGLE = ROOT / 'blocksy-child/assets/css/single.css'
BASELINE = ROOT / 'scripts/baselines/legacy-nx-consumers.txt'

style = STYLE.read_text(encoding='utf-8')
home = HOME.read_text(encoding='utf-8')
single = SINGLE.read_text(encoding='utf-8')
baseline = BASELINE.read_text(encoding='utf-8')

if style.count('var(--nx-') != 29:
    raise SystemExit(f'Expected 29 style.css NX uses before migration, got {style.count("var(--nx-")}')

baseline_entry = 'blocksy-child/style.css'
if baseline.splitlines().count(baseline_entry) != 1:
    raise SystemExit('Expected style.css exactly once in legacy NX baseline')

home_marker = '/* ═══════════════════════════════════════════\n   HOMEPAGE FIXES – Februar 2026\n   ═══════════════════════════════════════════ */'
footer_marker = ":root[data-nx-theme='light'] .ft {"
single_marker = ":root[data-nx-theme='light'] .nexus-single-container {"
fix7_marker = '/* --- FIX 7: Screenreader-only Klasse (falls noch nicht vorhanden) --- */'

for marker in (home_marker, footer_marker, single_marker):
    if style.count(marker) != 1:
        raise SystemExit(f'Expected exactly one style marker: {marker!r}')

if 'STYLE-ROUTE-OWNER-BRIDGE' in home or 'STYLE-SINGLE-LIGHT-BRIDGE' in single:
    raise SystemExit('Migration bridge already present in destination stylesheet')

# 1) Move the old homepage compatibility rules to their actual legacy owner.
home_start = style.index(home_marker)
footer_start = style.index(footer_marker, home_start)
home_block_full = style[home_start:footer_start].rstrip()
if fix7_marker not in home_block_full:
    raise SystemExit('Homepage FIX 7 marker missing')
home_block = home_block_full.split(fix7_marker, 1)[0].rstrip()
style = style[:home_start] + style[footer_start:]

# 2) The old .ft footer system no longer renders. Remove its complete light bridge.
footer_start = style.index(footer_marker)
single_start = style.index(single_marker, footer_start)
style = style[:footer_start] + style[single_start:]

# 3) Replace the remaining mixed tail with one genuinely global table hint.
#    Single and legacy-home selectors are rehomed below.
single_start = style.index(single_marker)
style = style[:single_start].rstrip() + '''\n\n/* Light-theme contrast for the global mobile table scroll hint.\n   WGOS keeps the component rules; the global shell owns only the readable hint. */\n:root[data-nx-theme='light'] .wp-block-table::before,\n:root[data-nx-theme='light'] .wgos-table-wrap::before {\n  color: var(--text-secondary) !important;\n}\n'''

home_light_bridge = r'''/* STYLE-ROUTE-OWNER-BRIDGE
   These selectors used to live in global style.css. They belong to the
   legacy homepage/shortcode surface, whose base stylesheet is homepage.css.
   Kept before the existing homepage rules so the historical cascade remains. */

:root[data-nx-theme='light'] .micro-cta-link {
  color: var(--nx-text-muted) !important;
}

:root[data-nx-theme='light'] .selection-card {
  background: var(--nx-card-gradient);
  border-color: var(--nx-border) !important;
  box-shadow: var(--nx-shadow-sm);
}

:root[data-nx-theme='light'] body.home .hero-cta-secondary,
:root[data-nx-theme='light'] body.home .wp-block-buttons .wp-block-button:last-child .wp-block-button__link,
:root[data-nx-theme='light'] body.home a[href*="case-studies"].wp-block-button__link,
:root[data-nx-theme='light'] body.home .wp-hero .wp-btn[href*="case-studies"],
:root[data-nx-theme='light'] body.home .cs-page #hero a.wp-btn.wp-btn-secondary,
:root[data-nx-theme='light'] body.home .cs-page #hero a[href*="case-studies"],
:root[data-nx-theme='light'] .wp-block-buttons .wp-block-button:nth-child(2) .wp-block-button__link,
:root[data-nx-theme='light'] .hero-cta-secondary .wp-block-button__link,
:root[data-nx-theme='light'] a.wp-block-button__link[href*="case-studies"] {
  color: var(--nx-text-secondary) !important;
  border: 1px solid var(--nx-border) !important;
}

:root[data-nx-theme='light'] body.home .hero-cta-secondary:hover,
:root[data-nx-theme='light'] body.home .wp-block-buttons .wp-block-button:last-child .wp-block-button__link:hover,
:root[data-nx-theme='light'] body.home .wp-hero .wp-btn[href*="case-studies"]:hover,
:root[data-nx-theme='light'] body.home .cs-page #hero a.wp-btn.wp-btn-secondary:hover,
:root[data-nx-theme='light'] body.home .cs-page #hero a[href*="case-studies"]:hover,
:root[data-nx-theme='light'] .wp-block-buttons .wp-block-button:nth-child(2) .wp-block-button__link:hover,
:root[data-nx-theme='light'] a.wp-block-button__link[href*="case-studies"]:hover {
  border-color: var(--nx-border-hover) !important;
  color: var(--nx-text) !important;
}

:root[data-nx-theme='light'] body.home .cs-page #owned .wp-cards > .wp-success-card:first-child,
:root[data-nx-theme='light'] .modell-a,
:root[data-nx-theme='light'] [class*="modell-a"],
:root[data-nx-theme='light'] .model-a {
  border-color: var(--nx-border) !important;
}

:root[data-nx-theme='light'] .selection-card-label {
  color: var(--nx-accent-text);
}
'''

single_light_bridge = r'''/* STYLE-SINGLE-LIGHT-BRIDGE
   Former global style.css safety net. Kept at the beginning of single.css so
   later route-native rules retain the same cascade while global CSS is clean. */

:root[data-nx-theme='light'] .nexus-single-container {
  background: var(--nx-body-gradient);
  color: var(--nx-text);
}

:root[data-nx-theme='light'] .nexus-article-hero,
:root[data-nx-theme='light'] .nexus-hero-footer {
  border-bottom-color: var(--nx-border);
  border-top-color: var(--nx-border);
}

:root[data-nx-theme='light'] .nexus-meta-top,
:root[data-nx-theme='light'] .nexus-author-info .by,
:root[data-nx-theme='light'] .nexus-post-footer-cta p,
:root[data-nx-theme='light'] .share-label,
:root[data-nx-theme='light'] .nexus-reading-time {
  color: var(--nx-text-muted) !important;
}

:root[data-nx-theme='light'] .nexus-title,
:root[data-nx-theme='light'] .nexus-author-info .name,
:root[data-nx-theme='light'] .nexus-article-content h2,
:root[data-nx-theme='light'] .nexus-article-content h3,
:root[data-nx-theme='light'] .nexus-post-footer-cta h3 {
  color: var(--nx-text) !important;
}

:root[data-nx-theme='light'] .nexus-author-row img {
  border-color: var(--nx-border);
}

:root[data-nx-theme='light'] .nexus-article-content {
  color: var(--nx-text-secondary);
}

:root[data-nx-theme='light'] .nexus-post-footer-cta,
:root[data-nx-theme='light'] .nexus-share-btn {
  background: var(--nx-card-gradient);
  border-color: var(--nx-border) !important;
  box-shadow: var(--nx-shadow-sm);
}
'''

# Prepend compatibility bridges after each file's opening banner. This preserves
# the old global-before-route-specific cascade.
home_insert = home.find('\n\n')
if home_insert < 0:
    raise SystemExit('homepage.css opening banner boundary not found')
home = home[:home_insert + 2] + home_block + '\n\n' + home_light_bridge + '\n' + home[home_insert + 2:]

single_insert = single.find('\n\n')
if single_insert < 0:
    raise SystemExit('single.css opening banner boundary not found')
single = single[:single_insert + 2] + single_light_bridge + '\n' + single[single_insert + 2:]

# style.css is no longer an NX consumer, so the shrink-only contract must shrink.
baseline_lines = baseline.splitlines()
baseline_lines.remove(baseline_entry)
baseline = '\n'.join(baseline_lines) + '\n'

if 'var(--nx-' in style:
    raise SystemExit(f'style.css still has {style.count("var(--nx-")} NX uses after migration')
if footer_marker in style or home_marker in style or single_marker in style:
    raise SystemExit('A migrated route-owner marker still remains in style.css')
if baseline_entry in baseline.splitlines():
    raise SystemExit('style.css still present in legacy NX baseline')

STYLE.write_text(style, encoding='utf-8')
HOME.write_text(home, encoding='utf-8')
SINGLE.write_text(single, encoding='utf-8')
BASELINE.write_text(baseline, encoding='utf-8')

print('style.css NX uses: 29 -> 0')
print('Legacy NX consumer baseline: 32 -> 31 files')
print('Moved legacy homepage rules to homepage.css')
print('Moved single light safety net to single.css')
print('Removed retired .ft light footer bridge')

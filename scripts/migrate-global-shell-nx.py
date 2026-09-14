#!/usr/bin/env python3
from pathlib import Path

STYLE = Path('blocksy-child/style.css')
DOC = Path('docs/architecture/CSS_ARCHITECTURE.md')


def replace_once(text: str, old: str, new: str, label: str) -> str:
    count = text.count(old)
    if count != 1:
        raise SystemExit(f'{label}: expected exactly one match, got {count}')
    return text.replace(old, new, 1)


style = STYLE.read_text(encoding='utf-8')
before_nx = style.count('var(--nx-')
if before_nx != 43:
    raise SystemExit(f'style.css: expected 43 NX uses before migration, got {before_nx}')

style = replace_once(
    style,
    '''.nexus-nav-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    text-decoration: none !important;
    transition: all 0.2s ease;
    line-height: 1;
}

/* Zustand: Ausgeloggt (Ghost Button) */
.nexus-nav-btn {
    background: var(--nx-bg-glass-light);
    border: 1px solid var(--nx-border);
    color: var(--nx-text) !important;
}
.nexus-nav-btn:hover {
    background: var(--nx-bg-glass-hover);
    border-color: var(--nx-border-hover);
    transform: translateY(-1px);
}

/* Zustand: Eingeloggt (Active / Gold) */
.nexus-nav-btn.active {
    background: color-mix(in srgb, var(--cta) 15%, transparent);
    border: 1px solid color-mix(in srgb, var(--cta) 30%, transparent);
    color: var(--cta) !important;
}
.nexus-nav-btn.active:hover {
    background: color-mix(in srgb, var(--cta) 25%, transparent);
    box-shadow: 0 0 15px color-mix(in srgb, var(--cta) 20%, transparent);
}

/* Der goldene Punkt */
.indicator {
    font-size: 1.2em;
    line-height: 0;
    color: var(--cta);
    animation: pulse 2s infinite;
}
''',
    '''.nexus-nav-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    text-decoration: none !important;
    transition:
        background-color var(--t-norm) var(--ease-weich),
        border-color var(--t-norm) var(--ease-weich),
        color var(--t-norm) var(--ease-weich),
        transform var(--t-norm) var(--ease-aus),
        box-shadow var(--t-norm) var(--ease-weich);
    line-height: 1;
}

/* Zustand: Ausgeloggt (Ghost Button) */
.nexus-nav-btn {
    background: color-mix(in srgb, var(--tinte) 6%, transparent);
    border: 1px solid var(--haar);
    color: var(--tinte) !important;
}
.nexus-nav-btn:hover {
    background: color-mix(in srgb, var(--tinte) 10%, transparent);
    border-color: color-mix(in srgb, var(--stempel) 32%, var(--haar));
    transform: translateY(-1px);
}

/* Zustand: Eingeloggt (Active / Stempel) */
.nexus-nav-btn.active {
    background: color-mix(in srgb, var(--stempel) 15%, transparent);
    border: 1px solid color-mix(in srgb, var(--stempel) 30%, transparent);
    color: var(--stempel) !important;
}
.nexus-nav-btn.active:hover {
    background: color-mix(in srgb, var(--stempel) 25%, transparent);
    box-shadow: 0 0 15px color-mix(in srgb, var(--stempel) 20%, transparent);
}

/* Statuspunkt */
.indicator {
    font-size: 1.2em;
    line-height: 0;
    color: var(--stempel);
    animation: pulse 2s infinite;
}
''',
    'global cockpit nav button',
)

style = replace_once(
    style,
    '''.nexus-header-cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-decoration: none !important;
    background: var(--cta);
    color: #000 !important;
    border: none;
    transition: all 0.2s ease;
    line-height: 1;
}
.nexus-header-cta:hover {
    background: var(--hasim-audit-orange-hover);
    transform: translateY(-1px);
    box-shadow: 0 4px 16px color-mix(in srgb, var(--cta) 30%, transparent);
}
''',
    '''.nexus-header-cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-decoration: none !important;
    background: var(--stempel);
    color: var(--papier) !important;
    border: none;
    transition:
        background-color var(--t-norm) var(--ease-weich),
        transform var(--t-norm) var(--ease-aus),
        box-shadow var(--t-norm) var(--ease-weich);
    line-height: 1;
}
.nexus-header-cta:hover {
    background: color-mix(in srgb, var(--stempel) 88%, var(--tinte));
    transform: translateY(-1px);
    box-shadow: 0 4px 16px color-mix(in srgb, var(--stempel) 30%, transparent);
}

@media (prefers-reduced-motion: reduce) {
    .nexus-nav-btn,
    .nexus-header-cta {
        transition: none;
        transform: none;
    }

    .indicator {
        animation: none;
    }
}
''',
    'global header CTA',
)

focus_uses = style.count('var(--nx-focus-ring)')
if focus_uses != 4:
    raise SystemExit(f'style.css: expected 4 NX focus uses, got {focus_uses}')
style = style.replace('var(--nx-focus-ring)', 'var(--stempel)')

after_nx = style.count('var(--nx-')
if after_nx != 34:
    raise SystemExit(f'style.css: expected 34 NX uses after migration, got {after_nx}')
if 'transition: all' in style:
    raise SystemExit('style.css: transition: all remains after global shell migration')

STYLE.write_text(style, encoding='utf-8')

doc = DOC.read_text(encoding='utf-8')
old = 'Footer- und Homepage-Blöcke bleiben als nächste globale Migrationsziele bestehen.'
new = 'Die globalen Cockpit-/Header-CTA-Regeln und die verbliebenen globalen Focus-Ringe konsumieren inzwischen ebenfalls direkt die Gutachten-Tokens statt `--nx-*`; `transition: all` wurde dort durch eigenschaftsspezifische Canon-Motion ersetzt und Reduced Motion ergänzt. Footer- und Homepage-Blöcke bleiben als nächste globale Migrationsziele bestehen.'
doc = replace_once(doc, old, new, 'architecture global shell migration note')
DOC.write_text(doc, encoding='utf-8')

print(f'style.css NX uses: {before_nx} -> {after_nx}')

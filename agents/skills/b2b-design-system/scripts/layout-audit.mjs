#!/usr/bin/env node
/**
 * Layout-Audit: gerenderte Geometrie messen statt Design beurteilen.
 *
 * b2b-design-system definiert das Soll (Tokens, Raster, Abstaende). Dieses
 * Skript prueft das Ist an der gerenderten Seite: echte Kanten, echte Kaesten,
 * echte Ueberlaeufe.
 *
 * Bewusst ohne Baseline. Jede Pruefung ist eine absolute Zusicherung, kein
 * Vergleich mit einem Referenz-Screenshot — es gibt also nichts zu pflegen und
 * nichts abzunicken, und Font-Rendering erzeugt keine Fehlalarme.
 *
 * WICHTIG — Treuepruefung vor der Messung:
 * Zahlen aus einer falsch aufgebauten Seite sind schlechter als Augenmass, weil
 * sie autoritativ aussehen. Wer eine Test-Seite ohne den Token-Wrapper rendert,
 * misst ein Layout ohne Abstaende und "findet" Fehler, die es nicht gibt. Genau
 * das ist beim Bau dieses Skripts passiert. Darum bricht das Audit ab, wenn
 * --expect fehlt oder das Token nicht aufloest, statt zu messen.
 *
 * Nutzung:
 *   node layout-audit.mjs <url> [Optionen]
 *
 *   --viewport=390x844        Wiederholbar, Default 390x844 und 1280x900
 *   --expect=<selector>       Muss existieren, sonst Abbruch (Treuepruefung)
 *   --expect-token=<prop>     CSS-Custom-Property, die am --expect-Element
 *                             aufloesen muss, z. B. --ag-bg
 *   --shot=<pfad.png>         Screenshot mit eingezeichneten Fundstellen
 *   --json                    Maschinenlesbar statt Text
 *
 * Beispiel:
 *   node layout-audit.mjs file:///tmp/probe.html \
 *     --expect=.wp-agentur-page-wrapper --expect-token=--ag-bg --shot=/tmp/o.png
 *
 * Exit 0 = keine Befunde, 1 = Befunde, 2 = konnte nicht messen.
 *
 * Braucht playwright und einen Chromium. Laeuft nicht in der CI: das Audit
 * misst eine gerenderte Seite und wird bei Design-Arbeit von Hand aufgerufen.
 */

import { existsSync } from 'node:fs';

const TAP_MIN = 44;        // WCAG 2.2 AA, Target Size (Minimum)
const NEAR_MISS_MAX = 7;   // 8px ist die Rastereinheit — 1..7px ist Schlamperei
const GRID = 4;            // Abstaende sollten Vielfache davon sein

const args = process.argv.slice(2);
const url = args.find((a) => !a.startsWith('--'));
const opt = (name, fallback = '') => {
  const hit = args.find((a) => a.startsWith(`--${name}=`));
  return hit ? hit.slice(name.length + 3) : fallback;
};
const flag = (name) => args.includes(`--${name}`);

if (!url) {
  console.error('Fehlt: URL. --help steht im Dateikopf.');
  process.exit(2);
}

let chromium;
try {
  ({ chromium } = await import('playwright'));
} catch {
  console.error('playwright fehlt.\n');
  console.error('Bewusst keine devDependency: ci.yml und deploy.yml rufen beide "npm ci",');
  console.error('und playwright zieht dabei jedes Mal einen Browser nach. Dieses Audit');
  console.error('laeuft von Hand bei Design-Arbeit, nicht in der Pipeline.\n');
  console.error('Im Repo-Wurzelverzeichnis einmalig:');
  console.error('  PLAYWRIGHT_SKIP_BROWSER_DOWNLOAD=1 npm i --no-save playwright');
  console.error('  npx playwright install chromium      # nur falls kein Chromium vorhanden');
  process.exit(2);
}

const viewports = args
  .filter((a) => a.startsWith('--viewport='))
  .map((a) => a.split('=')[1])
  .map((v) => {
    const [w, h] = v.split('x').map(Number);
    return { width: w, height: h || 900 };
  });
if (!viewports.length) viewports.push({ width: 390, height: 844 }, { width: 1280, height: 900 });

const expectSel = opt('expect');
const expectToken = opt('expect-token');
const shotPath = opt('shot');

const execPath = existsSync('/opt/pw-browsers/chromium') ? '/opt/pw-browsers/chromium' : undefined;
const browser = await chromium.launch(execPath ? { executablePath: execPath } : {});

/** Im Seitenkontext: messen. Gibt nur Zahlen zurueck, keine Urteile. */
const measure = ({ TAP_MIN, NEAR_MISS_MAX, GRID, expectSel, expectToken }) => {
  const vw = window.innerWidth;
  const out = { fidelity: {}, findings: [] };

  // ---- Treuepruefung -------------------------------------------------------
  const anchor = expectSel ? document.querySelector(expectSel) : document.body;
  out.fidelity.anchorFound = !!anchor;
  out.fidelity.bodyHeight = document.body.scrollHeight;
  out.fidelity.tokenValue = '';
  if (anchor && expectToken) {
    out.fidelity.tokenValue = getComputedStyle(anchor).getPropertyValue(expectToken).trim();
  }
  // Wie viele Elemente haben ueberhaupt eine gemalte Flaeche? Eine Seite, deren
  // Tokens nicht aufloesen, hat hier fast null — das verraet einen kaputten
  // Harness zuverlaessiger als jede einzelne Zusicherung.
  const painted = [...document.querySelectorAll('*')].filter((el) => {
    const s = getComputedStyle(el);
    return s.backgroundColor !== 'rgba(0, 0, 0, 0)' && s.backgroundColor !== 'transparent';
  });
  out.fidelity.paintedElements = painted.length;
  if (!anchor || (expectToken && !out.fidelity.tokenValue)) return out;

  const scope = anchor;
  const vis = (el) => {
    const s = getComputedStyle(el);
    if (s.display === 'none' || s.visibility === 'hidden' || parseFloat(s.opacity) === 0) return false;
    const r = el.getBoundingClientRect();
    return r.width > 0 && r.height > 0;
  };
  const all = [...scope.querySelectorAll('*')].filter(vis);
  const label = (el) => {
    const id = el.id ? `#${el.id}` : '';
    const cls = typeof el.className === 'string' && el.className
      ? `.${el.className.trim().split(/\s+/).slice(0, 2).join('.')}` : '';
    const txt = (el.textContent || '').replace(/\s+/g, ' ').trim().slice(0, 34);
    return `${el.tagName.toLowerCase()}${id}${cls}${txt ? ` "${txt}"` : ''}`;
  };
  const push = (kind, el, detail) => {
    const r = el.getBoundingClientRect();
    out.findings.push({
      kind, detail, label: label(el),
      box: { x: Math.round(r.x), y: Math.round(r.y + window.scrollY), w: Math.round(r.width), h: Math.round(r.height) },
    });
  };

  // ---- 1. Horizontaler Ueberlauf ------------------------------------------
  // Nicht der Dokument-scrollWidth allein: der sagt nur DASS, nicht WER.
  if (document.documentElement.scrollWidth > vw + 1) {
    for (const el of all) {
      const r = el.getBoundingClientRect();
      if (r.right > vw + 1 && r.width <= vw + 1) push('overflow', el, `ragt ${Math.round(r.right - vw)}px über den Viewport`);
    }
  }

  // ---- 2. Abgeschnittener Inhalt ------------------------------------------
  for (const el of all) {
    const s = getComputedStyle(el);
    if (s.overflowX === 'auto' || s.overflowX === 'scroll') continue; // absichtlich scrollbar
    if (el.scrollWidth > el.clientWidth + 1 && el.clientWidth > 0) {
      push('clipped', el, `Inhalt ${el.scrollWidth - el.clientWidth}px breiter als der Kasten`);
    }
  }

  // ---- 3. Tap-Targets -----------------------------------------------------
  const tappable = [...scope.querySelectorAll('a[href], button, input, select, textarea, [role="button"]')].filter(vis);
  for (const el of tappable) {
    const r = el.getBoundingClientRect();
    // Inline-Links im Fliesstext sind von der Regel ausgenommen (WCAG 2.2).
    const inlineInText = getComputedStyle(el).display.startsWith('inline')
      && el.closest('p, li, figcaption, dd');
    if (inlineInText) continue;
    if (r.width < TAP_MIN || r.height < TAP_MIN) {
      push('tap', el, `${Math.round(r.width)}×${Math.round(r.height)}px, unter ${TAP_MIN}×${TAP_MIN}`);
    }
  }

  // ---- 4. Fast-Ausrichtung ------------------------------------------------
  // Zwei Kanten, die 1..7px auseinanderliegen, sind kein Entwurf, sondern ein
  // Versehen: ab 8px ist es eine Rasterstufe und damit Absicht.
  const edges = new Map();
  for (const el of all) {
    const r = el.getBoundingClientRect();
    if (r.width < 40 || r.height < 12) continue;
    const k = Math.round(r.left);
    if (!edges.has(k)) edges.set(k, []);
    edges.get(k).push(el);
  }
  const keys = [...edges.keys()].sort((a, b) => a - b);
  for (let i = 0; i < keys.length - 1; i++) {
    for (let j = i + 1; j < keys.length; j++) {
      const d = keys[j] - keys[i];
      if (d === 0 || d > NEAR_MISS_MAX) break;
      if (edges.get(keys[i]).length >= 2 && edges.get(keys[j]).length >= 1) {
        push('align', edges.get(keys[j])[0], `linke Kante ${d}px neben einer Flucht mit ${edges.get(keys[i]).length} Elementen (x=${keys[i]})`);
      }
    }
  }

  // ---- 5. Abstands-Rhythmus ----------------------------------------------
  // Vertikale Luecken zwischen Geschwistern, die nicht auf dem Raster liegen.
  for (const parent of all) {
    const kids = [...parent.children].filter(vis);
    if (kids.length < 3) continue;
    const gaps = [];
    for (let i = 0; i < kids.length - 1; i++) {
      const a = kids[i].getBoundingClientRect();
      const b = kids[i + 1].getBoundingClientRect();
      const g = Math.round(b.top - a.bottom);
      if (g > 0 && g < 200) gaps.push(g);
    }
    if (gaps.length < 2) continue;
    const offGrid = [...new Set(gaps.filter((g) => g % GRID !== 0))];
    const distinct = [...new Set(gaps)];
    if (offGrid.length && distinct.length > 1) {
      push('rhythm', parent, `Abstände ${distinct.join('/')}px, davon ${offGrid.join('/')}px neben dem ${GRID}px-Raster`);
    }
  }

  return out;
};

const KINDS = {
  overflow: 'Horizontaler Überlauf',
  clipped: 'Abgeschnittener Inhalt',
  tap: 'Tap-Target zu klein',
  align: 'Fast-Ausrichtung',
  rhythm: 'Abstands-Rhythmus',
};
const COLORS = { overflow: '#e5484d', clipped: '#e5484d', tap: '#f5a623', align: '#3b82f6', rhythm: '#8b5cf6' };

let total = 0;
const report = [];

for (const vp of viewports) {
  const page = await browser.newPage({ viewport: vp, deviceScaleFactor: 2 });
  let res;
  try {
    await page.goto(url, { waitUntil: 'networkidle', timeout: 45000 });
  } catch (e) {
    console.error(`Konnte ${url} bei ${vp.width}px nicht laden: ${e.message.split('\n')[0]}`);
    await browser.close();
    process.exit(2);
  }
  res = await page.evaluate(measure, { TAP_MIN, NEAR_MISS_MAX, GRID, expectSel, expectToken });

  // ---- Treuepruefung auswerten, bevor irgendeine Zahl gilt ----------------
  const f = res.fidelity;
  const problems = [];
  if (expectSel && !f.anchorFound) problems.push(`Selector ${expectSel} existiert auf der Seite nicht`);
  if (expectToken && f.anchorFound && !f.tokenValue) problems.push(`${expectToken} löst am Anker nicht auf`);
  if (f.bodyHeight < 200) problems.push(`Seite ist nur ${f.bodyHeight}px hoch`);
  if (f.paintedElements < 3) problems.push(`nur ${f.paintedElements} Elemente mit Hintergrund — Stylesheet nicht angekommen?`);

  if (problems.length) {
    console.error(`\nAbbruch bei ${vp.width}×${vp.height}: die Seite ist nicht messbar aufgebaut.\n`);
    for (const p of problems) console.error(`  - ${p}`);
    console.error('\nEs wurde NICHTS gemessen. Zahlen aus einer falsch aufgebauten Seite');
    console.error('wären schlechter als kein Audit, weil sie belastbar aussehen.');
    await browser.close();
    process.exit(2);
  }

  if (shotPath && res.findings.length) {
    await page.evaluate(({ findings, COLORS, KINDS }) => {
      const layer = document.createElement('div');
      layer.style.cssText = 'position:absolute;inset:0;z-index:2147483647;pointer-events:none';
      for (const fd of findings) {
        const b = document.createElement('div');
        b.style.cssText = `position:absolute;left:${fd.box.x}px;top:${fd.box.y}px;width:${fd.box.w}px;height:${fd.box.h}px;`
          + `outline:2px solid ${COLORS[fd.kind]};background:${COLORS[fd.kind]}18`;
        const t = document.createElement('span');
        t.textContent = KINDS[fd.kind];
        t.style.cssText = `position:absolute;left:0;top:-16px;font:600 10px/14px system-ui;padding:0 4px;`
          + `color:#fff;background:${COLORS[fd.kind]};white-space:nowrap`;
        b.appendChild(t);
        layer.appendChild(b);
      }
      document.body.appendChild(layer);
    }, { findings: res.findings, COLORS, KINDS });
    const out = shotPath.replace(/\.png$/, `-${vp.width}.png`);
    await page.screenshot({ path: out, fullPage: true });
    report.push({ viewport: vp, shot: out, findings: res.findings });
  } else {
    report.push({ viewport: vp, findings: res.findings });
  }

  total += res.findings.length;
  await page.close();
}

await browser.close();

if (flag('json')) {
  console.log(JSON.stringify({ url, total, report }, null, 2));
  process.exit(total ? 1 : 0);
}

console.log(`\n########## Layout-Audit ##########`);
console.log(`Seite: ${url}`);
for (const r of report) {
  console.log(`\n== ${r.viewport.width}×${r.viewport.height} — ${r.findings.length} Befunde ==`);
  if (!r.findings.length) { console.log('  keine'); continue; }
  const byKind = {};
  for (const f of r.findings) (byKind[f.kind] ||= []).push(f);
  for (const [kind, list] of Object.entries(byKind)) {
    console.log(`\n  ${KINDS[kind]} (${list.length})`);
    for (const f of list.slice(0, 8)) console.log(`    ${f.label}\n      ${f.detail}`);
    if (list.length > 8) console.log(`    … ${list.length - 8} weitere`);
  }
  if (r.shot) console.log(`\n  Screenshot mit Markierungen: ${r.shot}`);
}
console.log(`\nGesamt: ${total} Befunde.`);
console.log('Fast-Ausrichtung und Rhythmus sind Hinweise, keine Fehler — ein bewusst');
console.log('gesetzter Versatz ist erlaubt. Überlauf, abgeschnittener Inhalt und zu');
console.log('kleine Tap-Targets sind dagegen immer Befunde.');
process.exit(total ? 1 : 0);

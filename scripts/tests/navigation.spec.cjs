const { test, expect } = require('@playwright/test');
const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');

// Real header and footer markup (render-navigation.php) with the real
// system.css, fonts and leiste.js. No WordPress, no network: every request to
// the site origin is answered from the working tree.
const theme = path.resolve(__dirname, '../../blocksy-child');
const types = { '.css': 'text/css', '.js': 'text/javascript', '.woff2': 'font/woff2', '.woff': 'font/woff', '.webp': 'image/webp' };
const rendered = {};
const pageTemplates = {
  references: ['page-referenzen.php', 'assets/css/system.css,assets/css/referenzen.css'],
  takeover: ['page-wordpress-website-uebernehmen.php', 'assets/css/system.css,assets/css/wordpress-uebernahme.css'],
  technical_seo: ['page-technisches-seo-wordpress.php', 'assets/css/system.css,assets/css/technisches-seo.css'],
};
const html = context => {
  if (rendered[context]) return rendered[context];
  if (context === 'seo_guide') return (rendered[context] = execFileSync('php', [path.join(__dirname, 'render-article.php'), 'technisches-seo-performance-fundament', 'assets/css/system.css,assets/css/seo-cornerstone.css'], { encoding: 'utf8' }));
  const output = execFileSync('php', pageTemplates[context]
    ? [path.join(__dirname, 'render-page.php'), context, ...pageTemplates[context]]
    : [path.join(__dirname, 'render-navigation.php'), context], { encoding: 'utf8' });
  return (rendered[context] = pageTemplates[context]
    ? output.replace('</body>', '<script src="/wp-content/themes/blocksy-child/assets/js/leiste.js"></script></body>')
    : output);
};
// Einziger Betrag der SEO-Seite: der Befund (hu_seo_finding_price()); die Korrekturen bleiben ohne Zahl.
const seoFindingPrice = execFileSync('php', ['-r', `require '${path.join(__dirname, 'navigation-harness.php')}'; echo hu_seo_finding_price();`], { encoding: 'utf8' });
// Betrag der Marktcheck-Tuer im Kopf (kurze Form, "0 €" solange kostenlos).
const marketcheckAmount = execFileSync('php', ['-r', `require '${path.join(__dirname, 'navigation-harness.php')}'; echo hu_funnel_doors()['marktcheck']['amount'];`], { encoding: 'utf8' });
const trackingAmount = execFileSync('php', ['-r', `require '${path.join(__dirname, 'navigation-harness.php')}'; echo 'ab ' . hu_tracking_price('measurement', 'setup');`], { encoding: 'utf8' });
const takeoverAmount = execFileSync('php', ['-r', `require '${path.join(__dirname, 'navigation-harness.php')}'; echo hu_freelancer_takeover_check_price();`], { encoding: 'utf8' });

async function open(page, context, viewport) {
  await page.setViewportSize(viewport);
  await page.route('https://hasimuener.de/**', route => {
    const url = new URL(route.request().url());
    const prefix = '/wp-content/themes/blocksy-child/';
    if (url.pathname.startsWith(prefix)) {
      const file = path.join(theme, url.pathname.slice(prefix.length));
      return fs.existsSync(file)
        ? route.fulfill({ path: file, contentType: types[path.extname(file)] || 'application/octet-stream' })
        : route.fulfill({ status: 404, body: '' });
    }
    return route.fulfill({ contentType: 'text/html', body: url.pathname === '/__nav' ? html(context) : '<!doctype html><title>Ziel</title>' });
  });
  await page.goto('https://hasimuener.de/__nav');
  await page.evaluate(() => document.fonts.ready);
}

const noHorizontalScroll = page => page.evaluate(() => document.documentElement.scrollWidth <= window.innerWidth);
const rowHeight = page => page.locator('[data-leiste-zeile]').evaluate(el => el.getBoundingClientRect().height);
const rowNav = page => page.getByRole('navigation', { name: 'Hauptnavigation' });
const rowDoor = page => page.locator('.leiste .rechts > .tuer');
const leiste = page => page.locator('.leiste');
const klappe = page => page.locator('[data-leiste-klappe]');
const sheet = page => page.locator('[data-leiste-blatt]');

// The widest door per mode: the row must hold even there.
const wideDoors = ['imprint', 'tracking', 'whitelabel', 'case_study'];

for (const width of [1081, 1280, 1440]) {
  for (const context of wideDoors) {
    test(`desktop ${width} (${context}): one row, four links, door apart from the menu`, async ({ page }) => {
      await open(page, context, { width, height: 800 });
      await expect(page.locator('[data-leiste][data-leiste-bereit]')).toHaveCount(1);
      const links = rowNav(page).getByRole('link');
      await expect(links).toHaveText(['Leistungen', 'Für Agenturen', 'Referenzen', 'Über mich']);
      await expect(links.nth(1)).toHaveAttribute('href', 'https://hasimuener.de/whitelabel-retainer/');
      await expect(rowDoor(page)).toBeVisible();
      await expect(klappe(page)).toBeHidden();
      await expect(sheet(page)).toBeHidden();
      const tops = await links.evaluateAll(els => els.map(el => Math.round(el.getBoundingClientRect().top)));
      expect(new Set(tops).size).toBe(1);
      expect(await rowHeight(page)).toBeLessThanOrEqual(60);
      expect(await noHorizontalScroll(page)).toBe(true);
    });
  }
}

test('desktop: the door is one bordered field per value, the hairline sits before Referenzen', async ({ page }) => {
  await open(page, 'tracking', { width: 1280, height: 800 });
  const door = rowDoor(page);
  await expect(door).toHaveAccessibleName(`Tracking anfragen ${trackingAmount}`);
  await expect(door.locator('.preis')).toHaveText(trackingAmount);
  const style = await door.evaluate(el => {
    const cs = getComputedStyle(el);
    const preis = getComputedStyle(el.querySelector('.preis'));
    return { radius: cs.borderTopLeftRadius, height: Math.round(el.getBoundingClientRect().height), divider: preis.borderLeftWidth, background: cs.backgroundColor };
  });
  expect(style.radius).toBe('0px');
  expect(style.height).toBe(40);
  expect(style.divider).toBe('1px');
  const beleg = await page.locator('.leiste nav .beleg').evaluate(el => ({ line: getComputedStyle(el).borderLeftWidth, text: el.textContent }));
  expect(beleg).toEqual({ line: '1px', text: 'Referenzen' });
});

test('desktop: hover turns the door around and moves the arrow by 3 px', async ({ page }) => {
  await open(page, 'imprint', { width: 1280, height: 800 });
  const door = rowDoor(page);
  const before = await door.evaluate(el => ({ bg: getComputedStyle(el).backgroundColor, arrow: new DOMMatrix(getComputedStyle(el.querySelector('.pf')).transform).m41 }));
  await door.hover();
  await page.waitForTimeout(400);
  const after = await door.evaluate(el => ({ bg: getComputedStyle(el).backgroundColor, arrow: new DOMMatrix(getComputedStyle(el.querySelector('.pf')).transform).m41 }));
  expect(after.bg).not.toBe(before.bg);
  expect(after.bg).toBe('rgba(0, 0, 0, 0)');
  expect(after.arrow - before.arrow).toBeCloseTo(3, 0);
});

for (const [width, door] of [[320, null], [360, 'Projekt'], [390, 'Projekt'], [414, 'Projekt'], [768, 'Projekt anfragen'], [1024, 'Projekt anfragen']]) {
  test(`narrow ${width}: menu button, ${door ? `door "${door}"` : 'door only in the sheet'}, no overflow`, async ({ page }) => {
    await open(page, 'imprint', { width, height: 800 });
    await expect(rowNav(page)).toBeHidden();
    await expect(klappe(page)).toBeVisible();
    await expect(klappe(page)).toHaveAccessibleName('Menü');
    if (door) {
      await expect(rowDoor(page)).toBeVisible();
      await expect(rowDoor(page)).toHaveAccessibleName(door);
    } else {
      await expect(rowDoor(page)).toBeHidden();
    }
    expect(await rowHeight(page)).toBeLessThanOrEqual(60);
    expect(await noHorizontalScroll(page)).toBe(true);
  });
}

// Spec: unter 560 px Kurztext, unter 370 px ohne Betrag; bei 360 px bleiben Wortmarke, Tuer und Menue in einer Zeile.
for (const context of ['tracking', 'whitelabel', 'case_study']) {
  for (const width of [360, 371, 375, 380, 390, 400, 414, 560, 561, 768]) {
    test(`narrow ${width} (${context}): wordmark, door and menu share one row`, async ({ page }) => {
      await open(page, context, { width, height: 800 });
      const boxes = await Promise.all([page.locator('.leiste .sig'), rowDoor(page), klappe(page)].map(l => l.evaluate(el => {
        const r = el.getBoundingClientRect();
        return { top: Math.round(r.top), bottom: Math.round(r.bottom), left: r.left, right: r.right };
      })));
      const [sig, door, menu] = boxes;
      expect(Math.abs(sig.top + (sig.bottom - sig.top) / 2 - (door.top + (door.bottom - door.top) / 2))).toBeLessThanOrEqual(6);
      expect(Math.abs(menu.top + (menu.bottom - menu.top) / 2 - (door.top + (door.bottom - door.top) / 2))).toBeLessThanOrEqual(6);
      expect(sig.right).toBeLessThanOrEqual(door.left);
      expect(door.right).toBeLessThanOrEqual(menu.left);
      expect(await rowHeight(page)).toBeLessThanOrEqual(60);
      expect(await noHorizontalScroll(page)).toBe(true);
    });
  }
}

test('door text steps down: long label and amount, short label, short label without amount', async ({ page }) => {
  await open(page, 'tracking', { width: 700, height: 800 });
  await expect(rowDoor(page).locator('.lang')).toBeVisible();
  await expect(rowDoor(page).locator('.kurz')).toBeHidden();
  await expect(rowDoor(page).locator('.preis')).toBeVisible();
  await page.setViewportSize({ width: 500, height: 800 });
  await expect(rowDoor(page).locator('.lang')).toBeHidden();
  await expect(rowDoor(page).locator('.kurz')).toBeVisible();
  await expect(rowDoor(page).locator('.preis')).toBeVisible();
  await expect(rowDoor(page)).toHaveAccessibleName(/^Tracking ab \d[\d.]* €$/);
  await page.setViewportSize({ width: 360, height: 800 });
  await expect(rowDoor(page).locator('.preis')).toBeHidden();
  await expect(rowDoor(page)).toHaveAccessibleName('Tracking');
});

test('mobile menu: open, keyboard order, Escape returns focus, link closes', async ({ page }) => {
  await open(page, 'imprint', { width: 390, height: 844 });
  await klappe(page).click();
  await expect(klappe(page)).toHaveAttribute('aria-expanded', 'true');
  await expect(klappe(page)).toHaveAccessibleName('Schließen');
  await expect(sheet(page)).toBeVisible();
  await expect(sheet(page).getByRole('link')).toHaveText(['Leistungen', 'Für Agenturen', 'Referenzen', 'Über mich', /Projekt anfragen/]);
  // The sheet carries the full-width door; the row door steps back.
  await expect(rowDoor(page)).toBeHidden();

  await klappe(page).focus();
  await page.keyboard.press('Tab');
  await expect(sheet(page).getByRole('link').first()).toBeFocused();
  const outline = await page.evaluate(() => getComputedStyle(document.activeElement).outlineStyle);
  expect(outline).not.toBe('none');

  await page.keyboard.press('Escape');
  await expect(klappe(page)).toHaveAttribute('aria-expanded', 'false');
  await expect(klappe(page)).toBeFocused();
  await expect(sheet(page)).toBeHidden();

  // Stay on the fixture: the sheet must close on the click itself.
  await page.evaluate(() => document.addEventListener('click', event => event.preventDefault()));
  await klappe(page).click();
  await sheet(page).getByRole('link', { name: 'Leistungen' }).click();
  await expect(klappe(page)).toHaveAttribute('aria-expanded', 'false');
  await expect(sheet(page)).toBeHidden();
});

// "Schliessen" is longer than "Menue": next to the widest door the open row must still fit.
for (const width of [360, 390]) {
  test(`mobile menu ${width} (tracking): open row keeps the close button inside the viewport`, async ({ page }) => {
    await open(page, 'tracking', { width, height: 800 });
    await klappe(page).click();
    await expect(sheet(page).locator('.tuer')).toBeVisible();
    await expect(sheet(page).locator('.tuer .preis')).toBeVisible();
    const box = await klappe(page).boundingBox();
    expect(box.x + box.width).toBeLessThanOrEqual(width);
    expect(await noHorizontalScroll(page)).toBe(true);
  });
}

test('landscape phone: the open sheet scrolls, its door stays reachable', async ({ page }) => {
  await open(page, 'imprint', { width: 740, height: 360 });
  await klappe(page).click();
  const sheetCta = sheet(page).locator('.tuer');
  await sheetCta.scrollIntoViewIfNeeded();
  await expect(sheetCta).toBeInViewport();
  const box = await sheet(page).boundingBox();
  expect(box.y + box.height).toBeLessThanOrEqual(360 + 1);
});

test('without JavaScript the navigation stays open and usable', async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false });
  const page = await context.newPage();
  await open(page, 'imprint', { width: 390, height: 844 });
  await expect(sheet(page).getByRole('link', { name: 'Leistungen' })).toBeVisible();
  await expect(klappe(page)).toBeHidden();
  await expect(rowDoor(page)).toBeHidden();
  await context.close();
});

test('desktop keyboard: wordmark, four links, door, visible focus', async ({ page }) => {
  await open(page, 'imprint', { width: 1280, height: 800 });
  const order = [];
  for (let i = 0; i < 6; i++) {
    await page.keyboard.press('Tab');
    order.push(await page.evaluate(() => {
      const el = document.activeElement;
      // innerText skips the hidden short label; the mono style uppercases it.
      return [el.innerText.trim().replace(/\s+/g, ' ').toLowerCase(), getComputedStyle(el).outlineStyle];
    }));
  }
  expect(order.map(([text]) => text)).toEqual(['haşim üner.', 'leistungen', 'für agenturen', 'referenzen', 'über mich', 'projekt anfragen →']);
  for (const [, outline] of order) expect(outline).not.toBe('none');
});

test('active states: only the linked reference page claims the current page', async ({ page }) => {
  await open(page, 'references', { width: 1280, height: 800 });
  const reference = rowNav(page).getByRole('link', { name: 'Referenzen' });
  await expect(reference).toHaveAttribute('aria-current', 'page');
  const [active, idle] = await Promise.all([
    reference.evaluate(el => getComputedStyle(el).color),
    rowNav(page).getByRole('link', { name: 'Für Agenturen' }).evaluate(el => getComputedStyle(el).color),
  ]);
  expect(active).not.toBe(idle);
  await open(page, 'server_side', { width: 1280, height: 800 });
  await expect(rowNav(page).locator('[aria-current]')).toHaveCount(0);
});

test('home: no nav item claims the page, the wordmark does', async ({ page }) => {
  await open(page, 'home', { width: 1280, height: 800 });
  await expect(rowNav(page).locator('[aria-current]')).toHaveCount(0);
  await expect(page.locator('.leiste .sig')).toHaveAttribute('aria-current', 'page');
});

// Seit 22.10.2026 fuehrt der Kopf der Startseite zur Projektanfrage (ein
// Hauptziel); die Ersteinschaetzung steht als URL-Feld im Inhalt.
test('home, narrow: the project door stays in the row like on every route, and in the sheet', async ({ page }) => {
  await open(page, 'home', { width: 390, height: 844 });
  await expect(rowDoor(page)).toBeVisible();
  await expect(rowDoor(page)).toHaveAttribute('data-door', 'projekt');
  await klappe(page).click();
  await expect(sheet(page).locator('.tuer')).toBeVisible();
  await expect(sheet(page).locator('.tuer')).toHaveAttribute('data-door', 'projekt');
  await expect(sheet(page).locator('.tuer .preis')).toHaveCount(0);
});

for (const width of [1440, 1080, 560, 414, 413, 390, 371, 370]) {
  test(`home project door ${width}: label, no amount and header fit`, async ({ page }) => {
    await open(page, 'home', { width, height: 800 });
    const door = rowDoor(page);
    await expect(door).toBeVisible();
    await expect(door).toHaveAttribute('data-door', 'projekt');
    await expect(door).toHaveAttribute('data-track-action', 'nav_header_project');
    await expect(door).toContainText('Projekt');
    await expect(door.locator('.preis')).toHaveCount(0);
    const sig = await page.locator('.leiste .sig').boundingBox();
    const doorBox = await door.boundingBox();
    expect(sig.x + sig.width).toBeLessThanOrEqual(doorBox.x);
    if (width > 1080) {
      const navBox = await rowNav(page).boundingBox();
      expect(navBox.x + navBox.width).toBeLessThanOrEqual(doorBox.x);
    } else {
      const menuBox = await klappe(page).boundingBox();
      expect(doorBox.x + doorBox.width).toBeLessThanOrEqual(menuBox.x);
      expect(menuBox.x + menuBox.width).toBeLessThanOrEqual(width);
    }
    expect(await rowHeight(page)).toBeLessThanOrEqual(60);
    expect(await noHorizontalScroll(page)).toBe(true);
  });
}

test('wordmark and About keep separate tracking actions', async ({ page }) => {
  await open(page, 'home', { width: 560, height: 800 });
  await expect(page.locator('.leiste .sig')).toHaveAttribute('data-track-action', 'nav_header_home');
  await klappe(page).click();
  await expect(sheet(page).getByRole('link', { name: 'Über mich' })).toHaveAttribute('data-track-action', 'nav_header_about');
});

test('solar cluster: specialised header door and quiet footer access', async ({ page }) => {
  await open(page, 'solar_cluster', { width: 1440, height: 900 });
  await expect(rowDoor(page)).toHaveAttribute('data-door', 'marktcheck');
  await expect(rowDoor(page)).toHaveAccessibleName(`Marktcheck ${marketcheckAmount}`);
  await expect(page.locator('.register [data-door="marktcheck"]')).toHaveCount(0);
  await expect(page.getByRole('navigation', { name: 'Weitere Seiten' }).getByRole('link', { name: 'Solar & Wärmepumpe' })).toHaveAttribute('href', 'https://hasimuener.de/solar-waermepumpen-leadgenerierung/');
});

test('contact: no door, the page is the target', async ({ page }) => {
  await open(page, 'contact', { width: 1280, height: 800 });
  await expect(page.locator('.leiste .tuer')).toHaveCount(0);
  await open(page, 'contact', { width: 390, height: 800 });
  await expect(page.locator('.leiste .tuer')).toHaveCount(0);
  await expect(klappe(page)).toBeVisible();
});

// Reader mode: wordmark, article path, door. No main menu, so no menu button either.
for (const [context, door, name] of [['portal', 'sofort', /^Sofortkontakt \d[\d.]* €$/], ['article_lead', 'marktcheck', `Marktcheck ${marketcheckAmount}`], ['article_track', 'tracking', /^Tracking anfragen ab \d[\d.]* €$/], ['article_cro', 'projekt', 'Projekt anfragen']]) {
  test(`reader ${context}: path and door "${door}", no menu`, async ({ page }) => {
    await open(page, context, { width: 1280, height: 800 });
    await expect(leiste(page)).toHaveAttribute('data-leiste-modus', 'leser');
    await expect(page.getByRole('navigation', { name: 'Artikelpfad' }).getByRole('link').first()).toHaveText('Wissen');
    await expect(rowNav(page)).toHaveCount(0);
    await expect(klappe(page)).toHaveCount(0);
    await expect(rowDoor(page)).toHaveAttribute('data-door', door);
    await expect(rowDoor(page)).toHaveAccessibleName(name);
    expect(await rowHeight(page)).toBeLessThanOrEqual(60);
    expect(await noHorizontalScroll(page)).toBe(true);
  });

  for (const width of [360, 390, 768]) {
    test(`reader ${context} ${width}: wordmark and door stay in one row`, async ({ page }) => {
      await open(page, context, { width, height: 800 });
      const [sig, doorBox] = await Promise.all([page.locator('.leiste .sig'), rowDoor(page)].map(l => l.evaluate(el => {
        const r = el.getBoundingClientRect();
        return { mid: r.top + r.height / 2, left: r.left, right: r.right };
      })));
      expect(Math.abs(sig.mid - doorBox.mid)).toBeLessThanOrEqual(6);
      expect(sig.right).toBeLessThanOrEqual(doorBox.left);
      expect(await rowHeight(page)).toBeLessThanOrEqual(60);
      expect(await noHorizontalScroll(page)).toBe(true);
    });
  }
}

test('reader: the article path hides below 560 px, the door stays', async ({ page }) => {
  await open(page, 'portal', { width: 768, height: 800 });
  await expect(page.getByRole('navigation', { name: 'Artikelpfad' })).toBeVisible();
  await open(page, 'portal', { width: 390, height: 800 });
  await expect(page.getByRole('navigation', { name: 'Artikelpfad' })).toBeHidden();
  await expect(rowDoor(page)).toBeVisible();
});

for (const [width, columns] of [[390, 2], [1280, 4]]) {
  test(`footer ${width}: grouped directory in ${columns} columns`, async ({ page }) => {
    await open(page, 'imprint', { width, height: 900 });
    const directory = page.getByRole('navigation', { name: 'Weitere Seiten' });
    await expect(directory.getByRole('list')).toHaveCount(4);
    await expect(directory.getByRole('list', { name: 'Leistungen' }).getByRole('link')).toHaveText(['Conversion-Tracking', 'Server-Side Tracking', 'Performance Marketing', 'WordPress Agentur Hannover', 'Landingpage erstellen lassen', 'Conversion-Optimierung', 'WordPress-Website erstellen lassen', 'WordPress-Übernahme & Weiterentwicklung', 'Technisches SEO für WordPress']);
    await expect(directory.getByRole('link', { name: 'Impressum' })).toHaveAttribute('aria-current', 'page');
    const tracks = await directory.evaluate(el => getComputedStyle(el).gridTemplateColumns.split(' ').length);
    expect(tracks).toBe(columns);
    expect(await noHorizontalScroll(page)).toBe(true);
  });
}

// Door register in the footer: three active doors on every page except /kontakt/, own way marked, one column on a phone.
const register = page => page.getByRole('navigation', { name: 'Welcher Weg passt?' });

for (const [width, tracks] of [[390, 1], [768, 2], [1280, 2]]) {
  test(`footer register ${width}: ${tracks} column(s) per way, no overflow, every door one click away`, async ({ page }) => {
    await open(page, 'imprint', { width, height: 900 });
    await expect(register(page).locator('.weg')).toHaveCount(3);
    await expect(register(page).getByRole('link')).toHaveCount(3);
    const columns = await register(page).locator('.weg').first().evaluate(el => getComputedStyle(el).gridTemplateColumns.split(' ').length);
    expect(columns).toBe(tracks);
    const rows = await register(page).getByRole('link').evaluateAll(els => els.map(el => Math.round(el.getBoundingClientRect().height)));
    for (const height of rows) expect(height).toBeGreaterThanOrEqual(44);
    expect(await noHorizontalScroll(page)).toBe(true);
    // Each row keeps label, amount and arrow on one line.
    const lines = await register(page).getByRole('link').evaluateAll(els => els.map(el => [...el.children].map(c => Math.round(c.getBoundingClientRect().top)).every((top, i, all) => Math.abs(top - all[0]) <= 8)));
    expect(lines.every(Boolean)).toBe(true);
  });
}

test('footer register: active amounts use mono/tabular figures and paid doors are not --matt', async ({ page }) => {
  await open(page, 'imprint', { width: 1280, height: 900 });
  const info = await register(page).locator('.betrag').evaluateAll(els => els.map(el => {
    const cs = getComputedStyle(el);
    return { text: el.textContent.trim(), family: cs.fontFamily, tabular: cs.fontVariantNumeric, color: cs.color };
  }));
  expect(info.map(i => i.text)).toEqual(['nach Umfang', trackingAmount, expect.stringMatching(/^\d[\d.]* €$/)]);
  for (const entry of info) {
    expect(entry.family).toContain('IBM Plex Mono');
    expect(entry.tabular).toContain('tabular-nums');
  }
  const muted = info[0].color;
  for (const entry of info.slice(1)) expect(entry.color).not.toBe(muted);
});

test('footer register: the own way is marked with a 3 px edge and "Ihr Weg", not hidden', async ({ page }) => {
  await open(page, 'tracking', { width: 1280, height: 900 });
  const marked = register(page).locator('.weg.ist-hier');
  await expect(marked).toHaveCount(1);
  await expect(marked.locator('.hier')).toHaveText('Ihr Weg');
  await expect(marked.getByRole('link')).toHaveAttribute('data-door', 'tracking');
  const edge = await marked.evaluate(el => {
    const edgeStyle = getComputedStyle(el, '::before');
    const stempel = getComputedStyle(el.querySelector('.hier')).color;
    return { width: edgeStyle.width, color: edgeStyle.backgroundColor, stempel, shadow: getComputedStyle(el).boxShadow };
  });
  expect(edge.width).toBe('3px');
  expect(edge.color).toBe(edge.stempel);
  expect(edge.shadow).toBe('none');
  // The door column stays flush with the other ways.
  const lefts = await register(page).locator('.weg .tueren').evaluateAll(els => els.map(el => Math.round(el.getBoundingClientRect().left)));
  expect(new Set(lefts).size).toBe(1);
  await expect(register(page).locator('.weg')).toHaveCount(3);
});

test('footer register: no way is marked on a page of no way', async ({ page }) => {
  await open(page, 'imprint', { width: 1280, height: 900 });
  await expect(register(page).locator('.ist-hier')).toHaveCount(0);
});

test('footer register: the Solar page keeps the global project paths and a quiet specialisation link', async ({ page }) => {
  await open(page, 'solar', { width: 1280, height: 900 });
  expect(await register(page).getByRole('link').evaluateAll(els => els.map(el => el.dataset.door))).toEqual(['projekt', 'tracking', 'aufgabe']);
  await expect(page.getByRole('navigation', { name: 'Weitere Seiten' }).getByRole('link', { name: 'Solar & Wärmepumpe' })).toHaveAttribute('aria-current', 'page');
});

test('footer register: contact has none, the page is the target of every door', async ({ page }) => {
  await open(page, 'contact', { width: 1280, height: 900 });
  await expect(register(page)).toHaveCount(0);
  await expect(page.getByRole('navigation', { name: 'Weitere Seiten' })).toBeVisible();
});

test('footer register: hover turns label and arrow to --stempel and moves the arrow by 3 px', async ({ page }) => {
  await open(page, 'imprint', { width: 1280, height: 900 });
  const row = register(page).getByRole('link').nth(1);
  const probe = () => row.evaluate(el => ({ label: getComputedStyle(el.querySelector('.wie')).color, arrow: new DOMMatrix(getComputedStyle(el.querySelector('.pf')).transform).m41 }));
  const before = await probe();
  await row.hover();
  await page.waitForTimeout(400);
  const after = await probe();
  expect(after.label).not.toBe(before.label);
  expect(after.arrow - before.arrow).toBeCloseTo(3, 0);
});

test('footer register: keyboard reaches the active doors in header order after the page content', async ({ page }) => {
  await open(page, 'imprint', { width: 1280, height: 900 });
  await register(page).getByRole('link').first().focus();
  const doors = [];
  for (let i = 0; i < 3; i++) {
    doors.push(await page.evaluate(() => [document.activeElement.dataset.door, getComputedStyle(document.activeElement).outlineStyle]));
    await page.keyboard.press('Tab');
  }
  expect(doors.map(([door]) => door)).toEqual(['projekt', 'tracking', 'aufgabe']);
  for (const [, outline] of doors) expect(outline).not.toBe('none');
});

// Focus mode (Solar page): wordmark and the ladder of the page, no main menu, not sticky, at most 56 px.
const ladder = page => page.getByRole('navigation', { name: 'Einstiege auf dieser Seite' });

async function expectLadderFields(page) {
  const fields = await ladder(page).getByRole('link').evaluateAll(els => els.filter(a => a.getBoundingClientRect().width).map(a => {
    const label = a.querySelector('span').getBoundingClientRect();
    const amount = a.querySelector('b').getBoundingClientRect();
    return {
      gap: amount.left - label.right,
      expectedGap: parseFloat(getComputedStyle(a).getPropertyValue('--s1')) * parseFloat(getComputedStyle(document.documentElement).fontSize),
      height: a.getBoundingClientRect().height,
      middleDelta: Math.abs(label.top + label.height / 2 - amount.top - amount.height / 2),
    };
  }));
  for (const field of fields) {
    expect(field.gap).toBeCloseTo(field.expectedGap, 1);
    expect(field.middleDelta).toBeLessThanOrEqual(1);
    expect(field.height).toBeGreaterThanOrEqual(44);
  }
}

for (const width of [561, 640, 768, 1024, 1080, 1280, 1440]) {
  test(`solar focus ${width}: Marktcheck only, no menu, one row, not sticky`, async ({ page }) => {
    await open(page, 'solar', { width, height: 800 });
    await expect(leiste(page)).toHaveAttribute('data-leiste-modus', 'fokus');
    await expect(klappe(page)).toHaveCount(0);
    await expect(rowNav(page)).toHaveCount(0);
    await expect(ladder(page).getByRole('link')).toHaveText([`Marktcheck ${marketcheckAmount}`]);
    expect(await ladder(page).getByRole('link').evaluateAll(els => els.map(el => el.getAttribute('href')))).toEqual(['#marktcheck']);
    const tops = await ladder(page).getByRole('link').evaluateAll(els => els.map(el => Math.round(el.getBoundingClientRect().top)));
    expect(new Set(tops).size).toBe(1);
    expect(await rowHeight(page)).toBeLessThanOrEqual(56);
    expect(await leiste(page).evaluate(el => getComputedStyle(el).position)).toBe('static');
    expect(await leiste(page).evaluate(el => getComputedStyle(el).borderBottomWidth)).toBe('1px');
    expect(await noHorizontalScroll(page)).toBe(true);
    await expectLadderFields(page);
    const sig = await page.locator('.leiste .sig').boundingBox();
    const links = await ladder(page).boundingBox();
    expect(sig.x + sig.width).toBeLessThanOrEqual(links.x);
    expect(links.x + links.width).toBeLessThanOrEqual(width);
  });
}

for (const width of [320, 360, 370, 390, 414, 560]) {
  test(`solar focus ${width}: only the filled Marktcheck button stays, 44 px, in one row with the wordmark`, async ({ page }) => {
    await open(page, 'solar', { width, height: 800 });
    const main = ladder(page).getByRole('link', { name: `Marktcheck · ${marketcheckAmount}` });
    await expect(main).toBeVisible();
    await expect(ladder(page).getByRole('link', { name: /^Analyse/ })).toBeHidden();
    await expect(ladder(page).getByRole('link', { name: /^Sofortkontakt/ })).toBeHidden();
    await expect(page.locator('.leiste .leiter .trenn:visible')).toHaveCount(0);
    const style = await main.evaluate(el => {
      const css = getComputedStyle(el);
      return { background: css.backgroundColor, color: css.color, height: el.getBoundingClientRect().height };
    });
    expect(style.background).toBe('rgb(184, 66, 15)');
    expect(style.color).toBe('rgb(255, 255, 255)');
    expect(style.height).toBeGreaterThanOrEqual(44);
    const [sig, link] = await Promise.all([page.locator('.leiste .sig'), main].map(l => l.evaluate(el => {
      const r = el.getBoundingClientRect();
      return { mid: r.top + r.height / 2, left: r.left, right: r.right };
    })));
    expect(Math.abs(sig.mid - link.mid)).toBeLessThanOrEqual(6);
    expect(sig.right).toBeLessThanOrEqual(link.left);
    expect(await rowHeight(page)).toBeLessThanOrEqual(56);
    expect(await noHorizontalScroll(page)).toBe(true);
    await expectLadderFields(page);
  });
}

test('solar focus: Marktcheck stays on the page ladder and outside global footer doors', async ({ page }) => {
  await open(page, 'solar', { width: 1280, height: 900 });
  await expect(ladder(page).getByRole('link')).toHaveAttribute('href', '#marktcheck');
  await expect(register(page).locator('[data-door="marktcheck"]')).toHaveCount(0);
});

// White-Label landing page: its own header (page anchors) with the Test-Sprint door.
const wlDoor = page => page.locator('.wl-site-header .rechts > .tuer');

for (const width of [1081, 1280, 1440]) {
  test(`white-label header ${width}: wordmark, five anchors and the door in one row`, async ({ page }) => {
    await open(page, 'whitelabel:own', { width, height: 800 });
    await expect(page.getByRole('navigation', { name: 'Navigation auf dieser Seite' }).getByRole('link')).toHaveText(['Leistungen', 'Belege', 'Preise', 'Ablauf', 'Fragen']);
    await expect(wlDoor(page)).toBeVisible();
    await expect(wlDoor(page)).toHaveAccessibleName(/^Test-Sprint anfragen \d[\d.]* €$/);
    await expect(wlDoor(page)).toHaveAttribute('data-track-action', 'cta_whitelabel_header_task_brief');
    const tops = await page.locator('.wl-site-header .rechts > nav a, .wl-site-header .rechts > .tuer').evaluateAll(els => els.map(el => Math.round(el.getBoundingClientRect().top + el.getBoundingClientRect().height / 2)));
    expect(Math.max(...tops) - Math.min(...tops)).toBeLessThanOrEqual(8);
    expect(await page.locator('.wl-site-header .in').evaluate(el => el.getBoundingClientRect().height)).toBeLessThanOrEqual(60);
    expect(await noHorizontalScroll(page)).toBe(true);
  });
}

for (const width of [768, 1024, 1080]) {
  test(`white-label header ${width}: anchors fold away, the door stays`, async ({ page }) => {
    await open(page, 'whitelabel:own', { width, height: 800 });
    await expect(page.getByRole('navigation', { name: 'Navigation auf dieser Seite' })).toBeHidden();
    await expect(wlDoor(page)).toBeVisible();
    expect(await noHorizontalScroll(page)).toBe(true);
  });
}

for (const width of [360, 390, 767]) {
  test(`white-label header ${width}: the sticky bar carries the request, the header keeps only the wordmark`, async ({ page }) => {
    await open(page, 'whitelabel:own', { width, height: 800 });
    await expect(wlDoor(page)).toBeHidden();
    await expect(page.locator('.wl-site-header .sig')).toBeVisible();
    expect(await noHorizontalScroll(page)).toBe(true);
  });
}

for (const width of [390, 1280]) {
  test(`references ${width}: project scope, public evidence and enquiry stay readable`, async ({ page }, testInfo) => {
    await open(page, 'references', { width, height: 900 });
    await expect(page.getByRole('heading', { level: 1 })).toHaveCount(1);
    await expect(page.locator('#e3-new-energy, #civaka-azad')).toHaveCount(2);
    await expect(page.locator('#e3-new-energy a[target="_blank"]')).toHaveAttribute('href', 'https://e3-newenergy.de/');
    await expect(page.locator('#civaka-azad a[target="_blank"]')).toHaveAttribute('href', 'https://civaka-azad.org/');
    await expect(page.locator('#e3-new-energy')).not.toContainText(/\d[\d.,]*\s*(?:%|€)|1\.750/);
    await expect(page.locator('#e3-new-energy a[href*="case-study"]')).toHaveCount(0);
    await expect(page.locator('[data-track-action="references_request_project"]')).toHaveAttribute('href', 'https://hasimuener.de/kontakt/?type=project');
    await expect(page.locator('[data-track-action="references_agency_collaboration"]')).toHaveAttribute('href', 'https://hasimuener.de/whitelabel-retainer/');
    const dimensions = await page.locator('.referenzen-bild img').evaluateAll(images => images.map(img => ({ width: img.getAttribute('width'), height: img.getAttribute('height'), alt: img.alt })));
    expect(dimensions.every(img => img.width === '960' && img.height === '600' && img.alt.length > 0)).toBe(true);
    await page.locator('.referenzen-bild img').evaluateAll(async images => {
      images.forEach(img => { img.loading = 'eager'; });
      await Promise.all(images.map(img => img.decode()));
    });
    await page.screenshot({ path: testInfo.outputPath(`references-${width}.png`), fullPage: true });
    if (width === 390) {
      await klappe(page).click();
      await expect(sheet(page)).toBeVisible();
      await page.screenshot({ path: testInfo.outputPath('references-mobile-menu.png') });
      await page.keyboard.press('Escape');
    }
    expect(await noHorizontalScroll(page)).toBe(true);
    if (width > 1080) {
      await expect(rowNav(page).getByRole('link', { name: 'Referenzen' })).toHaveAttribute('aria-current', 'page');
    }
  });
}


for (const width of [320, 390, 1280]) {
  test(`takeover ${width}: price, scope, evidence and request fit the existing-site offer`, async ({ page }, testInfo) => {
    await open(page, 'takeover', { width, height: 900 });
    const content = page.locator('#takeover-content');
    await expect(content.getByRole('heading', { level: 1 })).toHaveText('Ihre WordPress-Website weiterentwickeln.');
    await expect(content.locator('.uebernahme-preis .zahl')).toHaveText(takeoverAmount);
    for (const action of ['hero', 'close']) {
      await expect(content.locator(`[data-track-action="takeover_request_${action}"]`)).toHaveAttribute('href', 'https://hasimuener.de/kontakt/?type=project&focus=takeover');
    }
    await expect(content.locator('#uebernahme-check')).toContainText('Reparaturen, Migrationen und neue Seiten werden separat vereinbart');
    await expect(content.locator('#weiterentwicklung')).toContainText('Ohne Rufbereitschaft und ohne garantierte Reaktionszeiten');
    await expect(content.locator('#beleg a')).toHaveAttribute('href', 'https://hasimuener.de/referenzen/#e3-new-energy');
    await expect(content.locator('#beleg')).not.toContainText(/\d[\d.,]*\s*(?:%|€)|1\.750/);
    await expect(content.locator('a[href*="marktcheck"], a[href*="case-study"]')).toHaveCount(0);
    await expect(page.locator('.fuss .register')).toHaveCount(0);
    const image = content.locator('img');
    await image.evaluate(async img => { img.loading = 'eager'; await img.decode(); });
    await expect(image).toHaveAttribute('width', '960');
    await expect(image).toHaveAttribute('height', '600');
    const question = content.locator('summary').first();
    await question.focus();
    await page.keyboard.press('Enter');
    await expect(content.locator('details').first()).toHaveAttribute('open', '');
    await expect(content.locator('details').first().locator('.antwort')).toBeVisible();
    await page.keyboard.press('Enter');
    expect(await noHorizontalScroll(page)).toBe(true);
    await page.screenshot({ path: testInfo.outputPath(`takeover-${width}.png`), fullPage: true });
  });
}

test('takeover: without JavaScript the offer, native questions and request remain usable', async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false });
  const page = await context.newPage();
  await open(page, 'takeover', { width: 390, height: 900 });
  await expect(page.locator('[data-track-action="takeover_request_hero"]')).toBeVisible();
  await page.locator('#voraussetzungen summary').nth(1).click();
  await expect(page.locator('#voraussetzungen details').nth(1)).toHaveAttribute('open', '');
  await expect(page.locator('#voraussetzungen details').nth(1)).toContainText('Ein Wechsel ist keine Voraussetzung');
  expect(await noHorizontalScroll(page)).toBe(true);
  await context.close();
});


for (const width of [320, 390, 1280]) {
  test(`technical SEO ${width}: priced finding, agreed corrections, own evidence and enquiry fit`, async ({ page }, testInfo) => {
    await open(page, 'technical_seo', { width, height: 900 });
    const content = page.locator('#technical-seo-content');
    await expect(content.getByRole('heading', { level: 1 })).toHaveText('Technisches SEO für Ihre WordPress-Website.');
    for (const position of ['hero', 'close']) {
      await expect(content.locator(`[data-track-action="technical_seo_request_${position}"]`)).toHaveAttribute('href', 'https://hasimuener.de/kontakt/?type=project&focus=seo');
    }
    await expect(content).toContainText(`SEO-Befund für ${seoFindingPrice} netto`);
    // Genau ein Betrag auf der Seite: der Befund. Korrekturen bekommen ihren
    // Festpreis erst nach dem Befund, Prozent- und Ranking-Zusagen gibt es keine.
    const amounts = (await content.textContent()).match(/\d[\d.,]*\s*(?:€|%)/g) || [];
    expect(amounts.map(a => a.replace(/\s+/g, ' '))).toEqual([seoFindingPrice.replace(/\s+/g, ' ')]);
    await expect(content.locator('#abnahme')).toContainText('wiederholbare Prüfung');
    await expect(content.locator('#arbeitsbeispiel')).toContainText('keine Kundenreferenz');
    await expect(content.locator('[data-track-action="technical_seo_proof_page"]')).toHaveAttribute('href', 'https://hasimuener.de/referenzen/');
    await expect(content.locator('[data-track-action="technical_seo_to_takeover"]')).toHaveAttribute('href', 'https://hasimuener.de/wordpress-website-uebernehmen/');
    await expect(content.locator('[data-track-action="technical_seo_read_guide"]')).toHaveAttribute('href', 'https://hasimuener.de/technisches-seo-performance-fundament/');
    await expect(content.locator('a[href*="marktcheck"]')).toHaveCount(0);
    await expect(page.locator('.fuss .register')).toHaveCount(0);
    const cta = content.locator('[data-track-action="technical_seo_request_hero"]');
    await cta.focus();
    expect(await cta.evaluate(el => getComputedStyle(el).outlineStyle)).not.toBe('none');
    await cta.evaluate(el => el.blur());
    await page.evaluate(() => window.scrollTo({ top: 0, behavior: 'instant' }));
    expect(await noHorizontalScroll(page)).toBe(true);
    await page.screenshot({ path: testInfo.outputPath(`technical-seo-${width}.png`), fullPage: true });
  });
}

test('technical SEO: without JavaScript the scope, evidence and typed enquiry remain available', async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false });
  const page = await context.newPage();
  await open(page, 'technical_seo', { width: 390, height: 900 });
  await expect(page.locator('#abnahme')).toBeVisible();
  await expect(page.locator('[data-track-action="technical_seo_request_hero"]')).toHaveAttribute('href', 'https://hasimuener.de/kontakt/?type=project&focus=seo');
  expect(await noHorizontalScroll(page)).toBe(true);
  await context.close();
});


test('technical SEO guide: service link, typed enquiry and cost FAQ remain consistent', async ({ page }) => {
  await open(page, 'seo_guide', { width: 1280, height: 900 });
  await expect(page.locator('[data-track-action="cta_seo_cornerstone_audit"]')).toHaveAttribute('href', 'https://hasimuener.de/technisches-seo-wordpress/');
  await expect(page.locator('[data-track-action="cta_seo_cornerstone_project"]')).toHaveAttribute('href', 'https://hasimuener.de/kontakt/?type=project&focus=seo');
  const visibleAnswer = await page.locator('details').filter({ has: page.locator('summary', { hasText: 'Was kostet technisches SEO' }) }).locator('.nx-faq__content').textContent();
  const schemaAnswer = await page.locator('script[type="application/ld+json"]').evaluateAll(scripts => {
    const faq = scripts.map(s => JSON.parse(s.textContent)).find(s => s['@type'] === 'FAQPage');
    return faq.mainEntity.find(q => q.name === 'Was kostet technisches SEO für den Mittelstand?').acceptedAnswer.text;
  });
  expect(schemaAnswer).toBe(visibleAnswer);
  expect(visibleAnswer).toContain('Abnahmekriterien');
});

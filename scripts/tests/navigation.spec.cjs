const { test, expect } = require('@playwright/test');
const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');

// Real header and footer markup (render-navigation.php) with the real
// system.css, fonts and leiste.js. No WordPress, no network: every request to
// the site origin is answered from the working tree.
const theme = path.resolve(__dirname, '../../blocksy-child');
const types = { '.css': 'text/css', '.js': 'text/javascript', '.woff2': 'font/woff2', '.woff': 'font/woff' };
const rendered = {};
const html = context => (rendered[context] ??= execFileSync('php', [path.join(__dirname, 'render-navigation.php'), context], { encoding: 'utf8' }));

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
const rowCta = page => page.locator('.leiste .rechts > .tun');
const klappe = page => page.locator('[data-leiste-klappe]');
const sheet = page => page.locator('[data-leiste-blatt]');

for (const width of [1081, 1280, 1440]) {
  test(`desktop ${width}: one row, six links, CTA apart from the menu`, async ({ page }) => {
    await open(page, 'imprint', { width, height: 800 });
    await expect(page.locator('[data-leiste][data-leiste-bereit]')).toHaveCount(1);
    const links = rowNav(page).getByRole('link');
    await expect(links).toHaveText(['Leistungen', 'Tracking', 'White-Label', 'Solar & Wärmepumpe', 'Ergebnisse', 'Über Haşim']);
    await expect(links.nth(1)).toHaveAttribute('href', 'https://hasimuener.de/ga4-tracking-setup/');
    await expect(rowCta(page)).toBeVisible();
    await expect(rowCta(page)).toHaveAccessibleName('Projekt anfragen');
    await expect(klappe(page)).toBeHidden();
    await expect(sheet(page)).toBeHidden();
    const tops = await links.evaluateAll(els => els.map(el => Math.round(el.getBoundingClientRect().top)));
    expect(new Set(tops).size).toBe(1);
    expect(await rowHeight(page)).toBeLessThanOrEqual(60);
    expect(await noHorizontalScroll(page)).toBe(true);
  });
}

for (const [width, cta] of [[320, null], [360, 'Anfragen'], [390, 'Anfragen'], [414, 'Anfragen'], [768, 'Projekt anfragen'], [1024, 'Projekt anfragen']]) {
  test(`narrow ${width}: menu button, ${cta ? `CTA "${cta}"` : 'CTA only in the sheet'}, no overflow`, async ({ page }) => {
    await open(page, 'imprint', { width, height: 800 });
    await expect(rowNav(page)).toBeHidden();
    await expect(klappe(page)).toBeVisible();
    await expect(klappe(page)).toHaveAccessibleName('Menü');
    if (cta) {
      await expect(rowCta(page)).toBeVisible();
      await expect(rowCta(page)).toHaveAccessibleName(cta);
    } else {
      await expect(rowCta(page)).toBeHidden();
    }
    expect(await rowHeight(page)).toBeLessThanOrEqual(60);
    expect(await noHorizontalScroll(page)).toBe(true);
  });
}

test('mobile menu: open, keyboard order, Escape returns focus, link closes', async ({ page }) => {
  await open(page, 'imprint', { width: 390, height: 844 });
  await klappe(page).click();
  await expect(klappe(page)).toHaveAttribute('aria-expanded', 'true');
  await expect(klappe(page)).toHaveAccessibleName('Schließen');
  await expect(sheet(page)).toBeVisible();
  await expect(sheet(page).getByRole('link')).toHaveText(['Leistungen', 'Tracking', 'White-Label', 'Solar & Wärmepumpe', 'Ergebnisse', 'Über Haşim', /Projekt anfragen/]);
  // The sheet carries the full-width CTA; the row button steps back.
  await expect(rowCta(page)).toBeHidden();

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

test('landscape phone: the open sheet scrolls, its CTA stays reachable', async ({ page }) => {
  await open(page, 'imprint', { width: 740, height: 360 });
  await klappe(page).click();
  const sheetCta = sheet(page).locator('.tun');
  await sheetCta.scrollIntoViewIfNeeded();
  await expect(sheetCta).toBeInViewport();
  const box = await sheet(page).boundingBox();
  expect(box.y + box.height).toBeLessThanOrEqual(360 + 1);
});

test('without JavaScript the navigation stays open and usable', async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false });
  const page = await context.newPage();
  await open(page, 'imprint', { width: 390, height: 844 });
  await expect(sheet(page).getByRole('link', { name: 'Tracking' })).toBeVisible();
  await expect(klappe(page)).toBeHidden();
  await expect(rowCta(page)).toBeHidden();
  await context.close();
});

test('desktop keyboard: wordmark, six links, CTA, visible focus', async ({ page }) => {
  await open(page, 'imprint', { width: 1280, height: 800 });
  const order = [];
  for (let i = 0; i < 8; i++) {
    await page.keyboard.press('Tab');
    order.push(await page.evaluate(() => {
      const el = document.activeElement;
      // innerText skips the hidden short label; the mono style uppercases it.
      return [el.innerText.trim().replace(/\s+/g, ' ').toLowerCase(), getComputedStyle(el).outlineStyle];
    }));
  }
  expect(order.map(([text]) => text)).toEqual(['haşim üner.', 'leistungen', 'tracking', 'white-label', 'solar & wärmepumpe', 'ergebnisse', 'über haşim', 'projekt anfragen']);
  for (const [, outline] of order) expect(outline).not.toBe('none');
});

test('active states: page vs. area', async ({ page }) => {
  await open(page, 'server_side', { width: 1280, height: 800 });
  const tracking = rowNav(page).getByRole('link', { name: 'Tracking' });
  await expect(tracking).toHaveAttribute('aria-current', 'true');
  const [active, idle] = await Promise.all([
    tracking.evaluate(el => getComputedStyle(el).color),
    rowNav(page).getByRole('link', { name: 'White-Label' }).evaluate(el => getComputedStyle(el).color),
  ]);
  expect(active).not.toBe(idle);
});

test('home: no nav item claims the page, the wordmark does', async ({ page }) => {
  await open(page, 'home', { width: 1280, height: 800 });
  await expect(rowNav(page).locator('[aria-current]')).toHaveCount(0);
  await expect(page.locator('.leiste .sig')).toHaveAttribute('aria-current', 'page');
});

test('home, narrow: the hero carries the request, the row button stays in the sheet', async ({ page }) => {
  await open(page, 'home', { width: 390, height: 844 });
  await expect(rowCta(page)).toBeHidden();
  await klappe(page).click();
  await expect(sheet(page).locator('.tun')).toBeVisible();
});

for (const [width, columns] of [[390, 2], [1280, 4]]) {
  test(`footer ${width}: grouped directory in ${columns} columns`, async ({ page }) => {
    await open(page, 'imprint', { width, height: 900 });
    const directory = page.getByRole('navigation', { name: 'Weitere Seiten' });
    await expect(directory.getByRole('list')).toHaveCount(4);
    await expect(directory.getByRole('list', { name: 'Leistungen' }).getByRole('link')).toHaveText(['Server-Side Tracking', 'Performance Marketing', 'WordPress Agentur Hannover']);
    await expect(directory.getByRole('link', { name: 'Impressum' })).toHaveAttribute('aria-current', 'page');
    const tracks = await directory.evaluate(el => getComputedStyle(el).gridTemplateColumns.split(' ').length);
    expect(tracks).toBe(columns);
    expect(await noHorizontalScroll(page)).toBe(true);
  });
}

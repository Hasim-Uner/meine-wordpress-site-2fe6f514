const { test, expect } = require('@playwright/test');
const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');
const theme = path.resolve(__dirname, '../../blocksy-child');
const renders = {};
async function settleImages(page) {
  for (const image of await page.locator('.blog-card img').all()) {
    await image.scrollIntoViewIfNeeded();
    await expect.poll(() => image.evaluate(el => el.complete && el.naturalWidth > 0)).toBe(true);
  }
  await page.evaluate(() => window.scrollTo({ top: 0, behavior: 'instant' }));
}
async function open(page, mode = 'home', width = 1440, fonts = true) {
  await page.setViewportSize({ width, height: 1000 });
  renders[mode] ??= execFileSync('php', [path.join(__dirname, 'render-blog-entry.php'), mode], { encoding: 'utf8' });
  await page.route('https://hasimuener.de/**', route => {
    const url = new URL(route.request().url());
    const prefix = '/wp-content/themes/blocksy-child/';
    if (url.pathname.startsWith(prefix)) {
      const file = path.join(theme, url.pathname.slice(prefix.length));
      const types = { '.css': 'text/css', '.js': 'text/javascript', '.woff2': 'font/woff2', '.png': 'image/png' };
      return fs.existsSync(file) ? route.fulfill({ path: file, contentType: types[path.extname(file)] || 'application/octet-stream' }) : route.fulfill({ status: 404 });
    }
    return route.fulfill({ body: renders[mode], contentType: 'text/html' });
  });
  await page.goto(`https://hasimuener.de/${mode === 'category' ? 'category/wordpress-performance/' : 'blog/'}`);
  if (fonts) await page.evaluate(() => document.fonts.ready);
}

for (const width of [390, 768, 1440]) {
  test(`blog ${width}: readable entry, keyboard topics and complete card images`, async ({ page }) => {
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    await open(page, 'home', width);
    await expect(page.getByRole('heading', { level: 1 })).toHaveText('WordPress, Tracking und Conversion aus der Praxis.');
    await expect(page.locator('.blog-index__meta')).toHaveCount(0);
    const topics = page.getByRole('navigation', { name: 'Thema wählen' });
    await expect(topics.getByRole('link')).toHaveCount(4);
    await topics.getByRole('link').first().focus();
    expect(await topics.getByRole('link').first().evaluate(el => getComputedStyle(el).outlineStyle)).not.toBe('none');
    expect(await topics.getByRole('link').first().evaluate(el => el.getBoundingClientRect().height)).toBeGreaterThanOrEqual(44);
    await expect(page.locator('.blog-card')).toHaveCount(4);
    await expect(page.locator('.blog-card--featured')).toHaveCount(1);
    await expect(page.locator('.blog-card__media--placeholder')).toHaveCount(1);
    expect(await page.locator('.blog-card__link').evaluateAll(els => new Set(els.map(e => e.href)).size)).toBe(4);
    const image = page.locator('.blog-card--featured img');
    await expect.poll(() => image.evaluate(el => el.naturalWidth)).toBeGreaterThan(0);
    await expect(image).toHaveCSS('object-fit', 'contain');
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    const order = await page.locator('#aktuell, #chronik, #einstieg').evaluateAll(els => els.map(el => el.id));
    expect(order).toEqual(['aktuell', 'chronik', 'einstieg']);
    await expect(page.locator('.page-numbers')).toHaveAttribute('href', '/blog/page/2/');
    await settleImages(page);
    await page.screenshot({ path: test.info().outputPath(`blog-entry-${width}.png`), fullPage: true });
    expect(errors).toEqual([]);
  });
  test(`category ${width}: same cards, complete titles and no horizontal overflow`, async ({ page }) => {
    await open(page, 'category', width);
    await expect(page.getByRole('heading', { level: 1 })).toHaveText('WordPress & technisches SEO');
    await expect(page.locator('.blog-card')).toHaveCount(4);
    await expect(page.locator('.blog-card--featured')).toHaveCount(0);
    await expect(page.locator('.blog-card h3').last()).toContainText('Google Ads wirklich bedeutet');
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    expect(await page.locator('.blog-category__filter-link').evaluateAll(els => els.every(el => { const r = el.getBoundingClientRect(); return r.left >= 0 && r.right <= innerWidth && r.height >= 44; }))).toBe(true);
    await settleImages(page);
    await page.screenshot({ path: test.info().outputPath(`category-entry-${width}.png`), fullPage: true });
  });
}

test('blog: page two retains every loop post; empty archive remains usable', async ({ page }) => {
  await open(page, 'paged', 390);
  await expect(page.locator('.blog-card')).toHaveCount(4);
  await expect(page.locator('.blog-card--featured')).toHaveCount(0);
  await expect(page.locator('#einstieg')).toHaveCount(0);
  await open(page, 'empty', 390);
  await expect(page.locator('.blog-card')).toHaveCount(0);
  await expect(page.getByText('Aktuell sind keine Beiträge veröffentlicht.')).toBeVisible();
});

test('blog: native reading without JavaScript and instant feedback with reduced motion', async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false, reducedMotion: 'reduce' });
  const page = await context.newPage();
  await open(page, 'home', 390, false);
  await expect(page.locator('.blog-card--featured')).toBeVisible();
  await expect(page.locator('.blog-card__link').last()).toBeVisible();
  expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
  await context.close();
});

test('blog: changed motion preference removes card movement; modal focus returns on Escape', async ({ page }) => {
  await open(page);
  const arrow = page.locator('.blog-card--featured .blog-card__read > span');
  await page.emulateMedia({ reducedMotion: 'reduce' });
  expect(await arrow.evaluate(el => getComputedStyle(el).transitionDuration)).toMatch(/^(0s|1e-05s)$/);
  const trigger = page.locator('[data-blog-bell-open]');
  await trigger.click();
  await expect(page.locator('#blog-bell-email')).toBeFocused();
  await page.keyboard.press('Escape');
  await expect(page.locator('#blog-bell-modal')).toBeHidden();
  await expect(trigger).toBeFocused();
});

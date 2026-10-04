const { test, expect } = require('@playwright/test');
const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');

const theme = path.resolve(__dirname, '../../blocksy-child');
const renders = {};
const types = { '.css': 'text/css', '.js': 'text/javascript', '.woff2': 'font/woff2', '.webp': 'image/webp' };
async function open(page, fixture = 'glossary-links') {
  const html = renders[fixture] ??= execFileSync('php', [path.join(__dirname, `render-${fixture}.php`)], { encoding: 'utf8' });
  await page.route('https://hasimuener.de/**', route => {
    const url = new URL(route.request().url());
    const prefix = '/wp-content/themes/blocksy-child/';
    if (url.pathname.startsWith(prefix)) {
      const file = path.join(theme, url.pathname.slice(prefix.length));
      return fs.existsSync(file) ? route.fulfill({ path: file, contentType: types[path.extname(file)] || 'application/octet-stream' }) : route.fulfill({ status: 404 });
    }
    return route.fulfill({ contentType: 'text/html', body: html });
  });
  await page.goto('https://hasimuener.de/');
}
function pair(page, selector) {
  const link = page.locator(`${selector} .glossary-autolink`).first();
  return { link, tip: page.getByRole('tooltip').filter({ visible: true }) };
}
async function insideViewport(page, tip) {
  const box = await tip.boundingBox();
  const viewport = page.viewportSize();
  expect(box.x).toBeGreaterThanOrEqual(0);
  expect(box.y).toBeGreaterThanOrEqual(0);
  expect(box.x + box.width).toBeLessThanOrEqual(viewport.width);
  expect(box.y + box.height).toBeLessThanOrEqual(viewport.height);
}

for (const fallback of [false, true]) {
  test(`glossary ${fallback ? 'fixed fallback' : 'native top layer'}: hover, persistence, Escape and clipping`, async ({ page }) => {
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    await page.setViewportSize({ width: 1440, height: 900 });
    if (fallback) await page.addInitScript(() => { delete HTMLElement.prototype.popover; });
    await open(page);
    const { link, tip } = pair(page, '#clipped');
    await link.hover();
    await expect(tip).toBeVisible();
    await expect(tip).toContainText('Customer Relationship Management');
    expect(await tip.evaluate(el => el.parentElement.tagName)).toBe('BODY');
    expect(await tip.evaluate(el => el.matches(':popover-open'))).toBe(!fallback);
    await insideViewport(page, tip);
    await tip.hover();
    await page.waitForTimeout(220);
    await expect(tip).toBeVisible();
    await page.keyboard.press('Escape');
    await expect(page.getByRole('tooltip')).toBeHidden();
    await page.mouse.move(900, 800);
    await link.hover();
    await expect(tip).toBeVisible();
    await link.click();
    await expect(page).toHaveURL('https://hasimuener.de/glossar/crm/');
    expect(errors).toEqual([]);
  });
}

test('glossary: keyboard description, visible focus and Escape without losing focus', async ({ page }) => {
  await open(page);
  await page.locator('#before').focus();
  await page.keyboard.press('Tab');
  const { link, tip } = pair(page, '#clipped');
  await expect(link).toBeFocused();
  await expect(tip).toBeVisible();
  await expect(link).toHaveAccessibleDescription(await tip.textContent());
  expect(await link.evaluate(el => getComputedStyle(el).outlineStyle)).toBe('solid');
  await page.keyboard.press('Escape');
  await expect(page.getByRole('tooltip')).toBeHidden();
  await expect(link).toBeFocused();
  await page.keyboard.press('Tab');
  await expect(page.locator('#dark .glossary-autolink')).toBeFocused();
  await expect(tip).toContainText('Attribution');
});

test('glossary: Escape also cancels a pending hover preview', async ({ page }) => {
  await open(page);
  await page.locator('#clipped .glossary-autolink').hover();
  await page.keyboard.press('Escape');
  await page.waitForTimeout(230);
  await expect(page.getByRole('tooltip')).toBeHidden();
});

for (const width of [360, 1440]) {
  test(`glossary ${width}: viewport edges, dark surface, wrapped words and scroll`, async ({ page }) => {
    await page.setViewportSize({ width, height: 900 });
    await open(page);
    for (const selector of ['#edge', '#dark', '#long', '#bottom']) {
      const { link, tip } = pair(page, selector);
      await link.hover();
      await expect(tip).toBeVisible();
      await insideViewport(page, tip);
      expect(await tip.evaluate(el => getComputedStyle(el).color === getComputedStyle(el).backgroundColor)).toBe(false);
      await page.keyboard.press('Escape');
      await page.mouse.move(width - 1, 0);
    }
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    await page.locator('#clipped .glossary-autolink').hover();
    await expect(page.getByRole('tooltip').filter({ visible: true })).toBeVisible();
    await page.evaluate(() => window.scrollTo(0, 900));
    await expect(page.getByRole('tooltip')).toBeHidden();
  });
}

test('glossary: touch follows the definition link with the first tap', async ({ browser }) => {
  const context = await browser.newContext({ hasTouch: true, isMobile: true, viewport: { width: 390, height: 844 } });
  const page = await context.newPage();
  await open(page);
  await page.locator('#clipped .glossary-autolink').tap();
  await expect(page).toHaveURL('https://hasimuener.de/glossar/crm/');
  await expect(page.getByRole('tooltip')).toBeHidden();
  await context.close();
});

test('glossary: without JavaScript, descriptions and ordinary navigation remain available', async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false });
  const page = await context.newPage();
  await open(page);
  const link = page.locator('#clipped .glossary-autolink');
  const id = await link.getAttribute('aria-describedby');
  await expect(link).toHaveAccessibleDescription(await page.locator(`#${id}`).textContent());
  await expect(page.getByRole('tooltip', { includeHidden: true }).first()).toBeHidden();
  await link.click();
  await expect(page).toHaveURL('https://hasimuener.de/glossar/crm/');
  await context.close();
});

for (const fixture of ['homepage', 'whitelabel', 'website']) {
  test(`glossary: ${fixture} renders its chosen terms and preserves conversion links`, async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 900 });
    await open(page, fixture);
    const links = page.locator('.glossary-autolink');
    await expect(links).toHaveCount(fixture === 'website' ? 4 : 1);
    if (fixture === 'whitelabel') await page.locator('#lieferfelder details').nth(1).locator('summary').click();
    if (fixture === 'website') await links.first().locator('xpath=ancestor::details').locator('summary').click();
    await links.first().hover();
    await expect(page.getByRole('tooltip').filter({ visible: true })).toBeVisible();
    await insideViewport(page, page.getByRole('tooltip').filter({ visible: true }));
    expect(await page.locator('a[data-track-category="lead_gen"]').count()).toBeGreaterThan(0);
    await expect(page.locator('h1 .glossary-autolink, h2 .glossary-autolink, summary .glossary-autolink, a .glossary-autolink, form .glossary-autolink')).toHaveCount(0);
  });
}

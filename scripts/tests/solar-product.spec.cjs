const { test, expect } = require('@playwright/test');
const { execFileSync } = require('node:child_process');
const path = require('node:path');
const fs = require('node:fs');
const theme = path.resolve(__dirname, '../../blocksy-child');
const prefix = '/wp-content/themes/blocksy-child/';
const products = ['photovoltaik', 'waermepumpe', 'speicher'];
const render = () => execFileSync('php', [path.join(__dirname, 'render-page.php'), 'solar',
  'page-solar-waermepumpen-leadgenerierung.php',
  'style.css,assets/css/design-system.css,assets/css/system.css,assets/css/anfragestrecke.css'], { encoding: 'utf8' })
  .replace('</body>', `<script src="${prefix}assets/js/anfragestrecke.js"></script></body>`);

async function open(page, width) {
  await page.setViewportSize({ width, height: 900 });
  await page.route('**/*', route => {
    const url = new URL(route.request().url());
    if (url.pathname.startsWith(prefix)) {
      const file = path.join(theme, url.pathname.slice(prefix.length));
      const types = { '.css': 'text/css', '.js': 'text/javascript', '.woff2': 'font/woff2', '.webp': 'image/webp' };
      return fs.existsSync(file) ? route.fulfill({ path: file, contentType: types[path.extname(file)] || 'application/octet-stream' }) : route.fulfill({ status: 404 });
    }
    return route.fulfill({ contentType: 'text/html', body: render() });
  });
  await page.goto('https://hasimuener.de/solar-waermepumpen-leadgenerierung/');
  await page.evaluate(() => document.fonts.ready);
}

for (const width of [320, 390, 768, 1440]) {
  test(`solar ${width}: all selections, price and request handoff`, async ({ page }) => {
    const errors = [];
    page.on('pageerror', e => errors.push(e.message));
    await open(page, width);
    const root = page.locator('[data-system-konfigurator]');
    const base = Number(await root.getAttribute('data-base-price'));
    const extra = Number(await root.getAttribute('data-extra-price'));
    await expect(root.locator('fieldset')).toBeVisible();
    for (let mask = 1; mask < 8; mask++) {
      const selected = products.filter((_, i) => mask & (1 << i));
      // Add before removing to retain at least one selection at every step.
      for (const key of selected) await root.locator(`input[value="${key}"]`).check();
      for (const key of products.filter(p => !selected.includes(p))) await root.locator(`input[value="${key}"]`).uncheck();
      await expect(root.locator('[data-config-price]')).toHaveText((base + (selected.length - 1) * extra).toLocaleString('de-DE') + ' €');
      const target = new URL(await root.locator('[data-system-request-link]').getAttribute('href'));
      expect(target.searchParams.get('products')).toBe(selected.join(','));
      expect(target.searchParams.get('focus')).toBe('energy');
      expect(target.searchParams.has('price')).toBe(false);
    }
    // Native keyboard operation and stable focus through an update.
    const pv = root.locator('input[value="photovoltaik"]');
    await pv.focus();
    await page.keyboard.press('Space');
    await expect(pv).toBeFocused();
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    expect(errors).toEqual([]);
  });
}

test('solar no JS keeps the true PV offer and a usable request', async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false });
  const page = await context.newPage();
  await open(page, 320);
  const root = page.locator('[data-system-konfigurator]');
  await expect(root.locator('fieldset')).toBeHidden();
  await expect(root).toContainText('Grundangebot für Photovoltaik');
  const target = new URL(await root.locator('[data-system-request-link]').getAttribute('href'));
  expect(target.searchParams.get('products')).toBe('photovoltaik');
  expect(target.searchParams.has('price')).toBe(false);
  await context.close();
});

test('solar handoff renders canonical scope and registered Sofortkontakt topic', async ({ page }) => {
  await page.setContent(execFileSync('php', [path.join(__dirname, 'render-contact.php'), 'energy',
    'type=project&focus=energy&products=photovoltaik,waermepumpe&price=1'], { encoding: 'utf8' }));
  await expect(page.locator('[name="focus"]')).toHaveValue('energy');
  await expect(page.locator('[name="products"]')).toHaveValue('photovoltaik,waermepumpe');
  await expect(page.locator('[data-energy-scope]')).toContainText('Photovoltaik + Wärmepumpe');
  await expect(page.locator('[data-energy-scope]')).toContainText('keine Beauftragung');
  await page.setContent(execFileSync('php', [path.join(__dirname, 'render-contact.php'), 'sofortkontakt',
    'type=project&focus=sofortkontakt'], { encoding: 'utf8' }));
  await expect(page.locator('[name="focus"]')).toHaveValue('sofortkontakt');
  await expect(page.locator('[data-contact-submit]')).toHaveText('Sofortkontakt anfragen');
});

test('solar reduced motion preserves an immediate price update', async ({ page }) => {
  await page.emulateMedia({ reducedMotion: 'reduce' });
  await open(page, 390);
  const root = page.locator('[data-system-konfigurator]');
  await root.locator('input[value="speicher"]').check();
  await expect(root.locator('[data-config-selection]')).toContainText('Photovoltaik + Speicher');
  expect(await root.evaluate(el => el.getAnimations({ subtree: true }).length)).toBe(0);
});

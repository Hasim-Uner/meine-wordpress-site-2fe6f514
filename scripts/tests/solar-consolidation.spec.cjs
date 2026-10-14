const { test, expect } = require('@playwright/test');
const { execFileSync } = require('node:child_process');
const fs = require('node:fs');
const path = require('node:path');
const theme = path.resolve(__dirname, '../../blocksy-child');
const prefix = '/wp-content/themes/blocksy-child/';
const output = path.resolve(__dirname, '../../.build/form-test-results');
const sheets = 'style.css,assets/css/design-system.css,assets/css/system.css,assets/css/solar-leads-kaufen-alternative.css,assets/css/solar-leads-kaufen-alternative-page.css,assets/css/cpo-calculator.css';
const retired = /\/(?:eigene-leadgenerierung-vs-portale|lead-funnel-solar|kunden-gewinnen-solarteure|cost-per-lead-photovoltaik|qualifizierte-pv-anfragen)\//;

function render(template = 'page-solar-leads-kosten-studie.php', css = sheets, context = 'solar_cluster') {
  return execFileSync('php', [path.join(__dirname, 'render-page.php'), context,
    template, css], { encoding: 'utf8' })
    .replace('</body>', `<script src="${prefix}assets/js/cpo-calculator.js"></script></body>`);
}
async function open(page, width = 1440, route = {}) {
  const html = render(route.template, route.css, route.context);
  await page.setViewportSize({ width, height: 900 });
  await page.route('**/*', route => {
    const url = new URL(route.request().url());
    if (url.pathname.startsWith(prefix)) {
      const file = path.join(theme, url.pathname.slice(prefix.length));
      const types = { '.css': 'text/css', '.js': 'text/javascript', '.woff2': 'font/woff2', '.webp': 'image/webp' };
      return fs.existsSync(file)
        ? route.fulfill({ path: file, contentType: types[path.extname(file)] || 'application/octet-stream' })
        : route.fulfill({ status: 404, body: '' });
    }
    return route.fulfill({ contentType: 'text/html', body: html });
  });
  await page.goto('https://fixture.test/' + (route.slug || 'solar-leads-kosten-studie') + '/');
  await page.evaluate(() => document.fonts.ready);
}

const baseSheets = 'style.css,assets/css/design-system.css,assets/css/system.css,';
const retainedRoutes = [
  { slug: 'solar-waermepumpen-leadgenerierung', context: 'solar',
    template: 'page-solar-waermepumpen-leadgenerierung.php', css: baseSheets + 'assets/css/anfragestrecke.css' },
  { slug: 'solar-leads-kaufen-alternative', template: 'page-solar-leads-kaufen-alternative.php', css: sheets },
  { slug: 'waermepumpen-leads', template: 'page-waermepumpen-leads.php',
    css: baseSheets + 'assets/css/solar-leads-kaufen-alternative.css,assets/css/waermepumpen-leads.css' },
  { slug: 'b2b-solar-leads', template: 'page-b2b-solar-leads.php',
    css: baseSheets + 'assets/css/solar-leads-kaufen-alternative.css,assets/css/b2b-solar-leads-page.css' },
];
for (const route of retainedRoutes) {
  for (const width of [360, 1440]) {
    test(`${route.slug}: retained destination at ${width}px`, async ({ page }) => {
      await open(page, width, route);
      await expect(page.locator('h1')).toHaveCount(1);
      expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth + 1)).toBe(true);
      const links = await page.locator('main a[href]').evaluateAll(nodes => nodes.map(n => n.getAttribute('href')));
      expect(links.filter(href => retired.test(href))).toEqual([]);
      fs.mkdirSync(output, { recursive: true });
      await page.screenshot({ path: path.join(output, `${route.slug}-${width}.png`), fullPage: true });
    });
  }
}

for (const width of [360, 1440]) {
  test(`Consolidated costs: readable layout and retained links at ${width}px`, async ({ page }) => {
    await open(page, width);
    await expect(page.locator('h1')).toHaveCount(1);
    await expect(page.locator('#hu-study-title')).toBeVisible();
    const geometry = await page.evaluate(() => ({
      viewport: document.documentElement.clientWidth,
      scroll: document.documentElement.scrollWidth,
    }));
    expect(geometry.scroll).toBeLessThanOrEqual(geometry.viewport + 1);
    const links = await page.locator('main a[href]').evaluateAll(nodes => nodes.map(n => n.getAttribute('href')));
    expect(links.filter(href => retired.test(href))).toEqual([]);
    const faq = await page.locator('script[type="application/ld+json"]').evaluateAll(nodes =>
      nodes.map(n => JSON.parse(n.textContent)).find(data => data['@type'] === 'FAQPage'));
    expect(faq.mainEntity).toHaveLength(await page.locator('#faq details').count());
    for (const item of faq.mainEntity) {
      const detail = page.locator('#faq details').filter({ has: page.getByText(item.name, { exact: true }) });
      await expect(detail.locator('summary')).toHaveText(item.name);
      await expect(detail.locator('p')).toHaveText(item.acceptedAnswer.text);
    }
    fs.mkdirSync(output, { recursive: true });
    await page.screenshot({ path: path.join(output, `solar-costs-${width}.png`), fullPage: true });
  });
}

test('Cost calculator requires real inputs and includes internal sales costs', async ({ page }) => {
  await open(page, 360);
  const calculator = page.locator('[data-hu-cpo-calculator]');
  await expect(calculator.locator('[data-cpo-output="full_cpo"]')).toHaveText('–');
  const values = { cpl: '80', leads: '30', close_rate: '10', sales_minutes: '30', hourly_rate: '40' };
  for (const [key, value] of Object.entries(values)) {
    await calculator.locator(`[data-cpo-input="${key}"]`).fill(value);
  }
  await expect(calculator.locator('[data-cpo-output="orders"]')).toHaveText('3');
  await expect(calculator.locator('[data-cpo-output="full_cpo"]')).toContainText('1.000');
  await calculator.locator('[data-cpo-input="close_rate"]').fill('0');
  await expect(calculator.locator('[data-cpo-output="full_cpo"]')).toHaveText('–');
  expect(await page.evaluate(() => localStorage.length)).toBe(0);
});

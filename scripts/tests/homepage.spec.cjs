const { test, expect } = require('@playwright/test');
const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');

const theme = path.resolve(__dirname, '../../blocksy-child');
const renders = {};
const types = { '.css': 'text/css', '.js': 'text/javascript', '.woff2': 'font/woff2', '.webp': 'image/webp' };
async function open(page, { width = 1440, off = false, search = '', referer } = {}) {
  await page.setViewportSize({ width, height: 900 });
  const key = off ? 'off' : 'on';
  const html = renders[key] ??= execFileSync('php', [path.join(__dirname, 'render-homepage.php'), key], { encoding: 'utf8' });
  await page.route('https://hasimuener.de/**', route => {
    const url = new URL(route.request().url());
    const prefix = '/wp-content/themes/blocksy-child/';
    if (url.pathname.startsWith(prefix)) {
      const file = path.join(theme, url.pathname.slice(prefix.length));
      return fs.existsSync(file) ? route.fulfill({ path: file, contentType: types[path.extname(file)] || 'application/octet-stream' }) : route.fulfill({ status: 404 });
    }
    return route.fulfill({ contentType: 'text/html', body: html });
  });
  await page.goto('https://hasimuener.de/' + search, referer ? { referer } : {});
  await page.evaluate(() => document.fonts.ready);
}
const sections = ['klick', 'strecke', 'arbeiten', 'pruefstand', 'angebote', 'uebergabe', 'fragen', 'anfrage'];

for (const width of [360, 768, 1024, 1440]) {
  test(`homepage ${width}: complete route, gutter, keyboard and proportional proof`, async ({ page }) => {
    const errors = [];
    page.on('pageerror', e => errors.push(e.message));
    await open(page, { width });
    await expect(page).toHaveTitle('WordPress Freelancer: Website, Tracking, Anfragen · Haşim Üner');
    expect(await page.locator('[data-st-abschnitt]').evaluateAll(els => els.map(el => el.id))).toEqual(sections);
    await expect(page.locator('#klick [data-track-category="lead_gen"]')).toHaveCount(1);
    await expect(page.locator('#klick .tun')).toHaveAttribute('href', 'https://hasimuener.de/kontakt/?focus=ersteinschaetzung');
    await expect(page.locator('.leiste .rechts > .tuer')).toHaveAttribute('data-door', 'ersteinschaetzung');
    await expect(page.locator('.fuss .register')).toHaveCount(0);
    await expect(page.locator('.fuss .verzeichnis')).toHaveCount(1);
    await expect(page.locator('[data-st-wert="herkunft"]')).toHaveText('Direkt aufgerufen');
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    const overflow = await page.locator('[data-st-final] *').evaluateAll(els => els.filter(el => {
      const r = el.getBoundingClientRect();
      return r.width > 0 && r.right > innerWidth + 1;
    }).map(el => el.className));
    expect(overflow).toEqual([]);
    expect(await page.locator('#strecke').evaluate(el => {
      const rail = el.querySelector('.st-rail').getBoundingClientRect();
      return rail.left < el.querySelector('.st-inhalt').getBoundingClientRect().left;
    })).toBe(true);
    const summaries = page.locator('#angebot-funnel summary');
    await summaries.nth(2).focus();
    await page.keyboard.press('Enter');
    await expect(page.locator('#angebot-funnel details[open]')).toHaveCount(1);
    await expect(page.locator('#angebot-funnel details').nth(2)).toHaveAttribute('open', '');
    expect(await summaries.nth(2).evaluate(el => getComputedStyle(el).outlineStyle)).not.toBe('none');
    await page.locator('[data-st-messtafel]').scrollIntoViewIfNeeded();
    await expect(page.locator('[data-st-messtafel]')).toHaveAttribute('data-st-gesehen', '');
    const ratios = await page.locator('.st-balken i').evaluateAll(els => els.map(el => Number(getComputedStyle(el).getPropertyValue('--st-anteil'))));
    const values = await page.locator('.st-vergleich strong').allTextContents();
    const numeric = value => Number(value.replace(/[^\d,]/g, '').replace(',', '.'));
    expect(ratios[1]).toBeCloseTo(numeric(values[1]) / numeric(values[0]));
    await page.evaluate(() => window.scrollTo(0, document.documentElement.scrollHeight));
    await expect(page.locator('.st-rail__punkt.st-erreicht')).toHaveCount(sections.length);
    await expect(page.locator('[data-st-wert="abschnitte"]')).toHaveText(`${sections.length} von ${sections.length}`);
    await expect(page.locator('[data-st-wert="tiefe"]')).toHaveText('100 %');
    await expect(page.locator('[data-st-ende-zeit]')).toBeVisible();
    await expect.poll(() => page.locator('#anfrage img').evaluate(el => el.naturalWidth)).toBeGreaterThan(0);
    expect(errors).toEqual([]);
  });

  test(`homepage ${width}: no JavaScript, native stations and full proof`, async ({ browser }) => {
    const context = await browser.newContext({ javaScriptEnabled: false });
    const page = await context.newPage();
    await open(page, { width });
    await expect(page.locator('.st-protokoll__ohne-js')).toContainText('bleibt leer');
    await expect(page.locator('.st-protokoll__ohne-js')).toBeVisible();
    await page.locator('#angebot-funnel summary').nth(3).click();
    await expect(page.locator('#angebot-funnel details').nth(3)).toHaveAttribute('open', '');
    await expect(page.locator('#angebot-funnel details[open]')).toHaveCount(1);
    expect(await page.locator('.st-balken i').last().evaluate(el => new DOMMatrix(getComputedStyle(el).transform).a)).toBeGreaterThan(0);
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    await context.close();
  });
}

test('homepage: disabled assessment has project routing at every remaining entry', async ({ page }) => {
  await open(page, { off: true });
  await expect(page.locator('a[href*="focus=ersteinschaetzung"]')).toHaveCount(0);
  await expect(page.locator('.leiste .rechts > .tuer')).toHaveAttribute('data-door', 'projekt');
  await expect(page.locator('#klick [data-track-category="lead_gen"]')).toHaveText('Projekt anfragen →');
  await expect(page.locator('#arbeiten [data-track-category="lead_gen"]')).toHaveText('Projekt anfragen →');
  await expect(page.locator('#angebote .st-folge')).toHaveCount(0);
  await expect(page.locator('#anfrage .st-einstieg')).toHaveCount(1);
});

test('homepage: reached line and static bars under reduced motion', async ({ page }) => {
  await page.emulateMedia({ reducedMotion: 'reduce' });
  await open(page, { width: 360 });
  expect(await page.locator('.st-balken i').last().evaluate(el => getComputedStyle(el).transitionDuration)).toBe('0s');
  expect(await page.locator('.st-balken i').last().evaluate(el => new DOMMatrix(getComputedStyle(el).transform).a)).toBeGreaterThan(0);
  await page.evaluate(() => window.scrollTo(0, document.documentElement.scrollHeight));
  await expect(page.locator('.st-rail__punkt.st-erreicht')).toHaveCount(sections.length);
  expect(await page.locator('[data-st-fuellung]').last().evaluate(el => getComputedStyle(el).display)).not.toBe('none');
});

for (const [referer, name] of [
  ['https://www.google.de/search?q=wordpress', 'Google-Suche'],
  ['https://www.bing.com/search?q=wordpress', 'Bing-Suche'],
  ['https://www.linkedin.com/feed/', 'LinkedIn'],
  ['https://chatgpt.com/', 'ChatGPT'],
  ['https://www.perplexity.ai/', 'Perplexity'],
  ['https://example.org/article', 'example.org'],
]) {
  test(`homepage: local referral ${name}`, async ({ page }) => {
    await open(page, { referer });
    await expect(page.locator('[data-st-wert="herkunft"]')).toHaveText(name);
  });
}

test('homepage: campaign text stays text and wins over referral', async ({ page }) => {
  const source = '<img src=x onerror=alert(1)>';
  await open(page, { search: `?utm_source=${encodeURIComponent(source)}&utm_medium=email`, referer: 'https://www.google.de/' });
  await expect(page.locator('[data-st-wert="herkunft"]')).toHaveText(`Kampagne: ${source} / email`);
  await expect(page.locator('[data-st-wert="herkunft"] img')).toHaveCount(0);
});

test('homepage: FAQ schema, retained anchors and enquiry locations', async ({ page }) => {
  await open(page);
  const visible = await page.locator('#fragen details').evaluateAll(els => els.map(el => ({ q: el.querySelector('summary').textContent, a: el.querySelector('.antwort').textContent })));
  const schema = await page.locator('#fragen script[type="application/ld+json"]').textContent();
  expect(JSON.parse(schema).mainEntity.map(entry => ({ q: entry.name, a: entry.acceptedAnswer.text }))).toEqual(visible);
  expect(visible.length).toBe(5);
  for (const id of ['angebot-funnel', 'systemprojekt', 'arbeiten', 'referenzen', 'kontakt', 'angebot-website', 'angebot-landingpage', 'angebot-conversion', 'angebot-tracking', 'angebot-weiterentwicklung']) {
    await expect(page.locator(`[id="${id}"]`)).toHaveCount(1);
  }
  await page.evaluate(() => document.addEventListener('click', e => {
    if (e.target.closest('[data-track-category="lead_gen"]')) e.preventDefault();
  }));
  for (const [selector, name] of [
    ['.leiste .rechts > .tuer', 'Kopf'], ['#klick .tun', 'Einstieg'], ['#arbeiten .tun', 'Fall'], ['#angebote .st-folge .tun', 'Preise'], ['#anfrage .tun', 'Ende'],
  ]) {
    await page.locator(selector).click();
    await expect(page.locator('[data-st-wert="klick"]')).toHaveText('ja · ' + name);
  }
});

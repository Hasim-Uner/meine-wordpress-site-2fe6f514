const { test, expect } = require('@playwright/test');
const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');

const theme = path.resolve(__dirname, '../../blocksy-child');
const renders = {};
const types = { '.css': 'text/css', '.js': 'text/javascript', '.woff2': 'font/woff2', '.webp': 'image/webp' };
async function open(page, { width = 1440, height = 900, off = false, search = '', referer } = {}) {
  await page.setViewportSize({ width, height });
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
    await expect(page.getByRole('heading', { level: 1 })).toHaveAccessibleName('Mehr Anfragen über Ihre Website. Und Sie sehen, woher jede kommt.');
    await expect(page.locator('[data-st-etikett]')).toBeHidden();
    await expect(page.locator('[data-st-signal]')).toBeHidden();
    expect(await page.locator('.st-quelle__kontur').evaluate(el => getComputedStyle(el).color)).not.toBe('rgba(0, 0, 0, 0)');
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
    await expect(page.locator('[data-st-wert="klick"]')).toHaveText('geöffnet · ' + name);
  }
});

// The first screen is the instrument: price, four lines, action and station 03.
for (const [width, height] of [[390, 844], [768, 900], [1100, 900], [1280, 800], [1440, 900], [1920, 1080]]) {
  test(`homepage instrument ${width}x${height}: geometry, source and first screen`, async ({ page }) => {
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    await open(page, { width, height });
    await expect(page.locator('[data-st-signal]')).toHaveClass(/is-here/, { timeout: 6000 });
    await expect(page.locator('[data-st-etikett]')).toHaveClass(/is-hung/);
    await expect(page.locator('[data-st-etikett-wert]')).toHaveText('Direkt aufgerufen');
    await expect(page.locator('[data-st-wert="klick"]')).toHaveText('noch offen');
    await expect(page.locator('.leiste')).toHaveClass(/st-messkopf.*tafel/);
    await expect(page.locator('#klick .st-seitennav')).toHaveCount(0);
    await expect(page.locator('[data-st-spur-station]')).toHaveCount(6);
    const geometry = await page.locator('#klick').evaluate(hero => {
      const rect = selector => hero.querySelector(selector).getBoundingClientRect();
      const signal = rect('[data-st-signal]');
      const point = rect('[data-st-spur-station="2"] .st-spur__punkt');
      const word = rect('[data-st-wort-quelle]');
      const tag = rect('[data-st-etikett]');
      return {
        signalAligned: Math.hypot(signal.left + signal.width / 2 - point.left - point.width / 2, signal.top + signal.height / 2 - point.top - point.height / 2) < 1,
        firstScreen: ['.st-hero__kopfzeile', '.st-hero__h1', '.tun', '[data-st-signal]'].every(selector => rect(selector).top >= 0 && rect(selector).bottom <= innerHeight),
        tagBelow: tag.top > word.bottom,
        tagBeside: tag.left > word.right,
        tagEndAligned: Math.abs(tag.right - word.right) < 4,
        h1Size: parseFloat(getComputedStyle(hero.querySelector('h1')).fontSize),
        overflow: document.documentElement.scrollWidth > innerWidth,
        lines: Array.from(hero.querySelectorAll('h1 .st-messzeile')).map(line => line.offsetHeight),
      };
    });
    expect(geometry.signalAligned).toBe(true);
    expect(geometry.overflow).toBe(false);
    expect(geometry.lines).toHaveLength(4);
    expect(Math.max(...geometry.lines) - Math.min(...geometry.lines)).toBeLessThan(2);
    if (width === 1100) { expect(geometry.tagBelow).toBe(true); expect(geometry.tagEndAligned).toBe(true); }
    if (width >= 1280) expect(geometry.tagBeside).toBe(true);
    if (width === 1280 || width === 1440) expect(geometry.firstScreen).toBe(true);
    if (width >= 1440) expect(geometry.h1Size).toBeGreaterThan(85);
    await page.screenshot({ path: path.resolve(__dirname, `../../.build/home-hero-${width}.png`) });
    expect(errors).toEqual([]);
  });
}

test('homepage instrument: every station opens its matching details with keyboard focus', async ({ page }) => {
  await open(page);
  for (const slug of ['klick', 'seite', 'formular', 'messung', 'crm', 'anfrage']) {
    // Select the tracking label itself: no dependence on translated link copy.
    await page.locator(`[data-st-station-link][data-track-label="${slug}"]`).focus();
    await page.keyboard.press('Enter');
    await expect(page.locator(`#station-${slug}`)).toBeFocused();
    await expect(page.locator(`#station-${slug}`).locator('..')).toHaveAttribute('open', '');
    await expect(page.locator('#angebot-funnel details[open]')).toHaveCount(1);
    expect(await page.locator(`#station-${slug}`).evaluate(el => getComputedStyle(el).outlineStyle)).not.toBe('none');
  }
});

test('homepage instrument: resize during flight and across mobile keeps final positions', async ({ page }) => {
  await open(page, { width: 1440 });
  await expect(page.locator('[data-st-signal]')).toHaveClass(/is-visible/);
  for (const width of [390, 1100, 1280]) {
    await page.setViewportSize({ width, height: 900 });
    await expect(page.locator('[data-st-signal]')).toHaveClass(/is-here/);
    await expect.poll(() => page.locator('[data-st-signal]').evaluate(el => {
      const r = el.getBoundingClientRect();
      const p = document.querySelector('[data-st-spur-station="2"] .st-spur__punkt').getBoundingClientRect();
      return Math.hypot(r.left + r.width / 2 - p.left - p.width / 2, r.top + r.height / 2 - p.top - p.height / 2);
    })).toBeLessThan(1);
  }
});

test('homepage instrument: reduced motion and navigation fallback show immediate measured end state', async ({ page }) => {
  await page.emulateMedia({ reducedMotion: 'reduce' });
  await page.addInitScript(() => { window.PerformanceObserver = undefined; });
  await open(page);
  await expect(page.locator('[data-st-signal]')).toHaveClass(/is-here/);
  await expect(page.locator('[data-st-lcp-label]')).toHaveText('Ladezeit (Seite geladen)');
  await expect(page.locator('[data-st-wert="lcp"]')).not.toHaveText('…');
  expect(await page.locator('[data-st-signal]').evaluate(el => el.getAnimations().length)).toBe(0);
  expect(await page.locator('[data-st-etikett]').evaluate(el => el.getAnimations().length)).toBe(0);
  expect(await page.locator('#st-h1 .st-messwort').first().evaluate(el => getComputedStyle(el).transform)).toBe('none');
});


test('homepage instrument: a direct station fragment opens its native panel', async ({ page }) => {
  const errors = [];
  page.on('pageerror', error => errors.push(error.message));
  await open(page, { search: '#station-crm' });
  await expect(page.locator('#station-crm').locator('..')).toHaveAttribute('open', '');
  await expect(page.locator('#angebot-funnel details[open]')).toHaveCount(1);
  expect(errors).toEqual([]);
});

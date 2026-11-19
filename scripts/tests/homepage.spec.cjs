const { test, expect } = require('@playwright/test');
const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');

const theme = path.resolve(__dirname, '../../blocksy-child');
const renders = {};
const types = { '.css': 'text/css', '.js': 'text/javascript', '.woff2': 'font/woff2', '.webp': 'image/webp' };
async function open(page, { width = 1440, height = 900, off = false, search = '', referer, awaitFonts = true } = {}) {
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
  // Firefox cannot settle the font Promise when JavaScript is disabled.
  // Native assertions wait for their DOM and actual stylesheet instead.
  if (awaitFonts) await page.evaluate(() => document.fonts.ready);
}
const sections = ['klick', 'angebote', 'strecke', 'arbeiten', 'uebergabe', 'pruefstand', 'fragen', 'anfrage'];

for (const width of [360, 768, 1024, 1440]) {
  test(`homepage ${width}: complete route, gutter, keyboard and proportional proof`, async ({ page }) => {
    const errors = [];
    page.on('pageerror', e => errors.push(e.message));
    await open(page, { width });
    await expect(page).toHaveTitle('WordPress Freelancer Hannover: Websites & Tracking | Haşim Üner');
    expect(await page.locator('[data-st-abschnitt]').evaluateAll(els => els.map(el => el.id))).toEqual(sections);
    await expect(page.locator('#klick [data-track-category="lead_gen"]')).toHaveCount(1);
    await expect(page.locator('#klick .tun')).toHaveAttribute('href', 'https://hasimuener.de/kontakt/?type=project');
    await expect(page.locator('.st-hero__ctas .tun')).toHaveCount(1);
    await expect(page.locator('.st-hero__ctas .st-link')).toHaveText('Leistungsumfang & Preise ↓');
    await expect(page.locator('.st-hero__kopfzeile a')).toHaveCount(0);
    await expect(page.locator('.st-hero__beleg')).toBeVisible();
    await expect(page.locator('#angebote .st-erstcheck')).toContainText('Drei Befunde');
    await expect(page.locator('#angebote .st-erstcheck form')).toHaveAttribute('action', 'https://hasimuener.de/kontakt/');
    await expect(page.locator('#angebote .st-erstcheck input[name="focus"]')).toHaveValue('ersteinschaetzung');
    await expect(page.locator('#angebote .st-erstcheck input[name="url"]')).toHaveAttribute('inputmode', 'url');
    await expect(page.locator('.leiste .rechts > .tuer')).toHaveAttribute('data-door', 'projekt');
    await expect(page.locator('.fuss .register')).toHaveCount(0);
    await expect(page.locator('.fuss .verzeichnis')).toHaveCount(1);
    // The visit protocol is gone; the portrait stands in its place without lazy loading.
    await expect(page.locator('[data-st-protokoll], [data-st-etikett], [data-st-signal], #eignung')).toHaveCount(0);
    const portrait = page.locator('#klick .st-hero__meta img.st-hero__portrait');
    await expect(portrait).toHaveAttribute('src', /hasim-freelancer-portrait-480x600\.webp$/);
    await expect(portrait).toHaveAttribute('fetchpriority', 'high');
    await expect(portrait).toHaveAttribute('width', '480');
    await expect(portrait).toHaveAttribute('height', '600');
    await expect(portrait).not.toHaveAttribute('loading', /.*/);
    expect(await portrait.getAttribute('alt')).toContain('Haşim Üner');
    await expect(page.locator('#anfrage img')).toHaveCount(0);
    // Three own ways directly below the hero actions.
    const ways = page.locator('.st-hero__ctas + .st-hero__wege a');
    await expect(ways).toHaveText(['WordPress-Wartung →', 'Für Agenturen →', 'Solar & Wärmepumpe →']);
    expect(await ways.evaluateAll(els => els.map(el => el.dataset.trackAction))).toEqual(['home_door_maintenance', 'home_door_whitelabel', 'home_door_energy']);
    // One filled action: offers and the assessment stay quiet, the project request leads.
    await expect(page.locator('#angebote .tun:not(.still)')).toHaveCount(0);
    await expect(page.locator('#angebote .st-angebot .tun.still')).toHaveCount(3);
    // Existing website: one line per service, name, canon price and sub page.
    await expect(page.locator('#angebote .st-angebotsliste li')).toHaveCount(5);
    await expect(page.locator('#angebote .st-angebotsliste li a[href^="https://hasimuener.de/"]')).toHaveCount(5);
    await expect(page.locator('#angebote .st-weiter')).toHaveCount(0);
    // Case: Solar businesses first into the Marktcheck, other trades to the request route.
    await expect(page.locator('#arbeiten [data-track-action="home_work_marktcheck"]')).toHaveAttribute('href', 'https://hasimuener.de/solar-waermepumpen-leadgenerierung/#marktcheck');
    await expect(page.locator('#arbeiten [data-track-action="home_work_anfragestrecke"]')).toHaveAttribute('href', '#angebot-anfragestrecke');
    await expect(page.locator('#arbeiten [data-track-action="home_work_anfragestrecke"]')).toContainText('Anfragestrecke für andere Branchen ab');
    await expect(page.locator('#anfrage .st-einstieg').first()).toHaveClass(/st-einstieg--haupt/);
    await expect(page.locator('#anfrage .st-einstieg--haupt .tun:not(.still)')).toHaveText('Projekt anfragen →');
    // The H1 carries its last line once; the filled copy is generated content.
    expect((await page.locator('#st-h1').textContent()).split('was funktioniert').length - 1).toBe(1);
    // Pruefstand: second instrument, dark, five stations, the last one is the visitor.
    await expect(page.locator('#pruefstand.tafel .st-livegang__station')).toHaveCount(5);
    await expect(page.locator('#pruefstand .st-livegang__station--sie')).toHaveCount(1);
    await expect(page.locator('#klick .st-belege li')).toHaveCount(2);
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
    await expect(page.locator('[data-st-ende-zeit]')).toBeVisible();
    await expect(page.locator('#pruefstand [data-st-lcp-grenze]')).toBeVisible();
    await expect(page.locator('#pruefstand [data-st-lcp-kopie]')).not.toHaveText('…');
    await expect.poll(() => portrait.evaluate(el => el.naturalWidth)).toBeGreaterThan(0);
    expect(errors).toEqual([]);
    if (width === 360 || width === 1440) {
      await page.evaluate(() => window.scrollTo({ top: 0, behavior: 'instant' }));
      await page.screenshot({ path: test.info().outputPath(`homepage-entry-${width}.png`) });
      await page.locator('.st-erstcheck').scrollIntoViewIfNeeded();
      await page.screenshot({ path: test.info().outputPath(`homepage-assessment-${width}.png`) });
    }
  });

  test(`homepage ${width}: no JavaScript, native stations and full proof`, async ({ browser }) => {
    const context = await browser.newContext({ javaScriptEnabled: false });
    const page = await context.newPage();
    await open(page, { width, awaitFonts: false });
    await expect(page.locator('[data-st-titel]')).toHaveCSS('container-type', 'inline-size');
    await expect(page.getByRole('heading', { level: 1 })).toHaveAccessibleName('Mehr Anfragen über Ihre Website. Und Sie sehen, was funktioniert.');
    await expect(page.locator('#klick .st-hero__portrait')).toBeVisible();
    await expect(page.locator('.st-hero__wege a')).toHaveCount(3);
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
  await expect(page.locator('#angebote .st-erstcheck')).toHaveCount(0);
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

test('homepage: FAQ schema and retained anchors', async ({ page }) => {
  await open(page);
  const visible = await page.locator('#fragen details').evaluateAll(els => els.map(el => ({ q: el.querySelector('summary').textContent, a: el.querySelector('.antwort').textContent })));
  const schema = await page.locator('#fragen script[type="application/ld+json"]').textContent();
  expect(JSON.parse(schema).mainEntity.map(entry => ({ q: entry.name, a: entry.acceptedAnswer.text }))).toEqual(visible);
  expect(visible.length).toBe(4);
  expect(visible.map(entry => entry.q).join(' ')).not.toContain('Protokoll');
  for (const id of ['angebot-funnel', 'systemprojekt', 'arbeiten', 'referenzen', 'kontakt', 'angebot-website', 'angebot-landingpage', 'angebot-anfragestrecke', 'angebot-conversion', 'angebot-tracking', 'angebot-weiterentwicklung']) {
    await expect(page.locator(`[id="${id}"]`)).toHaveCount(1);
  }
});

// First screen: price, four lines and the main action; the portrait follows below.
for (const [width, height] of [[390, 844], [768, 900], [1100, 900], [1280, 800], [1440, 900], [1920, 1080]]) {
  test(`homepage hero ${width}x${height}: geometry and first screen`, async ({ page }) => {
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    await open(page, { width, height });
    await expect(page.locator('.leiste')).toHaveClass(/st-messkopf.*tafel/);
    await expect(page.locator('#klick .st-seitennav')).toHaveCount(0);
    const geometry = await page.locator('#klick').evaluate(hero => {
      const rect = selector => hero.querySelector(selector).getBoundingClientRect();
      return {
        firstScreen: ['.st-hero__kopfzeile', '.st-hero__h1', '.tun'].every(selector => rect(selector).top >= 0 && rect(selector).bottom <= innerHeight),
        waysBelowActions: rect('.st-hero__wege').top >= rect('.st-hero__ctas').bottom,
        portraitBelowWays: rect('.st-hero__portrait').top >= rect('.st-hero__wege').bottom,
        h1Size: parseFloat(getComputedStyle(hero.querySelector('h1')).fontSize),
        overflow: document.documentElement.scrollWidth > innerWidth,
        lines: Array.from(hero.querySelectorAll('h1 .st-messzeile')).map(line => line.offsetHeight),
      };
    });
    expect(geometry.overflow).toBe(false);
    expect(geometry.waysBelowActions).toBe(true);
    expect(geometry.portraitBelowWays).toBe(true);
    expect(geometry.lines).toHaveLength(4);
    expect(Math.max(...geometry.lines) - Math.min(...geometry.lines)).toBeLessThan(2);
    if (width === 1280 || width === 1440) expect(geometry.firstScreen).toBe(true);
    if (width >= 1440) expect(geometry.h1Size).toBeGreaterThan(85);
    await page.screenshot({ path: path.resolve(__dirname, `../../.build/home-hero-${width}.png`) });
    expect(errors).toEqual([]);
  });
}

test('homepage: reduced motion and navigation fallback show the measured load time', async ({ page }) => {
  await page.emulateMedia({ reducedMotion: 'reduce' });
  await page.addInitScript(() => { window.PerformanceObserver = undefined; });
  await open(page);
  await expect(page.locator('[data-st-lcp-label]')).toHaveText(['Ladezeit (Seite geladen)']);
  // Google's LCP threshold belongs to an LCP only, not to a load-event time.
  await expect(page.locator('[data-st-lcp-grenze]')).toHaveCount(1);
  await expect(page.locator('[data-st-lcp-grenze]')).toBeHidden();
  await expect(page.locator('#pruefstand [data-st-lcp-kopie]')).not.toHaveText('…');
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

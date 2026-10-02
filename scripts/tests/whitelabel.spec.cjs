const { test, expect } = require('@playwright/test');
const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');

const theme = path.resolve(__dirname, '../../blocksy-child');
const types = { '.css': 'text/css', '.js': 'text/javascript', '.woff2': 'font/woff2', '.webp': 'image/webp' };
let rendered;
async function open(page, width = 1440, search = '') {
  await page.setViewportSize({ width, height: 900 });
  rendered ??= execFileSync('php', [path.join(__dirname, 'render-whitelabel.php')], { encoding: 'utf8' });
  await page.route('https://hasimuener.de/**', route => {
    const url = new URL(route.request().url());
    const prefix = '/wp-content/themes/blocksy-child/';
    if (url.pathname.startsWith(prefix)) {
      const file = path.join(theme, url.pathname.slice(prefix.length));
      return fs.existsSync(file) ? route.fulfill({ path: file, contentType: types[path.extname(file)] || 'application/octet-stream' }) : route.fulfill({ status: 404 });
    }
    return route.fulfill({ contentType: 'text/html', body: rendered });
  });
  await page.goto('https://hasimuener.de/whitelabel-retainer/' + search);
  if (await page.evaluate(() => !!document.fonts)) await page.evaluate(() => document.fonts.ready);
}
const finished = page => expect(page.locator('[data-wl-protokoll]')).toHaveClass(/is-complete/, { timeout: 6000 });
const sections = ['hero', 'lieferfelder', 'proof', 'zusammenarbeit', 'einstieg', 'absicherung', 'faq', 'naechster-schritt'];

for (const width of [768, 1440]) {
  test(`whitelabel ${width}: the measurement layers do not move hero content`, async ({ page }) => {
    await open(page, width);
    const boxes = () => page.locator('#hero').evaluate(hero => {
      const strip = hero.querySelector('.st-belege').getBoundingClientRect();
      const button = hero.querySelector('.tun').getBoundingClientRect();
      return [hero.getBoundingClientRect().height, strip.top, button.top, button.height];
    });
    const before = await boxes();
    await finished(page);
    const after = await boxes();
    after.forEach((value, i) => expect(Math.abs(value - before[i])).toBeLessThan(1));
    expect(await page.locator('.wl-site-header').evaluate(el => el.getBoundingClientRect().height)).toBeLessThan(61);
  });
}

for (const width of [360, 768, 1024, 1100, 1440]) {
  test(`whitelabel ${width}: real protocol, geometry and native keyboard fields`, async ({ page }) => {
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    await open(page, width);
    await expect(page.locator('[data-track-action="cta_whitelabel_hero_task_brief"]')).toBeEnabled();
    await finished(page);
    await expect(page.locator('[data-wl-status]')).toHaveText(/^7 von 7 bestanden ·/);
    await expect(page.locator('[data-wl-stempel-wert]')).toHaveText('7/7');
    await expect(page.locator('#hero')).toHaveClass(/is-approved/);
    expect(await page.locator('[data-st-abschnitt]').evaluateAll(els => els.map(el => el.id))).toEqual(sections);
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    expect(await page.locator('.wl-page *').evaluateAll(els => els.filter(el => {
      const r = el.getBoundingClientRect();
      return r.width > 0 && r.right > innerWidth + 1 && getComputedStyle(el).position !== 'absolute';
    }).map(el => el.className))).toEqual([]);
    const geometry = await page.locator('#hero').evaluate(hero => {
      const word = hero.querySelector('[data-wl-wort-abnahme]').getBoundingClientRect();
      const stamp = hero.querySelector('[data-wl-stempel]').getBoundingClientRect();
      const content = hero.querySelector('[data-wl-messfeld]').getBoundingClientRect();
      const rail = hero.querySelector('.st-rail__punkt').getBoundingClientRect();
      const elements = Array.from(hero.querySelectorAll('[data-wl-mess]'));
      const marks = Array.from(hero.querySelectorAll('.wl-messmarke')).map((el, i) => {
        const r = el.getBoundingClientRect(), target = elements[i].getBoundingClientRect();
        return r.left > rail.right && r.right <= content.left && Math.abs(r.top - target.top) < 3;
      });
      return { stampInsideWord: stamp.top >= word.top - 8 && stamp.bottom <= word.bottom + 8, marks, wordFits: word.right <= hero.querySelector('.wl-hero__links').getBoundingClientRect().right, railBeforeContent: rail.right < content.left };
    });
    expect(geometry.wordFits).toBe(true);
    expect(geometry.stampInsideWord).toBe(true);
    expect(geometry.railBeforeContent).toBe(true);
    expect(geometry.marks).toEqual(width < 768 ? [] : [true, true, true, true]);
    await page.locator('#lieferfelder summary').nth(1).focus();
    await page.keyboard.press('Enter');
    await expect(page.locator('#lieferfelder details[open]')).toHaveCount(1);
    expect(await page.locator('#lieferfelder summary').nth(1).evaluate(el => getComputedStyle(el).outlineStyle)).not.toBe('none');
    await page.locator('#marge').scrollIntoViewIfNeeded();
    await expect(page.locator('#marge')).toHaveAttribute('data-st-gesehen', '');
    const ratios = await page.locator('#marge .st-balken i').evaluateAll(els => els.map(el => Number(getComputedStyle(el).getPropertyValue('--st-anteil'))));
    const amounts = (await page.locator('#marge .st-vergleich strong').allTextContents()).map(s => Number(s.replace(/\D/g, '')));
    expect(ratios[1] / ratios[0]).toBeCloseTo(amounts[1] / amounts[0], 5);
    expect(errors).toEqual([]);
    await page.evaluate(() => window.scrollTo(0, 0));
    await page.screenshot({ path: path.resolve(__dirname, `../../.build/whitelabel-${width}.png`), fullPage: true });
  });
}

test('whitelabel: recheck reports a real cookie finding and malformed schema', async ({ page, context }) => {
  await open(page);
  await finished(page);
  await context.addCookies([{ name: 'wp-settings-1', value: 'fixture', domain: 'hasimuener.de', path: '/' }]);
  await page.locator('[data-wl-nochmal]').click();
  await finished(page);
  await expect(page.locator('[data-wl-status]')).toHaveText(/^6 von 7 bestanden ·/);
  await expect(page.locator('[data-wl-stempel]')).toHaveClass(/is-finding/);
  await expect(page.locator('#hero')).not.toHaveClass(/is-approved/);
  await page.evaluate(() => { document.querySelector('script[type="application/ld+json"]').textContent = '{'; });
  await page.locator('[data-wl-nochmal]').click();
  await finished(page);
  await expect(page.locator('[data-wl-status]')).toHaveText(/^5 von 7 bestanden ·/);
});

test('whitelabel: reduced motion, document fallback and local privacy', async ({ page }) => {
  await page.emulateMedia({ reducedMotion: 'reduce' });
  await page.addInitScript(() => {
    window.PerformanceObserver = undefined;
    window.protocolTransport = [];
    window.fetch = (...args) => { window.protocolTransport.push(args); throw new Error('Protocol must not send'); };
    navigator.sendBeacon = (...args) => { window.protocolTransport.push(args); return false; };
  });
  await open(page, 768);
  await finished(page);
  await expect(page.locator('[data-wl-lcp-label]')).toHaveText('Ladezeit (Dokument)');
  await expect(page.locator('[data-wl-status]')).toHaveText(/^7 von 7 bestanden ·/);
  expect(await page.locator('.wl-wort').first().evaluate(el => getComputedStyle(el).transitionDuration)).toBe('0s');
  expect(await page.locator('#marge .st-balken i').first().evaluate(el => getComputedStyle(el).transitionDuration)).toBe('0s');
  expect(await page.evaluate(() => ({ sent: window.protocolTransport.length, cookies: document.cookie, local: localStorage.length, session: sessionStorage.length }))).toEqual({ sent: 0, cookies: '', local: 0, session: 0 });
  await page.locator('[data-wl-nochmal]').click();
  await finished(page);
  expect(await page.evaluate(() => window.protocolTransport.length)).toBe(0);
  await page.locator('#faq summary').first().focus();
  await page.keyboard.press('Enter');
  await expect(page.locator('#faq details').first()).toHaveAttribute('open', '');
});

test('whitelabel: mobile sticky follows hero and actual form visibility', async ({ page }) => {
  await open(page, 360);
  await finished(page);
  await expect(page.locator('#wl-sticky-cta')).toHaveAttribute('aria-hidden', 'true');
  await page.locator('#lieferfelder').scrollIntoViewIfNeeded();
  await expect(page.locator('#wl-sticky-cta')).toHaveAttribute('aria-hidden', 'false');
  await expect(page.locator('#wl-sticky-cta a')).toHaveAttribute('tabindex', '0');
  await page.locator('#aufgabe').scrollIntoViewIfNeeded();
  await expect(page.locator('#wl-sticky-cta')).toHaveAttribute('aria-hidden', 'true');
  await expect(page.locator('#wl-sticky-cta a')).toHaveAttribute('tabindex', '-1');
});

for (const width of [360, 1440]) {
  test(`whitelabel ${width}: complete contents and operable disclosures without JavaScript`, async ({ browser }) => {
    const context = await browser.newContext({ javaScriptEnabled: false });
    const page = await context.newPage();
    await open(page, width);
    await expect(page.locator('[data-wl-status]')).toHaveText('Prüflauf braucht JavaScript. Es läuft keine Prüfung.');
    await expect(page.locator('.wl-stempel')).toBeHidden();
    await expect(page.locator('.wl-request__ohne-js')).toBeVisible();
    await expect(page.locator('[data-wl-submit]')).toBeDisabled();
    await page.locator('#lieferfelder summary').nth(2).click();
    await expect(page.locator('#lieferfelder details').nth(2)).toHaveAttribute('open', '');
    await page.locator('#faq summary').first().click();
    await expect(page.locator('#faq details').first()).toHaveAttribute('open', '');
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    await context.close();
  });
}

for (const [way, hook] of [['aufgabe', 'cta_whitelabel_hero_task_brief'], ['angebotsphase', 'cta_whitelabel_offers_assessment'], ['vormerken', 'cta_whitelabel_way_later']]) {
  test(`whitelabel actual page submits ${way} to the unchanged REST contract`, async ({ page }) => {
    await open(page, 1440, `?case=${way}`);
    await page.locator(`[data-track-action="${hook}"]`).click();
    await expect(page.locator('[data-wl-case]')).toHaveValue(way);
    let received;
    await page.route('**/wp-json/nexus/v1/whitelabel-request', route => {
      received = route.request().postDataJSON();
      return route.fulfill({ status: 201, contentType: 'application/json', body: JSON.stringify({ ok: true, case: way, message: 'Testanfrage angekommen.' }) });
    });
    await page.locator('[name="task"]').fill('WordPress-Formular an das vorhandene CRM anbinden.');
    await page.locator('[name="email"]').fill('fixture@example.test');
    await page.locator('[data-wl-submit]').click();
    await expect(page.locator('[data-wl-feedback]')).toHaveClass(/is-success/);
    expect(received.case).toBe(way);
    expect(received.company_website).toBe('');
  });
}

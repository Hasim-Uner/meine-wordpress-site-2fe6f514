const { test, expect } = require('@playwright/test');
const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');

const theme = path.resolve(__dirname, '../../blocksy-child');
const prefix = '/wp-content/themes/blocksy-child/';
const types = { '.css': 'text/css', '.js': 'text/javascript', '.woff2': 'font/woff2' };
const documents = Object.fromEntries(['impressum', 'datenschutz'].map(slug => [slug,
  execFileSync('php', [path.join(__dirname, 'render-legal.php'), slug], { encoding: 'utf8' })]));
const navigation = execFileSync('php', [path.join(__dirname, 'render-navigation.php'), 'imprint'], { encoding: 'utf8' });
const assets = `<link rel="stylesheet" href="${prefix}style.css"><script src="${prefix}assets/js/nexus-core.js" defer></script>`;
// The parent theme owns link colors. The legal buttons must explicitly own
// their foreground in every state, even under a white global hover color.
const parent = '<style>.site-main a{color:#171512}.site-main a:hover{color:#fff}</style>';

async function setup(page, { standalone = '', delay = null } = {}) {
  const requests = [];
  await page.context().route('https://hasimuener.de/**', async route => {
    const url = new URL(route.request().url());
    if (url.pathname.startsWith(prefix)) {
      const file = path.join(theme, url.pathname.slice(prefix.length));
      return fs.existsSync(file) ? route.fulfill({ path: file, contentType: types[path.extname(file)] || 'application/octet-stream' }) : route.fulfill({ status: 404 });
    }
    const slug = url.pathname.split('/')[1];
    if (documents[slug]) {
      requests.push(slug);
      if (delay) await delay(slug);
      return route.fulfill({ contentType: 'text/html', body: `<!doctype html><html lang="de"><head><meta charset="utf-8">${assets}<link rel="stylesheet" href="${prefix}assets/css/system.css"><link rel="stylesheet" href="${prefix}assets/css/legal-pages.css">${parent}<script src="${prefix}assets/js/legal-pages.js" defer></script></head><body>${documents[slug]}</body></html>` });
    }
    const html = navigation.replace('</head>', `${assets}${parent}<script src="${prefix}assets/js/legal-modal.js" defer></script></head>`);
    return route.fulfill({ contentType: 'text/html', body: html });
  });
  await page.goto(`https://hasimuener.de/${standalone || '__legal'}`);
  return requests;
}

const panel = page => page.locator('.legal-modal__panel');
const footerLink = (page, slug) => page.locator(`.fuss a[href="https://hasimuener.de/${slug}/"]`);
const dialog = page => page.getByRole('dialog');
const switchLink = page => panel(page).locator('.imprint-actions a').filter({ hasText: 'Datenschutz' });

async function openImprint(page) {
  await footerLink(page, 'impressum').click();
  await expect(panel(page).locator('.imprint-page[data-legal-ready]')).toBeVisible();
}

async function nearPanelTop(page, selector) {
  await expect.poll(() => page.locator(selector).evaluate(el => {
    const root = el.closest('.legal-modal__panel');
    return Math.round(el.getBoundingClientRect().top - root.getBoundingClientRect().top);
  })).toBeGreaterThan(0);
  await expect.poll(() => page.locator(selector).evaluate(el => {
    const root = el.closest('.legal-modal__panel');
    return Math.round(el.getBoundingClientRect().top - root.getBoundingClientRect().top);
  })).toBeLessThan(160);
}

for (const width of [390, 1280]) {
  for (const reducedMotion of ['no-preference', 'reduce']) {
    test(`legal modal ${width}, ${reducedMotion}: switch to the document, anchors scroll only the panel`, async ({ page }) => {
      await page.setViewportSize({ width, height: 850 });
      await page.emulateMedia({ reducedMotion });
      const requests = await setup(page);
      await openImprint(page);
      const backgroundY = await page.evaluate(() => window.scrollY);
      await expect(panel(page)).toHaveJSProperty('scrollTop', 0);
      await switchLink(page).click();
      await expect(panel(page).locator('.privacy-page[data-legal-ready]')).toBeVisible();
      await expect(panel(page).locator('#privacy-controller')).toBeFocused();
      await nearPanelTop(page, '.legal-modal #datenschutz-inhalt');
      expect(requests).toEqual(['impressum', 'datenschutz']);
      expect(await page.evaluate(() => window.scrollY)).toBe(backgroundY);
      expect(page.url()).toBe('https://hasimuener.de/__legal');
      await expect(panel(page).getByRole('navigation', { name: 'Inhalt dieser Seite' })).toBeVisible();
      await panel(page).locator('.legal-index__link[href="#rechte"]').click();
      await expect(panel(page).locator('#privacy-rights')).toBeFocused();
      await nearPanelTop(page, '.legal-modal #rechte');
      await expect(panel(page).locator('.legal-index__link[href="#rechte"]')).toHaveAttribute('aria-current', 'location');
      expect(await page.evaluate(() => window.scrollY)).toBe(backgroundY);
      await expect.poll(() => panel(page).locator('.legal-page').evaluate(el => Number(el.style.getPropertyValue('--legal-progress')))).toBeGreaterThan(0.8);
      await panel(page).evaluate(el => el.scrollTo({ top: 0, behavior: 'instant' }));
      await panel(page).locator('.privacy-actions').getByRole('link', { name: 'Ihre Rechte', exact: true }).click();
      await nearPanelTop(page, '.legal-modal #rechte');
      await panel(page).evaluate(el => el.scrollTo({ top: 0, behavior: 'instant' }));
      await panel(page).getByRole('link', { name: 'Zum Impressum', exact: true }).click();
      await expect(panel(page).locator('#imprint-ddg')).toBeFocused();
      await nearPanelTop(page, '.legal-modal #impressum-inhalt');
      expect(requests).toEqual(['impressum', 'datenschutz']);
      await expect.poll(() => panel(page).evaluate(el => el.scrollWidth <= el.clientWidth)).toBe(true);
      await page.keyboard.press('Escape');
      await expect(dialog(page)).toBeHidden();
      await expect(footerLink(page, 'impressum')).toBeFocused();
      expect(await page.evaluate(() => window.scrollY)).toBe(backgroundY);
    });
  }
}

test('legal modal: secondary and primary buttons keep contrasting text on hover and keyboard focus', async ({ page }) => {
  await setup(page);
  await openImprint(page);
  const secondary = switchLink(page);
  await secondary.hover();
  await expect(secondary).toHaveCSS('color', 'rgb(23, 20, 18)');
  await panel(page).getByRole('link', { name: 'E-Mail schreiben' }).focus();
  await page.keyboard.press('Tab');
  await expect(secondary).toBeFocused();
  await expect(secondary).toHaveCSS('outline-style', 'solid');
  await expect(secondary).toHaveCSS('color', 'rgb(23, 20, 18)');
  const primary = panel(page).getByRole('link', { name: 'E-Mail schreiben' });
  await primary.hover();
  await expect(primary).toHaveCSS('color', 'rgb(255, 255, 255)');
});

test('legal modal: focus is contained, overflow and pre-existing inert state are restored', async ({ page }) => {
  await setup(page);
  await page.evaluate(() => { document.documentElement.style.overflow = 'clip'; document.querySelector('main').inert = true; });
  await openImprint(page);
  const backgroundY = await page.evaluate(() => window.scrollY);
  await expect(panel(page).getByRole('button', { name: 'Schließen' })).toBeFocused();
  await page.keyboard.press('Shift+Tab');
  expect(await page.evaluate(() => !!document.activeElement.closest('.legal-modal'))).toBe(true);
  await expect(panel(page).getByRole('link', { name: 'Kontaktseite' })).toBeFocused();
  await expect(panel(page).getByRole('link', { name: 'Kontaktseite' })).toBeInViewport();
  expect(await page.evaluate(() => window.scrollY)).toBe(backgroundY);
  await page.keyboard.press('Tab');
  await expect(panel(page).getByRole('button', { name: 'Schließen' })).toBeFocused();
  await panel(page).getByRole('button', { name: 'Schließen' }).click();
  await expect(footerLink(page, 'impressum')).toBeFocused();
  expect(await page.evaluate(() => document.documentElement.style.overflow)).toBe('clip');
  expect(await page.locator('main').evaluate(el => el.inert)).toBe(true);
  expect(await page.locator('.fuss').evaluate(el => el.inert)).toBe(false);
});

test('legal modal: a late response cannot replace a newer document', async ({ page }) => {
  let release;
  const blocked = new Promise(resolve => { release = resolve; });
  await setup(page, { delay: slug => slug === 'impressum' ? blocked : Promise.resolve() });
  await footerLink(page, 'impressum').click();
  await expect(panel(page).getByRole('status')).toBeVisible();
  await page.keyboard.press('Escape');
  await footerLink(page, 'datenschutz').click();
  await expect(panel(page).locator('.privacy-page[data-legal-ready]')).toBeVisible();
  const settled = page.waitForResponse('https://hasimuener.de/impressum/');
  release();
  await settled;
  await expect(panel(page).locator('.privacy-page')).toBeVisible();
  await expect(panel(page).locator('.imprint-page')).toHaveCount(0);
});

test('legal modal: a failed load follows the original fragment URL', async ({ page }) => {
  await setup(page);
  await openImprint(page);
  await page.route('https://hasimuener.de/datenschutz/', route => route.fulfill({ status: 500, body: 'Unavailable' }), { times: 1 });
  await switchLink(page).click();
  await expect(page).toHaveURL('https://hasimuener.de/datenschutz/#datenschutz-inhalt');
});

test('legal modal: Ctrl-click and links to another tab retain browser behavior', async ({ page }) => {
  const requests = await setup(page);
  expect(await footerLink(page, 'impressum').evaluate(el => {
    let handled;
    document.addEventListener('click', event => { handled = event.defaultPrevented; event.preventDefault(); }, { once: true });
    el.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, button: 0, ctrlKey: true }));
    return handled;
  })).toBe(false);
  await expect(dialog(page)).toHaveCount(0);
  await footerLink(page, 'datenschutz').evaluate(el => el.target = '_blank');
  const newPage = page.waitForEvent('popup');
  await footerLink(page, 'datenschutz').click();
  const popup = await newPage;
  await expect(popup).toHaveURL('https://hasimuener.de/datenschutz/');
  await popup.close();
  await expect(dialog(page)).toHaveCount(0);
  expect(requests).toEqual(['datenschutz']);
});

test('legal standalone: rights link scrolls and focuses the document', async ({ page }) => {
  await setup(page, { standalone: 'datenschutz/' });
  await page.locator('.privacy-actions').getByRole('link', { name: 'Ihre Rechte', exact: true }).click();
  await expect(page.locator('#privacy-rights')).toBeFocused();
  await expect(page).toHaveURL('https://hasimuener.de/datenschutz/#rechte');
  await expect.poll(() => page.evaluate(() => window.scrollY)).toBeGreaterThan(1000);
});

test('legal standalone: document switches retain native fragment navigation without JavaScript', async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false });
  const page = await context.newPage();
  await setup(page, { standalone: 'impressum/' });
  await page.getByRole('link', { name: 'Datenschutz', exact: true }).first().click();
  await expect(page).toHaveURL('https://hasimuener.de/datenschutz/#datenschutz-inhalt');
  await expect(page.locator('#datenschutz-inhalt')).toBeInViewport();
  await context.close();
});

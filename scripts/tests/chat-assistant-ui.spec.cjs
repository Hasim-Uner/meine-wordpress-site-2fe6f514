const { test, expect } = require('@playwright/test');
const { execFileSync } = require('node:child_process');
const { readFileSync } = require('node:fs');
const path = require('node:path');
const theme = path.resolve(__dirname, '../../blocksy-child');
const facade = execFileSync('php', [path.join(__dirname, 'render-chat.php')], { encoding: 'utf8' });
const system = readFileSync(path.join(theme, 'assets/css/system.css'), 'utf8');
const answer = 'event: text\ndata: {"text":"Gern. Was soll Ihre Website leisten?"}\n\n';

async function setup(page, { fail = false, delayed = false } = {}) {
  const requests = [];
  let release;
  const responseReady = delayed ? new Promise(resolve => { release = resolve; }) : Promise.resolve();
  await page.route('**/*', async route => {
    const url = new URL(route.request().url());
    if (url.pathname.startsWith('/theme/assets/')) {
      requests.push(url.pathname);
      return route.fulfill({
        contentType: url.pathname.endsWith('.js') ? 'text/javascript' : 'text/css',
        body: readFileSync(path.join(theme, url.pathname.replace('/theme/', '')), 'utf8'),
      });
    }
    if (url.pathname.startsWith('/wp-json/')) {
      requests.push(url.pathname);
      await responseReady;
      return route.fulfill(fail
        ? { status: 503, contentType: 'application/json', body: JSON.stringify({ message: 'Bitte erneut versuchen.' }) }
        : { contentType: 'text/event-stream', body: answer });
    }
    if (url.origin !== 'https://hasimuener.de') return route.abort();
    return route.fulfill({ contentType: 'text/html', body: `<!doctype html><html lang="de"><head>
      <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><style>${system}</style>
      <style>body{margin:0}main{min-height:2600px;padding:24px}#contact{margin-top:1800px}footer.fuss{min-height:400px;padding:24px}</style>
      </head><body><main><h1>WordPress-Projekte</h1><section id="contact"><h2>Projekt anfragen</h2>
      <label>E-Mail<input id="contact-email" type="email"></label></section></main>
      <footer class="fuss" role="contentinfo">Kontakt</footer>${facade}</body></html>` });
  });
  await page.goto('https://hasimuener.de/');
  return { requests, release: () => release?.() };
}

test('footer offers help once without opening chat, loading AI or interrupting a form', async ({ page }) => {
  const { requests } = await setup(page);
  await page.locator('#contact-email').focus();
  await page.locator('footer').scrollIntoViewIfNeeded();
  await expect(page.locator('.hu-chat__hint')).toBeHidden();
  await expect(page.locator('#contact-email')).toBeFocused();
  expect(requests).toEqual([]);
  await page.locator('#contact-email').evaluate(input => input.blur());
  await expect(page.locator('.hu-chat__hint')).toBeVisible();
  await expect(page.locator('#hu-chat-bar')).toHaveCount(0);
  expect(requests).toEqual([]);
  await page.getByRole('button', { name: 'Chat-Hinweis schließen' }).click();
  await expect(page.locator('.hu-chat__hint')).toBeHidden();
  await page.reload();
  await page.locator('footer').scrollIntoViewIfNeeded();
  await expect(page.locator('.hu-chat__hint')).toBeHidden();
});

for (const viewport of [{ width: 1440, height: 900 }, { width: 390, height: 844 }, { width: 320, height: 568 }, { width: 844, height: 390 }]) {
  test(`black chat fits ${viewport.width}×${viewport.height}, preserves KI identity and keyboard access`, async ({ page }) => {
    await page.setViewportSize(viewport);
    await setup(page);
    const launcher = page.getByRole('button', { name: 'KI-Assistent öffnen', exact: true });
    await launcher.click();
    const panel = page.locator('#hu-chat-bar');
    await expect(panel).toBeVisible();
    await expect(page.locator('.hu-chat__disclosure')).toHaveText('KI-Projektassistent');
    await expect(panel).toHaveCSS('background-color', 'rgb(8, 8, 8)');
    await expect(page.getByRole('heading', { name: 'Was haben Sie vor?' })).toBeVisible();
    await expect(page.locator('.hu-chat__input')).toBeFocused();
    const fits = async () => {
      const box = await panel.boundingBox();
      expect(box.width).toBeLessThanOrEqual(380);
      expect(box.x).toBeGreaterThanOrEqual(0);
      expect(box.y).toBeGreaterThanOrEqual(0);
      expect(box.x + box.width).toBeLessThanOrEqual(viewport.width);
      expect(box.y + box.height).toBeLessThanOrEqual(viewport.height);
      await expect(page.getByRole('button', { name: 'Senden', exact: true })).toBeInViewport();
      await expect(page.locator('.hu-chat__disclosure')).toBeInViewport();
    };
    await fits();
    await page.locator('.hu-chat__input').fill('Ich brauche eine Website.');
    await page.locator('.hu-chat__input').press('Enter');
    await expect(page.locator('.hu-chat__msg--assistant')).toContainText('Was soll Ihre Website leisten?');
    await fits();
    await page.locator('.hu-chat__input').press('Escape');
    await expect(panel).toBeHidden();
    await expect(launcher).toBeFocused();
    await launcher.press('Enter');
    await expect(panel).toBeVisible();
    await expect(page.locator('.hu-chat__msg--assistant')).toContainText('Was soll Ihre Website leisten?');
    await expect(page.locator('.hu-chat__disclosure')).toBeVisible();
  });
}

test('failed response remains visible and keeps the message for retry', async ({ page }) => {
  await setup(page, { fail: true });
  await page.evaluate(() => sessionStorage.setItem('hu-chat-v1', JSON.stringify({ messages: [
    { role: 'user', text: 'Eine frühere Frage' }, { role: 'assistant', text: 'Eine frühere Antwort' },
  ] })));
  await page.getByRole('button', { name: 'KI-Assistent öffnen', exact: true }).click();
  const input = page.locator('.hu-chat__input');
  await input.fill('Meine Frage');
  await input.press('Enter');
  await expect(page.locator('.hu-chat__status')).toBeVisible();
  await expect(page.locator('.hu-chat__status')).toHaveText('Bitte erneut versuchen.');
  await expect(input).toHaveValue('Meine Frage');
  await page.getByRole('button', { name: 'Neues Gespräch' }).click();
  await expect(page.locator('.hu-chat__status')).toBeHidden();
  await expect(page.getByRole('heading', { name: 'Was haben Sie vor?' })).toBeVisible();
});

test('late answer preserves collapse and focus in another form', async ({ page }) => {
  const { requests, release } = await setup(page, { delayed: true });
  await page.getByRole('button', { name: 'KI-Assistent öffnen', exact: true }).click();
  await page.locator('.hu-chat__input').fill('Meine Frage');
  await page.locator('.hu-chat__input').press('Enter');
  await expect.poll(() => requests.some(url => url.includes('/wp-json/'))).toBe(true);
  await page.getByRole('button', { name: 'KI-Assistent einklappen' }).click();
  await page.locator('#contact-email').focus();
  release();
  await expect(page.locator('.hu-chat__send')).toBeEnabled();
  await expect(page.locator('#hu-chat-bar')).toBeHidden();
  await expect(page.locator('#contact-email')).toBeFocused();
});

test('reduced motion leaves the chat readable without animation', async ({ page }) => {
  await page.emulateMedia({ reducedMotion: 'reduce' });
  await setup(page);
  await page.getByRole('button', { name: 'KI-Assistent öffnen', exact: true }).click();
  await expect(page.locator('#hu-chat-bar')).toHaveCSS('animation-name', 'none');
  await expect(page.getByRole('heading', { name: 'Was haben Sie vor?' })).toBeVisible();
});

test('without JavaScript the normal enquiry stays available and no inert launcher appears', async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false });
  const page = await context.newPage();
  await setup(page);
  await expect(page.locator('.hu-chat')).toBeHidden();
  await expect(page.locator('#contact-email')).toBeVisible();
  await context.close();
});

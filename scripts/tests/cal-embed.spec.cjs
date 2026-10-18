const { test, expect } = require('@playwright/test');
const path = require('node:path');

const script = path.resolve(__dirname, '../../blocksy-child/assets/js/cal-embed.js');
const bookingUrl = 'https://cal.com/hasim-uener/30min?overlayCalendar=true';
const embedUrl = 'https://app.cal.com/embed/embed.js';
const vendorStub = `
  window.calCalls = [];
  window.Cal = function () {};
  window.Cal.version = 'test';
  window.Cal.ns = { booking: function () {
    window.calCalls.push(Array.from(arguments));
  } };
`;

async function setup(page, { failEmbed = false } = {}) {
  const requests = [];
  await page.route('https://hasimuener.de/**', route => route.fulfill({
    contentType: 'text/html',
    body: `<!doctype html><html lang="de"><head><meta charset="utf-8"></head><body>
      <a href="${bookingUrl}" data-track-section="contact_intro">Termin wählen</a>
      <script>window.NexusCalEmbedConfig = ${JSON.stringify({
        bookingUrl,
        calOrigin: 'https://cal.com',
        calLink: 'hasim-uener/30min',
        namespace: 'booking',
        embedScriptUrl: embedUrl,
      })};</script>
    </body></html>`,
  }));
  // No request reaches a real scheduling provider, even on a regression.
  await page.route(/^https:\/\/(?:app\.)?cal\.com\//, route => {
    const url = route.request().url();
    requests.push(url);
    if (url === embedUrl && failEmbed) return route.abort();
    return route.fulfill({
      contentType: url === embedUrl ? 'text/javascript' : 'text/html',
      body: url === embedUrl ? vendorStub : '<h1>Buchungskalender</h1>',
    });
  });
  await page.goto('https://hasimuener.de/kontakt/?private=value#briefing');
  await page.addScriptTag({ path: script });
  return requests;
}

for (const activation of ['mouse', 'keyboard']) {
  test(`Cal.com stays disconnected until completed ${activation} activation`, async ({ page }) => {
    const requests = await setup(page);
    const link = page.getByRole('link', { name: 'Termin wählen' });
    await link.hover();
    await link.focus();
    await link.dispatchEvent('pointerdown', { button: 0, pointerType: 'mouse' });
    await link.dispatchEvent('pointercancel', { pointerType: 'mouse' });
    await link.dispatchEvent('pointerdown', { button: 2, pointerType: 'mouse' });
    await link.dispatchEvent('pointercancel', { pointerType: 'mouse' });
    await link.dispatchEvent('pointerdown', { button: 0, pointerType: 'touch' });
    await link.dispatchEvent('pointercancel', { pointerType: 'touch' });
    // Let queued script insertion/request work settle without activating a link.
    await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
    expect(requests).toEqual([]);
    await expect(page.locator(`script[src="${embedUrl}"]`)).toHaveCount(0);

    if (activation === 'keyboard') await link.press('Enter');
    else await link.click();
    await expect.poll(() => page.evaluate(() => window.calCalls)).toEqual([
      ['modal', {
        calLink: '/hasim-uener/30min',
        calOrigin: 'https://cal.com',
        config: { 'metadata[quelle]': 'contact_intro', 'metadata[seite]': '/kontakt/' },
      }],
    ]);
    expect(requests).toEqual([embedUrl]);
    await expect(link).toHaveAttribute('href', bookingUrl);
  });
}

test('Cal.com keeps its ordinary booking link when the embed cannot load', async ({ page }) => {
  const requests = await setup(page, { failEmbed: true });
  await page.getByRole('link', { name: 'Termin wählen' }).click();
  await expect(page).toHaveURL(bookingUrl);
  await expect(page.getByRole('heading', { name: 'Buchungskalender' })).toBeVisible();
  expect(requests).toEqual([embedUrl, bookingUrl]);
});

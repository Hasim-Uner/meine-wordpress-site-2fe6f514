const { test, expect } = require('@playwright/test');
const path = require('node:path');
const { execFileSync } = require('node:child_process');
const js = name => path.resolve(__dirname, '../../blocksy-child/assets/js', name);

// Actual contact PHP template; small native fixtures for the adjacent forms.
const contactHtml = kind => execFileSync('php', [path.join(__dirname, 'render-contact.php'), kind], { encoding: 'utf8' });

const fixtures = {
  contact: { script: 'contact.js', html: contactHtml('contact'), form: '[data-contact-form]', feedback: '[data-contact-feedback]', button: '[data-contact-submit]' },
  assessment: { script: 'contact.js', html: contactHtml('assessment'), form: '[data-contact-form]', feedback: '[data-contact-feedback]', button: '[data-contact-submit]' },
  whitelabel: {
    script: 'whitelabel.js', form: '[data-wl-request-form]', feedback: '[data-wl-feedback]', button: '[data-wl-submit]',
    html: `<div data-wl-error-summary class="is-hidden" tabindex="-1"><ul data-wl-error-list></ul></div>
      <form data-wl-request-form action="https://example.test/wp-json/nexus/v1/whitelabel-request" novalidate>
      <textarea id="wl-task" name="task"></textarea><input id="wl-email" name="email" type="email">
      <input data-wl-case name="case" value="aufgabe" hidden><button data-wl-submit>Aufgabe senden</button>
      <div data-wl-feedback role="status"></div></form>`,
  },
  blog: {
    script: 'blog-notify.js', form: '[data-blog-notify-form]', feedback: '[data-blog-notify-feedback]', button: 'button[type="submit"]',
    html: `<form data-blog-notify-form><input name="email" type="email"><input name="nonce" value="fixture-nonce" hidden>
      <button type="submit">Neue Artikel erhalten</button><div data-blog-notify-feedback role="status"></div></form>`,
  },
  marketcheck: {
    script: 'solar-marketcheck-compact.js', form: '.mc2-form', feedback: '.mc2-submit-error', button: '.mc2-primary[type="submit"]',
    html: '<div class="solara-landing"><div id="sol-quiz-mount"></div></div>',
  },
};

async function setup(page, kind) {
  const fixture = fixtures[kind];
  // No real server, cookies, CRM or mail: every network attempt is intercepted.
  await page.route('**/*', route => route.fulfill({ contentType: 'text/html', body: '<html></html>' }));
  await page.goto('https://example.test/kontakt/?utm_source=fixture');
  await page.setContent('<style>.is-hidden,[hidden]{display:none!important}</style>' + fixture.html);
  await page.clock.install();
  await page.evaluate(() => {
    window.NexusContactConfig = { restEndpoint: '/wp-json/nexus/v1/contact-request' };
    window.NexusBlogNotifyConfig = { restEndpoint: '/wp-json/nexus/v1/blog-subscribe' };
    window.NexusMarktcheckConfig = { restEndpoint: '/wp-json/nexus/v1/audit-request' };
    window.dataLayer = [];
    window.requests = [];
    window.nativeFetch = window.fetch;
    window.fetch = (url, options) => new Promise((resolve, reject) => {
      // Deliberately ignore abort: prove late transport completions cannot touch UI.
      window.requests.push({ url, options, resolve, reject });
    });
  });
  await page.addScriptTag({ path: js('nexus-core.js') });
  await page.addScriptTag({ path: js(fixture.script) });
  if (kind === 'marketcheck') {
    for (const [name, value] of Object.entries({ solution_focus: 'photovoltaik', business_fit: 'founder_led_regional', sales_team_size: 'one', project_timing: 'sofort' })) {
      await page.selectOption(`[name="${name}"]`, value);
    }
    await page.click(fixture.button);
    await page.fill('[name="company"]', 'Fixture GmbH');
    await page.fill('[name="name"]', 'Fixture Person');
    await page.selectOption('[name="position"]', 'Geschäftsführung / Inhaber');
    await page.fill('[name="postal_code"]', '30159');
    await page.check('[name="consent_privacy"]');
  } else if (kind === 'contact' || kind === 'assessment') {
    await page.fill('[name="website_url"]', 'https://example.test/');
    if (kind === 'contact') await page.fill('[name="message"]', 'Bitte das bestehende Tracking prüfen.');
    await page.click('[data-contact-next]');
    await page.fill('[name="name"]', 'Fixture Person');
    await page.check('[name="consent"]');
  } else if (kind === 'whitelabel') {
    await page.fill('[name="task"]', 'Bitte das bestehende Tracking prüfen.');
  }
  await page.fill('[name="email"]', 'fixture@example.test');
  return fixture;
}

async function values(page, fixture) {
  return page.locator(fixture.form).evaluate(form => [...form.elements]
    .filter(control => control.name)
    .map(control => [control.name, control.value, control.checked]));
}

async function respond(page, status, body, index = 0) {
  await page.evaluate(({ status, body, index }) => {
    window.requests[index].resolve(new Response(status === 204 ? null : body, { status }));
  }, { status, body, index });
}

async function submit(page, fixture) {
  await page.click(fixture.button);
  await expect.poll(() => page.evaluate(() => window.requests.length)).toBe(1);
  await expect(page.locator(fixture.button)).toBeDisabled();
}

const malformed = [
  ['invalid JSON', '{'], ['empty body', ''], ['HTML', '<html>Proxy error</html>'],
  ['empty object', '{}'], ['foreign JSON', '{"status":"ok"}'], ['null', 'null'],
  ['array', '[]'], ['boolean', 'true'], ['number', '1'], ['string', '"ok"'],
  ['string ok', '{"ok":"false"}'], ['numeric ok', '{"ok":1}'], ['null ok', '{"ok":null}'],
];

for (const kind of Object.keys(fixtures)) {
  test.describe(kind, () => {
    for (const [label, body] of malformed) {
      test(`rejects ${label} with HTTP 200`, async ({ page }) => {
        const f = await setup(page, kind);
        const before = await values(page, f);
        await submit(page, f);
        await respond(page, 200, body);
        await expect(page.locator(f.feedback)).toContainText('nicht bestätigt');
        await expect(page.locator(f.button)).toBeEnabled();
        expect(await values(page, f)).toEqual(before);
        await expect(page.locator('.is-success, .mc2-success')).toHaveCount(0);
        await expect(page.locator('[name="email"]')).toBeEnabled();
      });
    }

    for (const status of [204, 400, 429, 500, 200]) {
      test(`retains inputs for HTTP ${status}${status === 200 ? ' ok false' : ''}`, async ({ page }) => {
        const f = await setup(page, kind);
        const before = await values(page, f);
        await submit(page, f);
        await respond(page, status, JSON.stringify({ ok: false, error: 'Bitte später erneut versuchen.', message: 'Bitte später erneut versuchen.' }));
        await expect(page.locator(f.feedback)).toContainText(status === 204 ? 'nicht bestätigt' : 'Bitte später');
        await expect(page.locator(f.button)).toBeEnabled();
        expect(await values(page, f)).toEqual(before);
      });
    }

    for (const status of [200, 201]) {
      test(`accepts explicit success HTTP ${status} without CRM id`, async ({ page }) => {
        const f = await setup(page, kind);
        await submit(page, f);
        await respond(page, status, '{"ok":true,"contactId":0,"message":"Danke. Eingegangen."}');
        if (kind === 'marketcheck') {
          await expect(page.locator('.mc2-success')).toBeVisible();
          await expect(page.locator(f.form)).toHaveCount(0);
        } else {
          await expect(page.locator(f.feedback)).toHaveClass(/is-success/);
          await expect(page.locator('[name="email"]')).toHaveValue('');
          await expect(page.locator(f.button)).toBeEnabled();
        }
      });
    }

    test('HTTP failure overrides ok true', async ({ page }) => {
      const f = await setup(page, kind);
      const before = await values(page, f);
      await submit(page, f);
      await respond(page, 500, '{"ok":true}');
      await expect(page.locator(f.button)).toBeEnabled();
      expect(await values(page, f)).toEqual(before);
      await expect(page.locator('.is-success, .mc2-success')).toHaveCount(0);
      await expect(page.locator(f.feedback)).not.toBeEmpty();
    });

    test('network failure keeps inputs and permits recovery', async ({ page }) => {
      const f = await setup(page, kind);
      const before = await values(page, f);
      await submit(page, f);
      await page.evaluate(() => window.requests[0].reject(new TypeError('Failed to fetch')));
      await expect(page.locator(f.feedback)).toContainText('bereits angekommen');
      await expect(page.locator(f.button)).toBeEnabled();
      expect(await values(page, f)).toEqual(before);
    });

    test('double submit, timeout and late response during a newer attempt', async ({ page }) => {
      const f = await setup(page, kind);
      const before = await values(page, f);
      await submit(page, f);
      await expect(page.locator('[name="email"]')).toBeDisabled();
      await page.locator(f.form).dispatchEvent('submit');
      expect(await page.evaluate(() => window.requests.length)).toBe(1);
      await page.clock.fastForward(30001);
      await expect(page.locator(f.feedback)).toContainText('bereits angekommen');
      await expect(page.locator(f.button)).toBeEnabled();
      await expect(page.locator('[name="email"]')).toBeEnabled();
      expect(await values(page, f)).toEqual(before);
      expect(await page.evaluate(() => window.requests.length)).toBe(1); // No automatic retry.
      await page.click(f.button);
      expect(await page.evaluate(() => window.requests.length)).toBe(2);
      await respond(page, 201, '{"ok":true}', 0);
      await expect(page.locator(f.button)).toBeDisabled();
      expect(await values(page, f)).toEqual(before);
      await expect(page.locator('.is-success, .mc2-success')).toHaveCount(0);
      await respond(page, 429, '{"ok":false,"error":"Bitte später.","message":"Bitte später."}', 1);
      await expect(page.locator(f.feedback)).toContainText('Bitte später');
      await expect(page.locator(f.button)).toBeEnabled();
    });
  });
}

for (const kind of ['contact', 'assessment', 'whitelabel', 'marketcheck']) {
  test(`${kind} server validation marks the field and preserves inputs`, async ({ page }) => {
    const f = await setup(page, kind);
    const before = await values(page, f);
    await submit(page, f);
    await respond(page, 400, JSON.stringify({ ok: false, error: 'Bitte E-Mail prüfen.', message: 'Bitte E-Mail prüfen.', error_code: 'invalid_email', error_details: { field: 'email' } }));
    await expect(page.locator('[name="email"]')).toHaveAttribute('aria-invalid', 'true');
    await expect(page.locator(f.button)).toBeEnabled();
    await expect(page.locator(kind === 'marketcheck' ? '[data-field="email"] .mc2-error' : f.feedback)).toContainText('Bitte E-Mail prüfen');
    expect(await values(page, f)).toEqual(before);
  });
}

for (const kind of ['contact', 'assessment']) {
  test(`${kind} website validation reveals the earlier step`, async ({ page }) => {
    const f = await setup(page, kind);
    const before = await values(page, f);
    await submit(page, f);
    await respond(page, 400, '{"ok":false,"error":"Bitte Website prüfen.","error_code":"invalid_website"}');
    await expect(page.locator('[name="website_url"]')).toHaveAttribute('aria-invalid', 'true');
    await expect(page.locator('[name="website_url"]')).toBeVisible();
    await expect(page.locator('[name="website_url"]')).toBeFocused();
    expect(await values(page, f)).toEqual(before);
    await expect(page.locator('[data-contact-next]')).toBeEnabled();
  });
}

test('local validation keeps the assessment URL required and project message required', async ({ page }) => {
  for (const kind of ['contact', 'assessment']) {
    const f = await setup(page, kind);
    const field = kind === 'assessment' ? 'website_url' : 'message';
    await page.click('[data-contact-prev]');
    await page.fill(`[name="${field}"]`, '');
    await page.click('[data-contact-next]');
    await expect(page.locator(`[name="${field}"]`)).toHaveAttribute('aria-invalid', 'true');
    expect(await page.evaluate(() => window.requests.length)).toBe(0);
    await expect(page.locator(f.button)).toBeEnabled();
  }
});

test('response body timeout, without AbortController, ignores a late body', async ({ page }) => {
  const f = await setup(page, 'contact');
  await page.evaluate(() => { window.AbortController = undefined; });
  const before = await values(page, f);
  await submit(page, f);
  await page.evaluate(() => window.requests[0].resolve({ ok: true, json: () => new Promise(resolve => { window.finishBody = resolve; }) }));
  await page.clock.fastForward(30001);
  await expect(page.locator(f.feedback)).toContainText('Antwort dauert zu lange');
  await expect(page.locator(f.button)).toBeEnabled();
  await page.evaluate(() => window.finishBody({ ok: true }));
  await expect(page.locator(f.feedback)).toContainText('nicht bestätigt');
  expect(await values(page, f)).toEqual(before);
});

test('contact native fetch confirms payload and rejects a proxy HTML response', async ({ page }) => {
  const f = await setup(page, 'contact');
  let payload;
  await page.route('**/wp-json/nexus/v1/contact-request', route => {
    payload = route.request().postDataJSON();
    return route.fulfill({ status: 200, contentType: 'text/html', body: '<html>Proxy</html>' });
  });
  await page.evaluate(() => { window.fetch = window.nativeFetch; });
  const before = await values(page, f);
  await page.click(f.button);
  await expect(page.locator(f.feedback)).toContainText('nicht bestätigt');
  expect(payload).toMatchObject({ request_type: 'project', focus: 'tracking', consent: '1', ads_source: 'fixture', company_website: '' });
  expect(await values(page, f)).toEqual(before);
  await expect(page.locator(f.button)).toBeEnabled();
});

test('unscoped project still advances from topic to message to identity', async ({ page }) => {
    await page.route('**/*', route => route.fulfill({ contentType: 'text/html', body: contactHtml('project') }));
    await page.goto('https://example.test/kontakt/?type=project');
    await page.evaluate(() => { window.NexusContactConfig = { restEndpoint: '/wp-json/nexus/v1/contact-request' }; });
    await page.addScriptTag({ path: js('nexus-core.js') });
    await page.addScriptTag({ path: js('contact.js') });
    await page.selectOption('[name="focus"]', 'tracking');
    await expect(page.locator('[name="message"]')).toBeVisible();
    await page.fill('[name="message"]', 'Bitte das bestehende Tracking prüfen.');
    await page.click('[data-contact-next]');
    await expect(page.locator('[name="email"]')).toBeVisible();
    await expect(page.locator(fixtures.contact.button)).toBeVisible();
});

test('scoped tracking form keeps its submit label and optional payload', async ({ page }) => {
  const f = await setup(page, 'contact');
  await page.evaluate(() => {
    const form = document.querySelector('[data-contact-form]');
    const field = document.createElement('input');
    field.name = 'ad_platform_google_ads';
    field.type = 'hidden';
    field.value = '1';
    form.appendChild(field);
  });
  const label = await page.locator(f.button).textContent();
  await submit(page, f);
  expect(await page.evaluate(() => JSON.parse(window.requests[0].options.body))).toMatchObject({ ad_platform_google_ads: '1', focus: 'tracking' });
  await respond(page, 201, '{"ok":true,"contactId":123}');
  await expect(page.locator(f.feedback)).toHaveClass(/is-success/);
  await expect(page.locator(f.button)).toHaveText(label);
});

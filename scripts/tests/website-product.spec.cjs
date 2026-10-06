const { test, expect } = require('@playwright/test');
const path = require('node:path');
const fs = require('node:fs');
const { execFileSync } = require('node:child_process');

const theme = path.resolve(__dirname, '../../blocksy-child');
const render = () => execFileSync('php', [path.join(__dirname, 'render-website.php')], { encoding: 'utf8' });
const euro = n => n.toLocaleString('de-DE') + ' €';
const workingDays = n => n.toLocaleString('de-DE') + (n === 1 ? ' Werktag' : ' Werktage');
const canonicalQuotes = cases => JSON.parse(execFileSync('php', [path.join(__dirname, 'quote-website-fixture.php')], {
  input: JSON.stringify(cases),
  encoding: 'utf8',
}));
const types = { '.css':'text/css', '.js':'text/javascript', '.woff2':'font/woff2', '.webp':'image/webp' };

async function open(page, width = 1440, extensions = true, waitForFonts = true) {
  await page.setViewportSize({ width, height: 900 });
  await page.route('https://hasimuener.de/**', route => {
    const url = new URL(route.request().url());
    const prefix = '/wp-content/themes/blocksy-child/';
    if (url.pathname.startsWith(prefix)) {
      const file = path.join(theme, url.pathname.slice(prefix.length));
      return fs.existsSync(file)
        ? route.fulfill({ path:file, contentType:types[path.extname(file)] || 'application/octet-stream' })
        : route.fulfill({ status:404 });
    }
    return route.fulfill({ contentType:'text/html', body:render() });
  });
  await page.goto('https://hasimuener.de/wordpress-website-erstellen-lassen/');
  if (waitForFonts) await page.evaluate(() => document.fonts.ready);
  if (extensions && await page.locator('.aw-extras-panel').isVisible()) {
    await page.locator('.aw-extras-panel summary').click();
  }
}

async function choosePreset(page, pages) {
  await page.locator(`[data-website-scenario="${pages}"]`).click();
}

async function setComposition(page, { utility = 0, standard = 0, sales = 0 }) {
  await choosePreset(page, 1);
  for (let i = 0; i < utility; i++) await page.locator('#utility-plus').click();
  for (let i = 0; i < standard; i++) await page.locator('#standard-plus').click();
  for (let i = 0; i < sales; i++) await page.locator('#sales-plus').click();
}

for (const width of [320, 360, 768, 1440]) {
  test(`request website ${width}: responsive calculator and product proof`, async ({ page }) => {
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    await open(page, width);

    await expect(page).toHaveTitle('WordPress-Website erstellen lassen ab ' + euro(1900) + ' | Haşim Üner');
    await expect(page.locator('meta[name="description"]')).toHaveAttribute('content', /Grundsystem, technisches SEO, Formular und Übergabe/);
    await expect(page.locator('h1')).toHaveCount(1);
    expect(await page.locator('.anfrage-website > section').evaluateAll(els => els.map(el => el.id)))
      .toEqual(['hero','beleg','angebot','unterschied','zeit','fragen','anfrage']);

    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    const overflow = await page.locator('.anfrage-website *').evaluateAll(els => els.filter(el => {
      const rect = el.getBoundingClientRect();
      return rect.width > 0 && rect.right > innerWidth + 1;
    }).map(el => el.className));
    expect(overflow).toEqual([]);

    const references = page.locator('#beleg .aw-projekt');
    await expect(references).toHaveCount(1);
    await expect(references.nth(0)).toContainText('E3 New Energy');
    await expect(page.locator('#qualitaet, #durch, #m-anfragen, #m-klassisch')).toHaveCount(0);
    await expect(page.locator('.aw-kapitel a')).toHaveCount(3);
    await expect(page.locator('.aw-produktkarte')).toBeVisible();
    await expect(page.locator('.aw-modulband span')).toHaveCount(4);
    await expect(page.locator('#unterschied .aw-prinzipien article')).toHaveCount(3);
    await expect(page.locator('#unterschied .aw-ergebnisband')).toContainText('Eine Website, die Ihnen gehört.');

    const premiumUi = await page.evaluate(() => {
      const card = document.querySelector('.aw-produktkarte');
      const price = card.querySelector('.betrag');
      const principle = document.querySelector('#unterschied .aw-prinzipien h3');
      const included = document.querySelector('.aw-lieferumfang-kompakt .aw-status');
      return {
        cardBackground: getComputedStyle(card).backgroundColor,
        cardColor: getComputedStyle(card).color,
        priceColor: getComputedStyle(price).color,
        principleColor: getComputedStyle(principle).color,
        includedBackground: getComputedStyle(included).backgroundColor,
        includedColor: getComputedStyle(included).color,
        cardHeight: card.getBoundingClientRect().height,
      };
    });
    expect(premiumUi.cardBackground).toBe('rgb(23, 20, 15)');
    expect(premiumUi.cardColor).toBe('rgb(244, 241, 236)');
    expect(premiumUi.priceColor).toBe('rgb(244, 241, 236)');
    expect(premiumUi.principleColor).toBe('rgb(244, 241, 236)');
    expect(premiumUi.includedBackground).toBe('rgb(23, 20, 18)');
    expect(premiumUi.includedColor).toBe('rgb(255, 255, 255)');
    if (width === 1440) expect(premiumUi.cardHeight).toBeLessThan(540);

    await expect(page.locator('#fragen details')).toHaveCount(6);
    await expect(page.locator('#beleg')).not.toContainText(/150\s*€|22\s*€|85\s*%|1[.,]750|15\s*%/);

    await expect(page.locator('.aw-szenario')).toHaveCount(3);
    await expect(page.locator('#gesamt')).toHaveText(euro(3090));
    await expect(page.locator('#utility-pages')).toHaveText('0');
    await expect(page.locator('#standard-pages')).toHaveText('1');
    await expect(page.locator('#sales-pages')).toHaveText('1');
    await expect(page.locator('#texte')).not.toBeChecked();

    await page.locator('[data-website-scenario="1"]').focus();
    await page.keyboard.press('Enter');
    await expect(page.locator('#gesamt')).toHaveText(euro(1900));
    for (const id of ['utility-minus','standard-minus','sales-minus']) await expect(page.locator('#' + id)).toBeDisabled();

    await page.locator('#standard-plus').focus();
    await page.keyboard.press('Space');
    await expect(page.locator('#standard-pages')).toHaveText('1');
    await expect(page.locator('#gesamt')).toHaveText(euro(2300));
    await expect(page.locator('#bauzeit')).toHaveText(workingDays(3));

    expect(errors).toEqual([]);
  });
}

test('FAQ keeps only one answer open at a time', async ({ page }) => {
  await open(page);
  const items = page.locator('#fragen details');
  await items.nth(0).locator('summary').click();
  await expect(items.nth(0)).toHaveAttribute('open', '');

  await items.nth(1).locator('summary').click();
  await expect(items.nth(1)).toHaveAttribute('open', '');
  await expect(items.nth(0)).not.toHaveAttribute('open', '');
});

test('presets, project-wide tracking and CTA contract agree with PHP canon', async ({ page }) => {
  await open(page);
  const scenarios = [
    { pages:1, utility:0, standard:0, sales:0 },
    { pages:3, utility:0, standard:1, sales:1 },
    { pages:5, utility:1, standard:1, sales:2 },
  ];

  for (const scenario of scenarios) {
    await choosePreset(page, scenario.pages);
    for (const art of ['neubau','relaunch']) {
      await page.locator(`.art [data-art="${art}"]`).click();
      for (const tracking of [0,1]) {
        await page.locator('#tracking').setChecked(Boolean(tracking));
        const quote = canonicalQuotes([{
          seiten:scenario.pages,
          art,
          tracking,
          texte:0,
          utility_pages:scenario.utility,
          standard_pages:scenario.standard,
          sales_pages:scenario.sales,
        }])[0];

        await expect(page.locator('#gesamt')).toHaveText(euro(quote.price));
        await expect(page.locator('#bauzeit')).toHaveText((tracking || art === 'relaunch' || quote.days !== 1) ? workingDays(quote.days) : workingDays(quote.days));

        const hrefs = await page.locator('[data-website-cta]').evaluateAll(els => els.map(el => el.href));
        expect(new Set(hrefs).size).toBe(1);
        const params = Object.fromEntries(new URL(hrefs[0]).searchParams);
        expect(params).toEqual({
          type:'project',
          focus:'website',
          seiten:String(scenario.pages),
          art,
          kurz:String(scenario.utility),
          standard:String(scenario.standard),
          leistung:String(scenario.sales),
          texte:'0',
          ...(tracking ? { tracking:'1' } : {}),
        });
      }
    }
  }
});

test('manual page types reach ten pages and price each type independently', async ({ page }) => {
  await open(page);
  await setComposition(page, { utility:2, standard:3, sales:4 });
  await expect(page.locator('#utility-pages')).toHaveText('2');
  await expect(page.locator('#standard-pages')).toHaveText('3');
  await expect(page.locator('#sales-pages')).toHaveText('4');

  for (const id of ['utility-plus','standard-plus','sales-plus']) await expect(page.locator('#' + id)).toBeDisabled();

  const quote = canonicalQuotes([{
    seiten:10, art:'neubau', tracking:0, texte:0,
    utility_pages:2, standard_pages:3, sales_pages:4,
  }])[0];
  await expect(page.locator('#gesamt')).toHaveText(euro(quote.price));
  await expect(page.locator('#bauzeit')).toHaveText(workingDays(quote.days));

  const params = Object.fromEntries(new URL(await page.locator('#cta-angebot').getAttribute('href')).searchParams);
  expect(params).toMatchObject({ seiten:'10', kurz:'2', standard:'3', leistung:'4', texte:'0' });
});

test('copywriting is priced by selected page type instead of being free', async ({ page }) => {
  await open(page);
  await choosePreset(page, 1);
  await expect(page.locator('#text-option-preis')).toHaveText('+' + euro(290));
  await page.locator('#texte').check();
  await expect(page.locator('#gesamt')).toHaveText(euro(2190));
  await expect(page.locator('#betrag-texte')).toHaveText(euro(290));

  await choosePreset(page, 3);
  await expect(page.locator('#text-option-preis')).toHaveText('+' + euro(730));
  await expect(page.locator('#gesamt')).toHaveText(euro(3820));

  await choosePreset(page, 5);
  await expect(page.locator('#text-option-preis')).toHaveText('+' + euro(1070));
  await expect(page.locator('#gesamt')).toHaveText(euro(5250));
  await expect(page.locator('#tage-texts')).toHaveText(workingDays(2));

  await page.locator('#texte').uncheck();
  await expect(page.locator('#gesamt')).toHaveText(euro(4180));
  await expect(page.locator('#auswahl-texte')).toHaveText('Eigene Texte');
});

test('screendesign counts layouts; tracking and CRM remain project modules', async ({ page }) => {
  await open(page);
  await choosePreset(page, 5);
  await page.locator('#screendesign').check();
  await page.locator('#design-layouts').selectOption('2');
  await page.locator('#tracking').check();
  await page.locator('#crm').check();

  const quote = canonicalQuotes([{
    seiten:5, art:'neubau', tracking:1, texte:0, design:'neu', design_layouts:2, crm:1,
    utility_pages:1, standard_pages:1, sales_pages:2,
  }])[0];
  await expect(page.locator('#gesamt')).toHaveText(euro(quote.price));
  await expect(page.locator('#betrag-design')).toHaveText(euro(940));
  await expect(page.locator('#bauzeit')).toHaveText(workingDays(quote.days));

  await page.locator('#sales-plus').click();
  const six = canonicalQuotes([{
    seiten:6, art:'neubau', tracking:1, texte:0, design:'neu', design_layouts:2, crm:1,
    utility_pages:1, standard_pages:1, sales_pages:3,
  }])[0];
  await expect(page.locator('#gesamt')).toHaveText(euro(six.price));
  expect(six.price - quote.price).toBe(790);
});

test('product -> contact -> REST -> CRM keeps typed scope and recalculates server-side', async ({ page }) => {
  await open(page);
  await choosePreset(page, 5);
  await page.locator('.art [data-art="relaunch"]').click();
  await page.locator('#tracking').check();
  await page.locator('#screendesign').check();
  await page.locator('#design-layouts').selectOption('2');
  await page.locator('#crm').check();
  await page.locator('#dashboard').check();

  const search = new URL(await page.locator('#cta-angebot').getAttribute('href')).searchParams.toString();
  const html = execFileSync('php', [path.join(__dirname,'render-contact.php'), 'website', search], { encoding:'utf8' });
  await page.setContent('<style>.is-hidden,[hidden]{display:none!important}</style>' + html);

  await expect(page.locator('[name="focus"]')).toHaveValue('website');
  await expect(page.locator('[name="kurz"]')).toHaveValue('1');
  await expect(page.locator('[name="standard"]')).toHaveValue('1');
  await expect(page.locator('[name="leistung"]')).toHaveValue('2');
  await expect(page.locator('[data-website-scope]')).toContainText('5 Seiten · Relaunch · Tracking dazu · 7.000 € netto · mindestens ' + workingDays(14));

  let response;
  await page.route('**/wp-json/nexus/v1/contact-request', async route => {
    response = JSON.parse(execFileSync('php', [path.join(__dirname,'submit-website-fixture.php')], {
      input:JSON.stringify(route.request().postDataJSON()),
      encoding:'utf8',
    }));
    await route.fulfill({ status:response.status, contentType:'application/json', body:JSON.stringify(response.data) });
  });

  await page.evaluate(() => { window.NexusContactConfig = { restEndpoint:'/wp-json/nexus/v1/contact-request' }; });
  for (const file of ['nexus-core.js','contact.js']) await page.addScriptTag({ path:path.join(theme,'assets/js',file) });
  await page.locator('[name="message"]').fill('Wir bieten Beratung an und möchten im November starten.');
  await page.locator('[data-contact-next]').click();
  await page.locator('[name="name"]').fill('Fixture Person');
  await page.locator('[name="email"]').fill('fixture@example.test');
  await page.locator('[name="consent"]').check();
  await page.locator('[data-contact-submit]').click();
  await expect(page.locator('[data-contact-feedback]')).toHaveClass(/is-success/);

  expect(response.status).toBe(201);
  const meta = response.meta['1'];
  expect(meta._nexus_contact_website_pages).toBe(5);
  expect(meta._nexus_contact_website_pages_utility).toBe(1);
  expect(meta._nexus_contact_website_pages_standard).toBe(1);
  expect(meta._nexus_contact_website_pages_sales).toBe(2);
  expect(meta._nexus_contact_website_texts).toBe(0);
  expect(meta._nexus_contact_website_screendesign).toBe(1);
  expect(meta._nexus_contact_website_design_layouts).toBe(2);
  expect(meta._nexus_contact_website_days).toBe(14);
  expect(meta._nexus_contact_website_preparation_days).toBe(2.5);
  expect(meta._nexus_contact_website_implementation_days).toBe(11);
  expect(meta._nexus_contact_website_duration_open).toBe(1);
  expect(meta._nexus_contact_website_price).toBe(7000);
  expect(meta._nexus_contact_website_page_price).toBe(2280);
  expect(meta._nexus_contact_website_text_price).toBe(0);
  expect(meta._nexus_contact_website_design_price).toBe(940);

  expect(response.mails).toHaveLength(2);
  for (const mail of response.mails) {
    expect(mail.body).toContain('7.000');
    expect(mail.body).toContain('Freigegebene Texte vorhanden');
    expect(mail.body).toContain('CRM-Anbindung Standard');
    expect(mail.body).toContain('Daten-Dashboard nach Angebot');
  }
});

test('no JavaScript keeps a valid static three-page offer', async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled:false });
  const page = await context.newPage();
  await open(page, 360, false, false);

  await expect(page.locator('#leiste')).toBeHidden();
  await expect(page.locator('.aw-szenario')).toHaveCount(3);
  expect(await page.locator('[data-website-controls]').evaluateAll(els => els.every(el => getComputedStyle(el).display === 'none'))).toBe(true);
  await expect(page.locator('#gesamt')).toHaveText(euro(3090));

  const url = new URL(await page.locator('#cta-angebot').getAttribute('href'));
  expect(Object.fromEntries(url.searchParams)).toMatchObject({
    type:'project', focus:'website', seiten:'3', kurz:'0', standard:'1', leistung:'1', texte:'0',
  });
  expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
  await context.close();
});

test('reduced motion has no simulated loading or browser storage', async ({ page }) => {
  await page.emulateMedia({ reducedMotion:'reduce' });
  await open(page);
  await expect(page.locator('.laden, #ladebalken')).toHaveCount(0);
  expect(await page.locator('.anfrage-website').evaluate(el => el.getAnimations({ subtree:true }).length)).toBe(0);
  await page.locator('#cta-angebot').evaluate(el => { el.addEventListener('click', e => e.preventDefault()); el.click(); });
  expect(await page.evaluate(() => [localStorage.length, sessionStorage.length, document.cookie])).toEqual([0,0,'']);
});

for (const width of [320, 390, 768, 1440]) {
  test(`200 percent text: calculator reflows at ${width}`, async ({ page }) => {
    await open(page, width);
    await page.locator('#texte').check();
    await page.locator('#screendesign').check();
    await page.locator('#tracking').check();
    await page.locator('#crm').check();
    await page.locator('#dashboard').check();
    await page.evaluate(() => { document.documentElement.style.fontSize = '200%'; });
    await expect.poll(() => page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    const overflow = await page.locator('.anfrage-website *').evaluateAll(els => els.filter(el => {
      const r = el.getBoundingClientRect();
      return r.width > 0 && r.right > innerWidth + 1;
    }).map(el => el.className));
    expect(overflow).toEqual([]);
  });
}

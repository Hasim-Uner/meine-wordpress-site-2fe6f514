const { test, expect } = require('@playwright/test');
const path = require('node:path');
const fs = require('node:fs');
const { execFileSync } = require('node:child_process');
const theme = path.resolve(__dirname, '../../blocksy-child');
const render = () => execFileSync('php', [path.join(__dirname, 'render-website.php')], { encoding: 'utf8' });
const euro = n => n.toLocaleString('de-DE') + ' €';
const workingDays = n => n.toLocaleString('de-DE') + (n === 1 ? ' Werktag' : ' Werktage');
const canonicalQuotes = cases => JSON.parse(execFileSync('php', [path.join(__dirname, 'quote-website-fixture.php')], { input:JSON.stringify(cases), encoding:'utf8' }));
const types = { '.css':'text/css', '.js':'text/javascript', '.woff2':'font/woff2', '.webp':'image/webp' };
async function open(page, width = 1440, extensions = true, waitForFonts = true) {
  await page.setViewportSize({ width, height: 900 });
  await page.route('https://hasimuener.de/**', route => {
    const url = new URL(route.request().url()), prefix = '/wp-content/themes/blocksy-child/';
    if (url.pathname.startsWith(prefix)) {
      const file = path.join(theme, url.pathname.slice(prefix.length));
      return fs.existsSync(file) ? route.fulfill({ path:file, contentType:types[path.extname(file)] || 'application/octet-stream' }) : route.fulfill({status:404});
    }
    return route.fulfill({ contentType:'text/html', body:render() });
  });
  await page.goto('https://hasimuener.de/wordpress-website-erstellen-lassen/');
  if (waitForFonts) await page.evaluate(() => document.fonts.ready);
  // Combination tests exercise the optional choices explicitly; the default-state test keeps them closed.
  if (extensions && await page.locator('.aw-extras-panel').isVisible()) await page.locator('.aw-extras-panel summary').click();
}
for (const width of [320, 360, 768, 1440]) {
  test(`request website ${width}: layout, keyboard controls and sticky visibility`, async ({ page }) => {
    const errors=[]; page.on('pageerror', e=>errors.push(e.message));
    await open(page,width);
    await expect(page).toHaveTitle('WordPress-Website erstellen lassen ab ' + euro(1490) + ' | Haşim Üner');
    await expect(page.locator('h1')).toHaveCount(1);
    expect(await page.locator('.anfrage-website > section').evaluateAll(els => els.map(el => el.id)))
      .toEqual(['hero','qualitaet','beleg','angebot','erweiterungen','unterschied','zeit','fragen','anfrage']);
    await expect(page.locator('meta[name="description"]')).toHaveAttribute('content', /Custom Code, Texte und technisches SEO inklusive/);
    expect(await page.locator('h2').evaluateAll(els=>els.every(el=>el.id))).toBe(true);
    expect(await page.evaluate(()=>document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    const overflow = await page.locator('.anfrage-website *').evaluateAll(els=>els.filter(el=>{
      const r=el.getBoundingClientRect(); return r.width>0 && r.right>innerWidth+1;
    }).map(el=>el.className));
    expect(overflow).toEqual([]);
    const priceFits = await page.locator('.formel .betrag small').evaluate(el => el.getBoundingClientRect().right <= el.closest('.formel').getBoundingClientRect().right);
    expect(priceFits).toBe(true);
    const references = page.locator('#beleg .aw-projekt');
    await expect(references).toHaveCount(3);
    await expect(references.nth(0)).toContainText('E3 New Energy');
    await expect(references.nth(1)).toContainText('hasimuener.org');
    await expect(references.nth(2)).toContainText('civaka-azad.org');
    for (const image of await references.locator('.bild img').all()) {
      await image.scrollIntoViewIfNeeded();
      await expect.poll(() => image.evaluate(el => el.complete && el.naturalWidth > 0)).toBe(true);
    }
    await expect(page.locator('#beleg')).not.toContainText(/150\s*€|22\s*€|85\s*%|1[.,]750|15\s*%/);
    await expect(page.locator('#beleg a[href*="case-study-solar"]')).toHaveCount(0);
    await expect(page.locator('#beleg a[href="https://hasimuener.de/"]')).toHaveCount(0);
    await expect(references.first().locator('.aw-projekt-flow li')).toHaveText(['Klick', 'Landingpage', 'Formular', 'Tracking', 'CRM']);
    await page.locator('#beleg').screenshot({
      path: test.info().outputPath(`website-references-${width}.png`),
      style: '.leiste, #leiste { visibility: hidden !important; }',
    });
    await page.evaluate(() => window.scrollTo({ top: 0, behavior: 'instant' }));
    await page.screenshot({ path: test.info().outputPath(`website-${width}.png`), fullPage: true });
    await expect(page.locator('#leiste')).toBeHidden();
    await page.locator('#m-anfragen').focus(); await page.keyboard.press('Space');
    await expect(page.locator('#m-anfragen')).toHaveAttribute('aria-pressed','true');
    await page.locator('.stellen li').nth(0).focus();
    await expect(page.locator('.v-anfragen .pin[data-pin="1"]')).toHaveClass(/an/);
    await page.locator('.groessen [data-seiten="1"]').focus(); await page.keyboard.press('Enter');
    await expect(page.locator('#gesamt')).toHaveText(euro(1490));
    await expect(page.locator('#minus')).toBeDisabled();
    await page.locator('#plus').focus(); await page.keyboard.press('Space');
    await expect(page.locator('#weitere')).toHaveText('1');
    await expect(page.locator('#bauzeit')).toHaveText(workingDays(4));
    await page.locator('#angebot').evaluate(el=>el.scrollIntoView({block:'start',behavior:'instant'}));
    if(width >= 1100) await expect(page.locator('#leiste')).toBeHidden();
    else {
      await expect(page.locator('#leiste')).toBeHidden();
      await page.locator('.aw-design').evaluate(el=>el.scrollIntoView({block:'start',behavior:'instant'}));
      await expect(page.locator('#leiste')).toBeVisible();
    }
    await page.locator('[data-website-cta="abschluss"]').scrollIntoViewIfNeeded();
    await expect(page.locator('#leiste')).toBeHidden();
    await expect(page.locator('#cta-leiste')).toHaveAttribute('tabindex','-1');
    expect(errors).toEqual([]);
  });
}
test('40 scopes: price, time and every CTA use the same configuration', async ({ page }) => {
  test.setTimeout(30000);
  await open(page);
  const cases=[];
  for(let seiten=1;seiten<=10;seiten++) for(const art of ['neubau','relaunch']) for(const tracking of [false,true]) cases.push({seiten,art,tracking,texte:1});
  const quotes=canonicalQuotes(cases);let index=0;
  for (let seiten=1;seiten<=10;seiten++) {
    await page.locator('.groessen [data-seiten="1"]').click();
    for(let n=1;n<seiten;n++) await page.locator('#plus').click();
    for(const art of ['neubau','relaunch']) for(const tracking of [false,true]) {
      await page.locator(`.art [data-art="${art}"]`).click();
      await page.locator('#tracking').setChecked(tracking);
      const result=quotes[index++], price=result.price;
      await expect(page.locator('#gesamt')).toHaveText(price.toLocaleString('de-DE')+' €');
      await expect(page.locator('#abschluss-preis')).toHaveText(euro(price));
      await expect(page.locator('#abschluss-umfang')).toHaveText(seiten+(seiten===1?' Seite':' Seiten')+' · '+(art==='relaunch'?'Relaunch':'Neubau'));
      await expect(page.locator('#bauzeit')).toHaveText(result.days+' Werktage');
      await expect(page.locator('#hero-bauzeit')).toHaveText('Ihre Auswahl: '+result.days+' Werktage geplant.');
      const hrefs=await page.locator('[data-website-cta]').evaluateAll(els=>els.map(el=>el.href));
      expect(new Set(hrefs).size).toBe(1);
      const url=new URL(hrefs[0]);
      expect(Object.fromEntries(url.searchParams)).toEqual({ type:'project',focus:'website',seiten:String(seiten),art,texte:'1',...(tracking?{tracking:'1'}:{}) });
    }
  }
  await expect(page.locator('#plus')).toBeDisabled();
  await expect(page.locator('.anfrage-website .gruppe')).toHaveCount(6);
  expect(await page.locator('.gruppe').evaluateAll(els=>els.every(el=>!el.open))).toBe(true);
});
for (const width of [390, 1440]) {
  test(`integrated scope choices preserve extras and keyboard focus at ${width}`, async ({ page }) => {
    test.setTimeout(30000);
    await open(page, width);
    await expect(page.locator('.aw-szenario')).toHaveCount(3);
    await expect(page.locator('.aw-szenario').nth(0)).toContainText('Ihr Angebot im Überblick');
    await expect(page.locator('#beispiele')).toHaveClass('groessen');
    await page.locator('#beispiele').scrollIntoViewIfNeeded();
    await expect(page.locator('#leiste')).toBeHidden();
    await page.screenshot({ path: test.info().outputPath(`website-scenarios-${width}.png`) });
    await page.locator('#texte').uncheck();
    await page.locator('#screendesign').check();
    await page.locator('#design-layouts').selectOption('2');
    for (const option of ['tracking', 'crm', 'dashboard']) await page.locator('#' + option).check();
    await page.locator('.art [data-art="relaunch"]').click();
    for (const pages of [1, 3, 5]) {
      await page.locator(`[data-website-scenario="${pages}"]`).focus();
      await page.keyboard.press('Enter');
      await expect(page.locator(`[data-website-scenario="${pages}"]`)).toBeFocused();
      await expect(page.locator('#seiten')).toHaveText(String(pages));
      for (const option of ['tracking', 'crm', 'dashboard', 'screendesign']) await expect(page.locator('#' + option)).toBeChecked();
      await expect(page.locator('#texte')).not.toBeChecked();
      await expect(page.locator('#design-layouts')).toHaveValue('1');
      await expect(page.locator(`[data-website-scenario="${pages}"]`)).toHaveAttribute('aria-pressed', 'true');
      await expect(page.locator('[data-website-scenario][aria-pressed="true"]')).toHaveCount(1);
      const expected = canonicalQuotes([{ seiten: pages, art: 'relaunch', texte: 0, design: 'neu', design_layouts: 1, tracking: 1, crm: 1, dashboard: 1 }])[0];
      await expect(page.locator('#gesamt')).toHaveText(euro(expected.price));
      const targets = await page.locator('[data-website-cta]').evaluateAll(els => els.map(el => el.href));
      expect(new Set(targets).size).toBe(1);
      const url = new URL(targets[0]);
      expect(Object.fromEntries(url.searchParams)).toEqual({ type: 'project', focus: 'website', seiten: String(pages), art: 'relaunch', texte: '0', tracking: '1', crm: '1', dashboard: '1', design: 'neu', screendesign: '1', design_layouts: '1' });
    }
    await page.locator('#plus').click();
    await expect(page.locator('[data-website-scenario][aria-pressed="true"]')).toHaveCount(0);
    const sectionOrder = await page.locator('#beleg, #unterschied').evaluateAll(els => els.map(el => el.id));
    expect(sectionOrder).toEqual(['beleg', 'unterschied']);
    await expect(page.locator('#durch')).toHaveAttribute('data-modus', 'anfragen');
    await expect(page.locator('#anfrage .direkt a').first()).toHaveAttribute('href', /^mailto:/);
    await expect(page.locator('#anfrage .direkt a').last()).toHaveAttribute('href', /^tel:/);
  });
}
test('no JavaScript: both comparison rows and diagrams, valid static offer', async ({ browser }) => {
  const context=await browser.newContext({ javaScriptEnabled:false });const page=await context.newPage();
  await open(page,360,false,false);
  await expect(page.locator('.v-klassisch')).toBeVisible();await expect(page.locator('.v-anfragen')).toBeVisible();
  await expect(page.locator('.stellen .k')).toHaveCount(7);await expect(page.locator('.stellen .a')).toHaveCount(7);
  expect(await page.locator('.stellen p').evaluateAll(els=>els.every(el=>getComputedStyle(el).display!=='none'))).toBe(true);
  await expect(page.locator('.schalter')).toBeHidden();await expect(page.locator('#leiste')).toBeHidden();
  await expect(page.locator('.aw-szenario')).toHaveCount(3);
  expect(await page.locator('[data-website-scenario]').evaluateAll(els => els.every(el => getComputedStyle(el).display === 'none' || el.getBoundingClientRect().width === 0))).toBe(true);
  const overflow = await page.locator('body *').evaluateAll(els => els.filter(el => {
    const r = el.getBoundingClientRect(); return r.width > 0 && r.right > innerWidth + 1;
  }).map(el => ({ tag: el.tagName, class: el.className, right: el.getBoundingClientRect().right })));
  await page.screenshot({ path: test.info().outputPath('website-no-js.png'), fullPage: true });
  expect(overflow).toEqual([]);
  expect(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth)).toBe(true);
  expect(new URL(await page.locator('#cta-angebot').getAttribute('href')).searchParams.get('seiten')).toBe('3');
  await context.close();
});
test('reduced motion: no transitions, no simulated loading, no browser storage', async ({ page }) => {
  await page.emulateMedia({reducedMotion:'reduce'});await open(page);
  await page.locator('#m-anfragen').click();
  await expect(page.locator('.laden, #ladebalken')).toHaveCount(0);
  expect(await page.locator('.anfrage-website').evaluate(el=>el.getAnimations({subtree:true}).length)).toBe(0);
  await page.locator('#cta-angebot').evaluate(el=>{el.addEventListener('click',e=>e.preventDefault());el.click()});
  expect(await page.evaluate(()=>[localStorage.length,sessionStorage.length,document.cookie])).toEqual([0,0,'']);
});
test('product to contact to real intake: request, CRM and mails', async ({ page }) => {
  await open(page);
  await page.locator('.groessen [data-seiten="5"]').click();await page.locator('.art [data-art="relaunch"]').click();await page.locator('#tracking').check();
  await page.locator('#screendesign').check();await page.locator('#crm').check();await page.locator('#dashboard').check();
  await page.locator('#design-layouts').selectOption('2');
  const search=new URL(await page.locator('#cta-angebot').getAttribute('href')).searchParams.toString();
  const html=execFileSync('php',[path.join(__dirname,'render-contact.php'),'website',search],{encoding:'utf8'});
  await page.setContent('<style>.is-hidden,[hidden]{display:none!important}</style>'+html);
  await expect(page.locator('[name="focus"]')).toHaveValue('website');
  await expect(page.locator('[data-website-scope]')).toContainText('5 Seiten · Relaunch · Tracking dazu · 5.470 € netto · mindestens '+workingDays(14));
  let response;
  await page.route('**/wp-json/nexus/v1/contact-request', async route=>{
    response=JSON.parse(execFileSync('php',[path.join(__dirname,'submit-website-fixture.php')],{input:JSON.stringify(route.request().postDataJSON()),encoding:'utf8'}));
    await route.fulfill({status:response.status,contentType:'application/json',body:JSON.stringify(response.data)});
  });
  await page.evaluate(()=>{window.NexusContactConfig={restEndpoint:'/wp-json/nexus/v1/contact-request'}});
  for(const file of ['nexus-core.js','contact.js']) await page.addScriptTag({path:path.join(theme,'assets/js',file)});
  await page.locator('[name="message"]').fill('Wir bieten Beratung an und möchten im November starten.');
  await page.locator('[data-contact-next]').click();await page.locator('[name="name"]').fill('Fixture Person');
  await page.locator('[name="email"]').fill('fixture@example.test');await page.locator('[name="consent"]').check();
  await page.locator('[data-contact-submit]').click();
  await expect(page.locator('[data-contact-feedback]')).toHaveClass(/is-success/);
  expect(response.status).toBe(201);
  expect(response.meta['1']._nexus_contact_website_pages).toBe(5);
  expect(response.meta['1']._nexus_contact_website_texts).toBe(1);
  expect(response.meta['1']._nexus_contact_website_screendesign).toBe(1);
  expect(response.meta['1']._nexus_contact_website_crm).toBe(1);
  expect(response.meta['1']._nexus_contact_website_design).toBe('neu');
  expect(response.meta['1']._nexus_contact_website_design_layouts).toBe(2);
  expect(response.meta['1']._nexus_contact_website_days).toBe(14);
  expect(response.meta['1']._nexus_contact_website_preparation_days).toBe(5.5);
  expect(response.meta['1']._nexus_contact_website_implementation_days).toBe(8);
  expect(response.meta['1']._nexus_contact_website_duration_open).toBe(1);
  expect(response.meta['1']._nexus_contact_website_dashboard).toBe(1);
  expect(response.meta['1']._nexus_contact_website_price).toBe(5470);
  expect(response.meta['1']._nexus_contact_website_design_price).toBe(940);
  expect(response.mails).toHaveLength(2);
  for(const mail of response.mails) {
    expect(mail.body).toContain('5.470');
    expect(mail.body).toContain('Texte erstellen lassen');
    expect(mail.body).toContain('CRM-Anbindung Standard');
    expect(mail.body).toContain('Daten-Dashboard nach Angebot');
    expect(mail.body).toContain('Dashboard-Aufwand noch nicht in Preis und Zeit enthalten');
  }
});


test('included scope, fixed extensions and explicitly unpriced dashboard', async ({ page }) => {
  await open(page,390);
  expect(await page.locator('.gruppe').evaluateAll(els => els.every(el => !el.open))).toBe(true);
  await page.locator('.gruppe summary').nth(0).focus();await page.keyboard.press('Enter');
  await expect(page.locator('.gruppe').nth(0)).toHaveAttribute('open','');
  await expect(page.locator('.gruppe').nth(0)).toContainText('Texte für jede gewählte Seite');
  await page.locator('#screendesign').check();await page.locator('#crm').check();
  await expect(page.locator('#gesamt')).toHaveText(euro(3750));
  await expect(page.locator('#betrag-design')).toHaveText(euro(690));
  await expect(page.locator('#preis-label')).toHaveText('Ihr Einmalpreis');
  await expect(page.locator('#angebot-hinweis')).toContainText('Standardumfang');
  await page.locator('#dashboard').check();
  await expect(page.locator('#gesamt')).toHaveText(euro(3750));
  await expect(page.locator('#preis-label')).toHaveText('Einmalpreis ohne Dashboard');
  await expect(page.locator('#angebot-hinweis')).toContainText('Zuzüglich Daten-Dashboard nach Angebot');
  await expect(page.locator('#abschluss-preis')).toHaveText(euro(3750));
  await expect(page.locator('#abschluss-preiszusatz')).toContainText('Zuzüglich Daten-Dashboard nach Angebot');
  await page.locator('.aw-price-details summary').click();
  await expect(page.locator('#summary-dashboard')).toBeVisible();
  await page.locator('#texte').uncheck();
  await expect(page.locator('#auswahl-texte')).toHaveText('Eigene Texte einpflegen');
  const url=new URL(await page.locator('#cta-angebot').getAttribute('href'));
  expect(url.searchParams.get('texte')).toBe('0');expect(url.searchParams.get('dashboard')).toBe('1');
  await page.locator('#crm').uncheck();await page.locator('#dashboard').uncheck();await page.locator('#design-basis').check();
  await expect(page.locator('#angebot-hinweis')).toBeHidden();
  await expect(page.locator('#preis-label')).toHaveText('Ihr Einmalpreis');
  await expect(page.locator('#abschluss-preiszusatz')).not.toContainText('Dashboard');
  const cleared=new URL(await page.locator('#cta-angebot').getAttribute('href'));
  expect(cleared.searchParams.has('crm')).toBe(false);expect(cleared.searchParams.has('dashboard')).toBe(false);
});

test('prepared scope, project extras, layout reuse and aggregate rounding', async ({ page }) => {
  await open(page,390);
  await page.locator('.groessen [data-seiten="1"]').click();await page.locator('#texte').uncheck();
  await expect(page.locator('#bauzeit')).toHaveText(workingDays(2));
  await page.locator('#design-vorhanden').check();
  await expect(page.locator('#bauzeit')).toHaveText(workingDays(2));
  await expect(page.locator('#angebot-hinweis')).toContainText('Vorlage');
  await page.locator('#tracking').check();await expect(page.locator('#bauzeit')).toHaveText(workingDays(3));
  await page.locator('#screendesign').check();await expect(page.locator('#bauzeit')).toHaveText(workingDays(5));
  await expect(page.locator('#design-layouts option')).toHaveCount(1);
  await page.locator('#crm').check();await expect(page.locator('#bauzeit')).toHaveText(workingDays(8));
  await page.locator('#dashboard').check();await expect(page.locator('#bauzeit')).toHaveText('Mindestens '+workingDays(8));
  await expect(page.locator('#zeit-dashboard')).toContainText('Zusätzlich nach Angebot');
  await page.locator('#dashboard').uncheck();await page.locator('#crm').uncheck();
  await page.locator('.groessen [data-seiten="5"]').click();await expect(page.locator('#design-layouts')).toHaveValue('1');
  await page.locator('#design-layouts').selectOption('1');await expect(page.locator('#bauzeit')).toHaveText(workingDays(5));
  await expect(page.locator('#tage-tracking')).toHaveText(workingDays(1));
  await page.locator('.groessen [data-seiten="3"]').click();await expect(page.locator('#design-layouts')).toHaveValue('1');
  await page.locator('#design-layouts').selectOption('3');await expect(page.locator('#bauzeit')).toHaveText(workingDays(6));
  await page.locator('.groessen [data-seiten="1"]').click();await expect(page.locator('#design-layouts')).toHaveValue('1');
  await page.locator('#plus').click();await page.locator('#design-layouts').selectOption('2');
  await page.locator('#tracking').uncheck();await page.locator('#texte').check();
  await expect(page.locator('#tage-texts')).toHaveText(workingDays(1.5));
  await expect(page.locator('#tage-design')).toHaveText(workingDays(2.5));
  await expect(page.locator('#bauzeit')).toHaveText(workingDays(6));
  await page.screenshot({path:test.info().outputPath('website-smart-mobile.png'),fullPage:true});
});

test('complete factor combinations agree with the PHP canon', async ({ page }) => {
  test.setTimeout(60000);
  await open(page);
  const cases=[];
  for(const texte of [0,1]) for(const design of ['basis','vorhanden','neu']) for(const dashboard of [0,1]) for(const crm of [0,1]) for(const tracking of [0,1]) for(const art of ['neubau','relaunch']) cases.push({seiten:3,texte,design,crm,tracking,art,dashboard,design_layouts:2});
  const quotes=canonicalQuotes(cases);
  for(let i=0;i<cases.length;i++) {
    const state=cases[i], expected=quotes[i];
    await page.locator('#texte').setChecked(Boolean(state.texte));
    await page.locator(state.design==='neu'?'#screendesign':'#design-'+state.design).check();
    if(state.design==='neu') await page.locator('#design-layouts').selectOption('2');
    await page.locator('#crm').setChecked(Boolean(state.crm));await page.locator('#dashboard').setChecked(Boolean(state.dashboard));await page.locator('#tracking').setChecked(Boolean(state.tracking));
    await page.locator('.art [data-art="'+state.art+'"]').click();
    await expect(page.locator('#bauzeit')).toHaveText((state.dashboard?'Mindestens ':'')+expected.days+' Werktage');
    await expect(page.locator('#gesamt')).toHaveText(euro(expected.price));
    const url=new URL(await page.locator('#cta-angebot').getAttribute('href'));
    const scope=execFileSync('php',[path.join(__dirname,'render-contact.php'),'website',url.searchParams.toString()],{encoding:'utf8'});
    expect(scope).toContain(expected.days+' Werktage geplant');
  }
  await page.screenshot({path:test.info().outputPath('website-smart-desktop.png'),fullPage:true});
});

for (const width of [320, 390, 768, 1440]) {
  test(`expanded product and all selected extras fit at ${width}`, async ({ page }) => {
    await open(page, width);
    for (const option of ['tracking', 'screendesign', 'crm', 'dashboard']) await page.locator('#' + option).check();
    await page.locator('.gruppe').evaluateAll(els => els.forEach(el => { el.open = true; }));
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    const overflow = await page.locator('.anfrage-website *').evaluateAll(els => els.filter(el => {
      const r = el.getBoundingClientRect(); return r.width > 1 && (r.right > innerWidth + 1 || r.left < -1);
    }).map(el => el.className));
    expect(overflow).toEqual([]);
  });
}

for(const [width,height] of [[1366,768],[1440,900]]) {
  test(`selection stays readable and summary remains available at ${width}`, async ({page})=>{
    await open(page,width);await page.setViewportSize({width,height});
    for(const id of ['screendesign','tracking','crm','dashboard']) await page.locator('#'+id).check();
    for(const id of ['plus','texte','design-basis','design-vorhanden','screendesign','design-layouts','tracking','crm','dashboard']) {
      await page.locator('#'+id).focus();
      await page.locator('#'+id).scrollIntoViewIfNeeded();
      const rect=await page.locator('#'+id).boundingBox();
      expect(rect.y, id+' visible below header').toBeGreaterThanOrEqual(52);
      expect(rect.y+rect.height,id+' visible above bottom').toBeLessThanOrEqual(height);
      const review=await page.locator('#cta-angebot').boundingBox();
      expect(review.y, 'summary CTA visible').toBeGreaterThanOrEqual(52);
      expect(review.y+review.height,'summary CTA fits').toBeLessThanOrEqual(height);
    }
    await expect(page.locator('#leiste')).toBeHidden();
    await page.screenshot({path:test.info().outputPath('website-configurator-'+width+'.png')});
  });
}

test('default hierarchy, explicit layout price and interruptible motion', async ({page})=>{
  await open(page,1440,false);
  await expect(page.locator('.aw-extras-panel')).not.toHaveAttribute('open','');
  await expect(page.locator('#tracking')).toBeHidden();
  await expect(page.locator('.aw-config-column')).toHaveCount(1);
  await expect(page.locator('[data-seiten]')).toHaveCount(3);
  await page.locator('#screendesign').check();
  await expect(page.locator('#design-layouts')).toHaveValue('1');
  await expect(page.locator('#gesamt')).toHaveText(euro(2760));
  await expect(page.locator('#design-option-preis')).toHaveText('+'+euro(690));
  await page.locator('#design-layouts').selectOption('2');
  await expect(page.locator('#design-option-preis')).toHaveText('+'+euro(940));
  await page.locator('#design-basis').check();await page.locator('#screendesign').check();
  await expect(page.locator('#design-layouts')).toHaveValue('2');
  await page.locator('.aw-extras-panel summary').focus();await page.keyboard.press('Enter');
  await expect(page.locator('#tracking')).toBeVisible();await expect(page.locator('.aw-extras-panel summary')).toBeFocused();
  await page.locator('#tracking').focus();await page.keyboard.press('Space');
  await expect(page.locator('#tracking')).toBeFocused();await expect(page.locator('#extras-count')).toHaveText('1 gewählt');
  await page.locator('.aw-extras-panel summary').click();
  await expect(page.locator('#tracking')).toBeHidden();
  const quote=canonicalQuotes([{seiten:3,art:'neubau',texte:1,design:'neu',design_layouts:2,tracking:1,crm:0,dashboard:0}])[0];
  await expect(page.locator('#gesamt')).toHaveText(euro(quote.price));
  // Repeated changes keep the latest value and cancel previous selection animations.
  await page.locator('#plus').evaluate(el=>{el.click();el.click();el.click();});
  await expect(page.locator('#seiten')).toHaveText('6');
  expect(await page.locator('#gesamt').evaluate(el=>el.getAnimations().length)).toBeLessThanOrEqual(1);
  await page.emulateMedia({reducedMotion:'reduce'});
  await expect.poll(()=>page.locator('.anfrage-website').evaluate(el=>el.getAnimations({subtree:true}).length)).toBe(0);
});

for (const width of [320, 390, 768, 1440]) {
  test(`200 percent text: selected product and shared shell reflow at ${width}`, async ({ page }) => {
    await open(page, width);
    for (const id of ['screendesign', 'tracking', 'crm', 'dashboard']) await page.locator('#' + id).check();
    await page.locator('.aw-price-details summary').click();
    await page.locator('.gruppe').evaluateAll(els => els.forEach(el => { el.open = true; }));
    await page.evaluate(() => { document.documentElement.style.fontSize = '200%'; });
    await expect.poll(() => page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    await expect.poll(() => page.locator('#leiste').evaluate(el => el.hidden || el.getBoundingClientRect().height <= innerHeight / 3)).toBe(true);
    await expect(page.locator('#gesamt')).toHaveText(euro(4640));
    await expect(page.locator('#preis-label')).toHaveText('Einmalpreis ohne Dashboard');
  });
}

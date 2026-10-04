const { test, expect } = require('@playwright/test');
const path = require('node:path');
const fs = require('node:fs');
const { execFileSync } = require('node:child_process');
const theme = path.resolve(__dirname, '../../blocksy-child');
const render = () => execFileSync('php', [path.join(__dirname, 'render-website.php')], { encoding: 'utf8' });
const euro = n => n.toLocaleString('de-DE') + ' €';
const workingDays = n => n + (n === 1 ? ' Werktag' : ' Werktage');
const canonicalQuotes = cases => JSON.parse(execFileSync('php', [path.join(__dirname, 'quote-website-fixture.php')], { input:JSON.stringify(cases), encoding:'utf8' }));
const types = { '.css':'text/css', '.js':'text/javascript', '.woff2':'font/woff2', '.webp':'image/webp' };
async function open(page, width = 1440) {
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
  await page.evaluate(() => document.fonts.ready);
}
for (const width of [360, 768, 1440]) {
  test(`request website ${width}: layout, keyboard controls and sticky visibility`, async ({ page }) => {
    const errors=[]; page.on('pageerror', e=>errors.push(e.message));
    await open(page,width);
    await expect(page).toHaveTitle('WordPress-Website erstellen lassen ab ' + euro(1490) + ' | Haşim Üner');
    expect(await page.locator('h2').evaluateAll(els=>els.every(el=>el.id))).toBe(true);
    expect(await page.evaluate(()=>document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    const overflow = await page.locator('.anfrage-website *').evaluateAll(els=>els.filter(el=>{
      const r=el.getBoundingClientRect(); return r.width>0 && r.right>innerWidth+1;
    }).map(el=>el.className));
    expect(overflow).toEqual([]);
    const priceFits = await page.locator('.formel .betrag small').evaluate(el => el.getBoundingClientRect().right <= el.closest('.formel').getBoundingClientRect().right);
    expect(priceFits).toBe(true);
    await page.locator('.bild img').scrollIntoViewIfNeeded();
    await expect.poll(() => page.locator('.bild img').evaluate(el => el.complete && el.naturalWidth > 0)).toBe(true);
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
    await expect(page.locator('#bauzeit')).toHaveText(workingDays(6));
    await expect(page.locator('#leiste')).toBeVisible();
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
test('no JavaScript: both comparison rows and diagrams, valid static offer', async ({ browser }) => {
  const context=await browser.newContext({ javaScriptEnabled:false });const page=await context.newPage();
  await open(page,360);
  await expect(page.locator('.v-klassisch')).toBeVisible();await expect(page.locator('.v-anfragen')).toBeVisible();
  await expect(page.locator('.stellen .k')).toHaveCount(7);await expect(page.locator('.stellen .a')).toHaveCount(7);
  expect(await page.locator('.stellen p').evaluateAll(els=>els.every(el=>getComputedStyle(el).display!=='none'))).toBe(true);
  await expect(page.locator('.schalter')).toBeHidden();await expect(page.locator('#leiste')).toBeHidden();
  const overflow = await page.locator('body *').evaluateAll(els => els.filter(el => {
    const r = el.getBoundingClientRect(); return r.width > 0 && r.right > innerWidth + 1;
  }).map(el => ({ tag: el.tagName, class: el.className, right: el.getBoundingClientRect().right })));
  await page.screenshot({ path: test.info().outputPath('website-no-js.png'), fullPage: true });
  expect(overflow).toEqual([]);
  expect(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth)).toBe(true);
  expect(new URL(await page.locator('#cta-angebot').getAttribute('href')).searchParams.get('seiten')).toBe('3');
  await context.close();
});
test('reduced motion: no transitions or load-bar animation, no browser storage', async ({ page }) => {
  await page.emulateMedia({reducedMotion:'reduce'});await open(page);
  await page.locator('#m-anfragen').click();
  expect(await page.locator('.laden').isVisible()).toBe(false);
  expect(await page.locator('.anfrage-website').evaluate(el=>el.getAnimations({subtree:true}).length)).toBe(0);
  await page.locator('#cta-angebot').evaluate(el=>{el.addEventListener('click',e=>e.preventDefault());el.click()});
  expect(await page.evaluate(()=>[localStorage.length,sessionStorage.length,document.cookie])).toEqual([0,0,'']);
});
test('product to contact to real intake: request, CRM and mails', async ({ page }) => {
  await open(page);
  await page.locator('.groessen [data-seiten="5"]').click();await page.locator('.art [data-art="relaunch"]').click();await page.locator('#tracking').check();
  await page.locator('#screendesign').check();await page.locator('#crm').check();
  await page.locator('#design-layouts').selectOption('2');
  const search=new URL(await page.locator('#cta-angebot').getAttribute('href')).searchParams.toString();
  const html=execFileSync('php',[path.join(__dirname,'render-contact.php'),'website',search],{encoding:'utf8'});
  await page.setContent('<style>.is-hidden,[hidden]{display:none!important}</style>'+html);
  await expect(page.locator('[name="focus"]')).toHaveValue('website');
  await expect(page.locator('[data-website-scope]')).toContainText('5 Seiten · Relaunch · Tracking dazu · 3.540 € netto · mindestens '+workingDays(16));
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
  expect(response.meta['1']._nexus_contact_website_days).toBe(16);
  expect(response.meta['1']._nexus_contact_website_preparation_days).toBe(6);
  expect(response.meta['1']._nexus_contact_website_implementation_days).toBe(10);
  expect(response.meta['1']._nexus_contact_website_duration_open).toBe(1);
  expect(response.mails).toHaveLength(2);
  for(const mail of response.mails) {
    expect(mail.body).toContain('3.540');
    expect(mail.body).toContain('Texte erstellen lassen');
    expect(mail.body).toContain('Screendesign und CRM-Anbindung');
    expect(mail.body).toContain('separatem Angebot');
    expect(mail.body).toContain('CRM-Aufwand noch nicht in der Zeit enthalten');
  }
});


test('expandable product: included scope, extras and honest partial pricing', async ({ page }) => {
  await open(page, 390);
  expect(await page.locator('.gruppe').evaluateAll(els => els.every(el => !el.open))).toBe(true);
  await page.locator('.gruppe summary').nth(0).focus(); await page.keyboard.press('Enter');
  await expect(page.locator('.gruppe').nth(0)).toHaveAttribute('open', '');
  await expect(page.locator('.gruppe').nth(0)).toContainText('Texte für jede gewählte Seite');
  const price = await page.locator('#gesamt').textContent();
  await page.locator('#screendesign').check(); await page.locator('#crm').check();
  await expect(page.locator('#gesamt')).toHaveText(price);
  await expect(page.locator('#preis-label')).toHaveText('Festpreis ohne individuelle Extras');
  await expect(page.locator('#angebot-hinweis')).toContainText('Zuzüglich Screendesign und CRM-Anbindung');
  await expect(page.locator('#summary-screendesign')).toBeVisible();
  await expect(page.locator('#summary-crm')).toBeVisible();
  await page.locator('#texte').uncheck();
  await expect(page.locator('#auswahl-texte')).toHaveText('Freigegebene Texte einpflegen');
  const url = new URL(await page.locator('#cta-angebot').getAttribute('href'));
  expect(url.searchParams.get('texte')).toBe('0');
  expect(url.searchParams.get('screendesign')).toBe('1');
  expect(url.searchParams.get('crm')).toBe('1');
  await page.locator('#crm').uncheck(); await page.locator('#design-basis').check();
  await expect(page.locator('#angebot-hinweis')).toBeHidden();
  await expect(page.locator('#preis-label')).toHaveText('Ihr Festpreis');
  expect(new URL(await page.locator('#cta-angebot').getAttribute('href')).searchParams.has('crm')).toBe(false);
});

test('working-day factors: supplied content, reusable designs, tracking and open CRM', async ({ page }) => {
  await open(page,390);
  await page.locator('.groessen [data-seiten="1"]').click();
  await page.locator('#texte').uncheck();
  await expect(page.locator('#bauzeit')).toHaveText(workingDays(3));
  await page.locator('#design-vorhanden').check();
  await expect(page.locator('#bauzeit')).toHaveText(workingDays(3));
  await expect(page.locator('#preis-label')).toHaveText('Basispreis vor Vorlagenprüfung');
  await expect(page.locator('#angebot-hinweis')).toContainText('Vorlagen');
  expect(new URL(await page.locator('#cta-angebot').getAttribute('href')).searchParams.get('design')).toBe('vorhanden');
  await page.locator('#tracking').check();
  await expect(page.locator('#bauzeit')).toHaveText(workingDays(4));
  await page.locator('#screendesign').check();
  await expect(page.locator('#bauzeit')).toHaveText(workingDays(6));
  await expect(page.locator('#design-layouts option')).toHaveCount(1);
  await page.locator('#crm').check();
  await expect(page.locator('#bauzeit')).toHaveText('Mindestens '+workingDays(6));
  await expect(page.locator('#zeit-hinweis')).toContainText('CRM-Aufwand');
  await expect(page.locator('#zeit-crm')).toContainText('Noch offen');
  await page.locator('#crm').uncheck();
  await page.locator('.groessen [data-seiten="5"]').click();
  await expect(page.locator('#design-layouts')).toHaveValue('5');
  await page.locator('#design-layouts').selectOption('1');
  await expect(page.locator('#bauzeit')).toHaveText(workingDays(10));
  await expect(page.locator('#tage-tracking')).toHaveText(workingDays(1));
  await page.locator('.groessen [data-seiten="3"]').click();
  await expect(page.locator('#design-layouts')).toHaveValue('1');
  await expect(page.locator('#bauzeit')).toHaveText(workingDays(8));
  await page.locator('#design-layouts').selectOption('3');
  await expect(page.locator('#bauzeit')).toHaveText(workingDays(10));
  await page.locator('.groessen [data-seiten="1"]').click();
  await expect(page.locator('#design-layouts')).toHaveValue('1');
  await expect(page.locator('#bauzeit')).toHaveText(workingDays(6));
  await page.screenshot({path:test.info().outputPath('website-smart-mobile.png'),fullPage:true});
});

test('complete factor combinations agree with the PHP canon', async ({ page }) => {
  test.setTimeout(60000);
  await open(page);
  const cases=[];
  for(const texte of [0,1]) for(const design of ['basis','vorhanden','neu']) for(const crm of [0,1]) for(const tracking of [0,1]) for(const art of ['neubau','relaunch']) cases.push({seiten:3,texte,design,crm,tracking,art,design_layouts:2});
  const quotes=canonicalQuotes(cases);
  for(let i=0;i<cases.length;i++) {
    const state=cases[i], expected=quotes[i];
    await page.locator('#texte').setChecked(Boolean(state.texte));
    await page.locator(state.design==='neu'?'#screendesign':'#design-'+state.design).check();
    if(state.design==='neu') await page.locator('#design-layouts').selectOption('2');
    await page.locator('#crm').setChecked(Boolean(state.crm));await page.locator('#tracking').setChecked(Boolean(state.tracking));
    await page.locator('.art [data-art="'+state.art+'"]').click();
    await expect(page.locator('#bauzeit')).toHaveText((state.crm?'Mindestens ':'')+expected.days+' Werktage');
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
    for (const option of ['tracking', 'screendesign', 'crm']) await page.locator('#' + option).check();
    await page.locator('.gruppe').evaluateAll(els => els.forEach(el => { el.open = true; }));
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    const overflow = await page.locator('.anfrage-website *').evaluateAll(els => els.filter(el => {
      const r = el.getBoundingClientRect(); return r.width > 1 && (r.right > innerWidth + 1 || r.left < -1);
    }).map(el => el.className));
    expect(overflow).toEqual([]);
  });
}

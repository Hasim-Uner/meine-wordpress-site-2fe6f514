const { test, expect } = require('@playwright/test');
const path = require('node:path');
const fs = require('node:fs');
const { execFileSync } = require('node:child_process');
const theme = path.resolve(__dirname, '../../blocksy-child');
const render = () => execFileSync('php', [path.join(__dirname, 'render-website.php')], { encoding: 'utf8' });
const euro = n => n.toLocaleString('de-DE') + ' €';
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
    await expect(page.locator('#bauzeit')).toHaveText('2 Wochen');
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
  for (let seiten=1;seiten<=10;seiten++) {
    await page.locator('.groessen [data-seiten="1"]').click();
    for(let n=1;n<seiten;n++) await page.locator('#plus').click();
    for(const art of ['neubau','relaunch']) for(const tracking of [false,true]) {
      await page.locator(`.art [data-art="${art}"]`).click();
      await page.locator('#tracking').setChecked(tracking);
      const price=1490+(seiten-1)*290+(tracking?890:0), weeks=(seiten<=2?2:seiten<=5?3:4)+(art==='relaunch'?1:0);
      await expect(page.locator('#gesamt')).toHaveText(price.toLocaleString('de-DE')+' €');
      await expect(page.locator('#bauzeit')).toHaveText(weeks+' Wochen');
      await expect(page.locator('#hero-bauzeit')).toHaveText('Ihr Umfang: '+weeks+' Wochen.');
      const hrefs=await page.locator('[data-website-cta]').evaluateAll(els=>els.map(el=>el.href));
      expect(new Set(hrefs).size).toBe(1);
      const url=new URL(hrefs[0]);
      expect(Object.fromEntries(url.searchParams)).toEqual({ type:'project',focus:'website',seiten:String(seiten),art,...(tracking?{tracking:'1'}:{}) });
    }
  }
  await expect(page.locator('#plus')).toBeDisabled();
  const points=await page.locator('.anfrage-website .gruppe li').count();
  await expect(page.locator('#anzahl')).toHaveText(String(points));
  const events=await page.evaluate(()=>window._paq);
  expect(events.filter(e=>e[0]==='trackEvent' && e[2]==='rechner_change').length).toBeGreaterThan(40);
});
test('no JavaScript: both comparison rows and diagrams, valid static offer', async ({ browser }) => {
  const context=await browser.newContext({ javaScriptEnabled:false });const page=await context.newPage();
  await open(page,360);
  await expect(page.locator('.v-klassisch')).toBeVisible();await expect(page.locator('.v-anfragen')).toBeVisible();
  await expect(page.locator('.stellen .k')).toHaveCount(7);await expect(page.locator('.stellen .a')).toHaveCount(7);
  expect(await page.locator('.stellen p').evaluateAll(els=>els.every(el=>getComputedStyle(el).display!=='none'))).toBe(true);
  await expect(page.locator('.schalter')).toBeHidden();await expect(page.locator('#leiste')).toBeHidden();
  expect(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth)).toBe(true);
  expect(new URL(await page.locator('#cta-angebot').getAttribute('href')).searchParams.get('seiten')).toBe('3');
  await context.close();
});
test('reduced motion: no transitions or load-bar animation; event dimensions only', async ({ page }) => {
  await page.emulateMedia({reducedMotion:'reduce'});await open(page);
  await page.locator('#m-anfragen').click();
  expect(await page.locator('.laden').isVisible()).toBe(false);
  expect(await page.locator('.anfrage-website').evaluate(el=>el.getAnimations({subtree:true}).length)).toBe(0);
  await page.locator('#cta-angebot').evaluate(el=>{el.addEventListener('click',e=>e.preventDefault());el.click()});
  const events=await page.evaluate(()=>window._paq);
  expect(events[0]).toEqual(['disableCookies']);
  expect(events.find(e=>e[2]==='toggle_durchleuchtung')).toEqual(['trackEvent','anfrage_website','toggle_durchleuchtung','{"modus":"anfragen"}']);
  const cta=events.find(e=>e[2]==='cta_click');expect(JSON.parse(cta[3])).toEqual({position:'angebot',seiten:3,art:'neubau',tracking:0});
  expect(await page.evaluate(()=>[localStorage.length,sessionStorage.length,document.cookie])).toEqual([0,0,'']);
});
test('product to contact to real intake: request, CRM, mails and success event', async ({ page }) => {
  await open(page);
  await page.locator('.groessen [data-seiten="5"]').click();await page.locator('.art [data-art="relaunch"]').click();await page.locator('#tracking').check();
  const search=new URL(await page.locator('#cta-angebot').getAttribute('href')).searchParams.toString();
  const html=execFileSync('php',[path.join(__dirname,'render-contact.php'),'website',search],{encoding:'utf8'});
  await page.setContent('<style>.is-hidden,[hidden]{display:none!important}</style>'+html);
  await expect(page.locator('[name="focus"]')).toHaveValue('website');
  await expect(page.locator('[data-website-scope]')).toContainText('5 Seiten · Relaunch · Tracking dazu · 3.540 € netto · 4 Wochen');
  let response;
  await page.route('**/wp-json/nexus/v1/contact-request', async route=>{
    response=JSON.parse(execFileSync('php',[path.join(__dirname,'submit-website-fixture.php')],{input:JSON.stringify(route.request().postDataJSON()),encoding:'utf8'}));
    await route.fulfill({status:response.status,contentType:'application/json',body:JSON.stringify(response.data)});
  });
  await page.evaluate(()=>{window.NexusContactConfig={restEndpoint:'/wp-json/nexus/v1/contact-request'};window._paq=[]});
  for(const file of ['nexus-core.js','website-product-events.js','contact.js']) await page.addScriptTag({path:path.join(theme,'assets/js',file)});
  await page.locator('[name="message"]').fill('Wir bieten Beratung an und möchten im November starten.');
  await page.locator('[data-contact-next]').click();await page.locator('[name="name"]').fill('Fixture Person');
  await page.locator('[name="email"]').fill('fixture@example.test');await page.locator('[name="consent"]').check();
  await page.locator('[data-contact-submit]').click();
  await expect(page.locator('[data-contact-feedback]')).toHaveClass(/is-success/);
  expect(response.status).toBe(201);
  expect(response.meta['1']._nexus_contact_website_pages).toBe(5);
  expect(response.mails).toHaveLength(2);
  for(const mail of response.mails) expect(mail.body).toContain('3.540');
  const events=await page.evaluate(()=>window._paq);
  expect(events.filter(e=>e[2]==='form_submit')).toHaveLength(1);
  expect(JSON.parse(events.find(e=>e[2]==='form_submit')[3])).toEqual({seiten:5,art:'relaunch',tracking:1});
  expect(JSON.stringify(events)).not.toContain('fixture@example.test');
});

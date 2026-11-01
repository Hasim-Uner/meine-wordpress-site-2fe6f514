const { test, expect } = require('@playwright/test');
const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');
const theme = path.resolve(__dirname, '../../blocksy-child');
const sheets = ['system', 'single', 'single-editorial', 'editorial-bausteine', 'wartung-rechner', 'article-reader-body', 'article-reader-toc', 'single-reader-unified'].map(s => `assets/css/${s}.css`).join(',');
async function open(page, slug, width) {
  await page.setViewportSize({ width, height: 1000 });
  const html = execFileSync('php', [path.join(__dirname, 'render-article.php'), slug, sheets], { encoding: 'utf8' });
  expect(html).not.toContain('Fatal error');
  await page.route('https://hasimuener.de/**', route => {
    const url = new URL(route.request().url());
    if (url.pathname.startsWith('/__article_fixture__/')) return route.fulfill({path:path.join(__dirname,'fixtures',path.basename(url.pathname)),contentType:'image/png'});
    const prefix = '/wp-content/themes/blocksy-child/';
    if (url.pathname.startsWith(prefix)) {
      const file = path.join(theme, url.pathname.slice(prefix.length));
      const types = { '.css': 'text/css', '.js': 'text/javascript', '.woff2': 'font/woff2', '.png': 'image/png', '.svg': 'image/svg+xml' };
      return fs.existsSync(file) ? route.fulfill({ path: file, contentType: types[path.extname(file)] }) : route.fulfill({ status: 404 });
    }
    return route.request().isNavigationRequest() ? route.fulfill({ body: html, contentType: 'text/html' }) : route.fulfill({ status: 404 });
  });
  await page.goto(`https://hasimuener.de/${slug}/`);
  await page.evaluate(() => document.fonts.ready);
}
for (const slug of ['wordpress-wartung-kosten', 'website-relaunch']) {
  for (const width of [390, 1440]) {
    test(`reader ${slug} ${width}: image, chapter navigation and content modules`, async ({ page }) => {
      const errors = [];
      page.on('pageerror', e => errors.push(e.message));
      await open(page, slug, width);
      const column=await page.locator('#article-content').boundingBox();
      expect(column.x).toBeGreaterThanOrEqual(0);
      expect(column.x+column.width).toBeLessThanOrEqual(width);
      const cover = page.locator('.nexus-hero-image img');
      await expect.poll(() => cover.evaluate(el => el.naturalWidth)).toBe(2400);
      await expect(cover).toHaveCSS('object-fit', 'contain');
      await expect(cover).toHaveAttribute('sizes', /1100px$/);
      expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
      const titleSize = await page.locator('h1').evaluate(el => parseFloat(getComputedStyle(el).fontSize));
      expect(titleSize).toBeLessThanOrEqual(width < 721 ? 42 : 80);
      if (width < 721) await expect(page.locator('#article-content')).toHaveCSS('font-size', '18px');
      await page.screenshot({ path: test.info().outputPath(`${slug}-${width}-hero.png`) });
      await cover.scrollIntoViewIfNeeded();
      await expect(page.locator('.nexus-article-cover')).toHaveCSS('opacity', '1');
      await page.screenshot({ path: test.info().outputPath(`${slug}-${width}-cover.png`) });
      const shell = page.locator('.hu-toc');
      const chapter = shell.locator('.hu-toc__link').first();
      if (width < 981) {
        const toggle = shell.locator('.hu-toc__leiste');
        await toggle.press('Enter');
        await expect(toggle).toHaveAttribute('aria-expanded', 'true');
        expect(await chapter.evaluate(el => el.getBoundingClientRect().height)).toBeGreaterThanOrEqual(44);
        await chapter.press('Escape');
        await expect(toggle).toHaveAttribute('aria-expanded', 'false');
        await expect(toggle).toBeFocused();
        await toggle.press('Enter');
      }
      await page.emulateMedia({ reducedMotion: 'reduce' });
      // A changed preference must affect the next interaction, not just page load.
      await page.evaluate(() => {
        const original = Element.prototype.scrollIntoView;
        Element.prototype.scrollIntoView = function(options) { window.readerScrollBehavior = options.behavior; return original.call(this, options); };
      });
      await chapter.press('Enter');
      await expect(page.locator('#article-content h2').first()).toBeFocused();
      expect(await page.evaluate(() => window.readerScrollBehavior)).toBe('instant');
      const headingTop = await page.locator('#article-content h2').first().evaluate(el => el.getBoundingClientRect().top);
      expect(headingTop).toBeGreaterThanOrEqual(width < 981 ? 98 : 52);
      if (width >= 981) {
        await expect(shell.locator('.hu-toc__kapitel').first().locator('.hu-toc__unter-huelle')).not.toHaveAttribute('hidden');
        expect(await shell.locator('.hu-toc__kapitel:not(.is-aktiv) .hu-toc__unter-huelle').evaluateAll(els => els.every(el => el.hidden))).toBe(true);
      }
      expect(await shell.locator('.hu-toc__link').first().evaluate(el => getComputedStyle(el).transitionDuration)).toBe('0s');
      await page.screenshot({ path: test.info().outputPath(`${slug}-${width}-reader.png`) });
      if (slug === 'wordpress-wartung-kosten') {
        const slider = page.locator('[data-wr-feld="anfragen"]');
        const before = await page.locator('[data-wr-out="verlust"]').textContent();
        await slider.press('ArrowRight');
        await expect(page.locator('[data-wr-out="verlust"]')).not.toHaveText(before);
        const contrast = await page.locator('.hu-wartung-rechner__ergebnis').evaluate(el => {
          const luminance = color => {
            const rgb = color.match(/[\d.]+/g).slice(0, 3).map(v => Number(v) / 255).map(v => v <= .04045 ? v / 12.92 : ((v + .055) / 1.055) ** 2.4);
            return rgb[0] * .2126 + rgb[1] * .7152 + rgb[2] * .0722;
          };
          const background = luminance(getComputedStyle(el).backgroundColor);
          return [...el.querySelectorAll('p')].map(p => (luminance(getComputedStyle(p).color) + .05) / (background + .05));
        });
        expect(Math.min(...contrast)).toBeGreaterThanOrEqual(4.5);
        await page.locator('[data-hu-wartung-rechner]').screenshot({ path: test.info().outputPath(`wartung-${width}-calculator.png`) });
      } else {
        await expect(page.locator('.hu-abb')).not.toHaveCount(0);
      }
      // Read the whole real seed, including its individual diagrams/tables.
      for (const heading of await page.locator('#article-content h2').all()) {
        await heading.scrollIntoViewIfNeeded();
        expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
      }
      await page.screenshot({ path: test.info().outputPath(`${slug}-${width}-full.png`), fullPage: true });
      if (width < 721) await expect(page.locator('.nexus-back-to-top')).toBeHidden();
      expect(errors).toEqual([]);
      expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    });
  }
}
test('reader: without JavaScript the actual article, cover and calculator remain readable', async ({ browser }) => {
  const context = await browser.newContext({ javaScriptEnabled: false });
  const page = await context.newPage();
  await open(page, 'wordpress-wartung-kosten', 390);
  await expect(page.locator('#article-content h2').first()).toBeVisible();
  await expect(page.locator('.nexus-hero-image img')).toBeVisible();
  await expect(page.locator('[data-wr-out="verlust"]')).not.toBeEmpty();
  expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
  await context.close();
});

// Next two mandatory articles: exercise the previously unstyled real modules,
// not a hand-built copy of the component markup.
for (const slug of ['wordpress-projekte-auslagern', 'b2b-landingpage-optimieren']) {
  for (const width of [320, 390, 768, 1440]) {
    test(`next reader ${slug} ${width}: complete process and responsive content`, async ({ page }) => {
      await open(page, slug, width);
      const column=await page.locator('#article-content').boundingBox();
      expect(column.x).toBeGreaterThanOrEqual(0);
      expect(column.x+column.width).toBeLessThanOrEqual(width);
      const cover = page.locator('.nexus-hero-image img');
      await expect.poll(() => cover.evaluate(el => el.naturalWidth)).toBe(slug.startsWith('wordpress-') ? 1672 : 1200);
      await expect(cover).toHaveCSS('object-fit', 'contain');
      await page.screenshot({path:test.info().outputPath(`${slug}-${width}-hero.png`)});
      if (slug === 'wordpress-projekte-auslagern') {
        const steps = page.locator('.agency-delivery-flow__track > li');
        await expect(steps).toHaveCount(9);
        for (const step of await steps.all()) {
          const boxes = await step.evaluate(el => [...el.children].map(child => {
            const r=child.getBoundingClientRect(); return {top:r.top,bottom:r.bottom,width:r.width};
          }));
          expect(boxes[1].top - boxes[0].bottom).toBeGreaterThanOrEqual(8);
          expect(boxes[2].top - boxes[1].bottom).toBeGreaterThanOrEqual(8);
        }
        const columns = await steps.evaluateAll(els => new Set(els.map(el => Math.round(el.getBoundingClientRect().left))).size);
        expect(columns).toBe(width < 720 ? 1 : 3);
        await page.locator('.agency-delivery-flow').scrollIntoViewIfNeeded();
        await page.locator('.agency-delivery-flow').screenshot({path:test.info().outputPath(`agency-${width}-process.png`)});
        await expect(page.locator('.agency-control-model__grid > section')).toHaveCount(3);
        await page.locator('.agency-control-model').screenshot({path:test.info().outputPath(`agency-${width}-roles.png`)});
      } else {
        const summary=page.locator('.nexus-subtitle');
        await expect(summary).toContainText('Angebot, Belege und nächsten Schritt');
        await expect(summary).not.toContainText(/\[…\]|\[\.\.\.\]|Einleitung:/);
        const table=page.locator('.kpi-table').first();
        const bounds=await table.boundingBox();
        expect(bounds.x+bounds.width).toBeLessThanOrEqual(width);
        await expect(table).toHaveAttribute('tabindex','0');
        await expect(table).toHaveAttribute('aria-label',/Tabelle:/);
        await table.evaluate(el=>el.scrollIntoView({block:'center'}));
        await table.screenshot({path:test.info().outputPath(`landing-${width}-table.png`)});
        if(width<720) {
          await table.focus();
          await table.press('ArrowRight');
          await expect.poll(()=>table.evaluate(el=>el.scrollLeft)).toBeGreaterThan(0);
          await table.evaluate(el=>{el.scrollLeft=0;});
        }
        await expect(page.locator('.faq-item').first().locator('summary')).toBeVisible();
        await page.locator('.faq-item').first().locator('summary').press('Enter');
        await expect(page.locator('.faq-item').first()).toHaveAttribute('open','');
      }
      if (width === 390) {
        const toggle=page.locator('.hu-toc__leiste');
        await toggle.press('Enter');
        await expect(toggle).toHaveAttribute('aria-expanded','true');
        await page.emulateMedia({reducedMotion:'reduce'});
        const chapter=page.locator('.hu-toc__link').first();
        await chapter.press('Enter');
        await expect(page.locator('#article-content h2').first()).toBeFocused();
        expect(await chapter.evaluate(el=>getComputedStyle(el).transitionDuration)).toBe('0s');
      }
      for (const heading of await page.locator('#article-content h2').all()) {
        await heading.scrollIntoViewIfNeeded();
        expect(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth)).toBe(true);
      }
      if (width===1440) {
        await page.addStyleTag({content:'html{font-size:200%}'});
        expect(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth)).toBe(true);
        const content=page.locator('#article-content');
        expect(await content.evaluate(el=>[...el.querySelectorAll('figure,li,.agency-risk-matrix')].every(child=>child.getBoundingClientRect().right<=innerWidth))).toBe(true);
        if (slug==='wordpress-projekte-auslagern') {
          const region=page.locator('.agency-risk-matrix');
          await expect(region).toHaveCSS('overflow-x','auto');
          await region.focus();
          await region.press('ArrowRight');
          await expect.poll(()=>region.evaluate(el=>el.scrollLeft)).toBeGreaterThan(0);
        }
      }
    });
  }
}
test('next reader: agency process remains complete without JavaScript',async({browser})=>{
  const context=await browser.newContext({javaScriptEnabled:false});
  const page=await context.newPage();
  await open(page,'wordpress-projekte-auslagern',390);
  await expect(page.locator('.agency-delivery-flow__track > li')).toHaveCount(9);
  await expect(page.locator('.agency-delivery-flow__track > li').last().locator('small')).toBeVisible();
  expect(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth)).toBe(true);
  await context.close();
});

test('next reader: landingpage tables are scrollable without JavaScript',async({browser})=>{
  const context=await browser.newContext({javaScriptEnabled:false});
  const page=await context.newPage();
  await open(page,'b2b-landingpage-optimieren',390);
  const table=page.locator('.kpi-table').first();
  const bounds=await table.boundingBox();
  expect(bounds.x+bounds.width).toBeLessThanOrEqual(390);
  await table.evaluate(el=>{el.scrollLeft=100;});
  expect(await table.evaluate(el=>el.scrollLeft)).toBeGreaterThan(0);
  await expect(page.locator('.faq-item').first().locator('summary')).toBeVisible();
  await page.locator('.faq-item').first().locator('summary').press('Enter');
  await expect(page.locator('.faq-item').first()).toHaveAttribute('open','');
  await context.close();
});

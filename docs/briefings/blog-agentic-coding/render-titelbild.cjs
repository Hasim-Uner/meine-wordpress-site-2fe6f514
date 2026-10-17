// Rendert titelbild.html als 2400 × 1260 PNG.
// Aufruf aus dem Repo-Wurzelverzeichnis: node docs/briefings/blog-agentic-coding/render-titelbild.cjs
const { chromium } = require('playwright');
const path = require('path');
(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage({ viewport: { width: 1200, height: 630 }, deviceScaleFactor: 2 });
  await page.goto('file://' + path.join(__dirname, 'titelbild.html'));
  await page.evaluate(() => document.fonts.ready);
  await page.screenshot({ path: path.join(__dirname, '../../../blocksy-child/assets/img/blog/agentic-coding-hero.png') });
  await browser.close();
})();

const { defineConfig } = require('@playwright/test');
const { existsSync } = require('node:fs');
const chrome = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
// CI uses the browser shipped with the GitHub runner image (not an exact pin).
const executablePath = process.env.PLAYWRIGHT_CHROMIUM_EXECUTABLE_PATH || (existsSync(chrome) ? chrome : undefined);
module.exports = defineConfig({
  testDir: __dirname,
  testMatch: ['form-submission.spec.cjs', 'website-product.spec.cjs'],
  outputDir: '../../.build/form-test-results',
  // The suite is hermetic. fullyParallel lets CI shard individual tests from
  // this single spec file across independent runners; each runner stays single-worker.
  fullyParallel: true,
  workers: 1,
  // The test budget includes context/page setup. A cold GitHub runner spent
  // 7 s creating its first page; keep startup headroom separate from the
  // unchanged 10 s action and 1 s assertion limits below.
  timeout: 30000,
  expect: { timeout: 1000 },
  reporter: 'list',
  use: {
    headless: true,
    actionTimeout: 10000,
    navigationTimeout: 10000,
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure',
    launchOptions: executablePath ? { executablePath } : {},
  },
});

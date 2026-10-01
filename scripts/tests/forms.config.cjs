const { defineConfig } = require('@playwright/test');
const { existsSync } = require('node:fs');
const chrome = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
// CI uses the browser shipped with the GitHub runner image (not an exact pin).
const executablePath = process.env.PLAYWRIGHT_CHROMIUM_EXECUTABLE_PATH || (existsSync(chrome) ? chrome : undefined);
module.exports = defineConfig({
  testDir: __dirname,
  testMatch: 'form-submission.spec.cjs',
  outputDir: '../../.build/form-test-results',
  // The suite is hermetic. fullyParallel lets CI shard individual tests from
  // this single spec file across independent runners; each runner stays single-worker.
  fullyParallel: true,
  workers: 1,
  timeout: 10000,
  expect: { timeout: 1000 },
  reporter: 'list',
  use: {
    headless: true,
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure',
    launchOptions: executablePath ? { executablePath } : {},
  },
});

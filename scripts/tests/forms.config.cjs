const { defineConfig } = require('@playwright/test');
const { existsSync } = require('node:fs');
const chrome = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
// CI uses the browser already shipped with the pinned GitHub runner image.
const executablePath = process.env.PLAYWRIGHT_CHROMIUM_EXECUTABLE_PATH || (existsSync(chrome) ? chrome : undefined);

module.exports = defineConfig({
  testDir: __dirname,
  testMatch: 'form-submission.spec.cjs',
  outputDir: '../../.build/form-test-results',
  workers: 1,
  timeout: 10000,
  expect: { timeout: 1000 },
  reporter: 'list',
  use: {
    headless: true,
    launchOptions: executablePath ? { executablePath } : {},
  },
});

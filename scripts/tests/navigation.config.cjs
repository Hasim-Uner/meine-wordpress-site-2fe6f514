const { defineConfig } = require('@playwright/test');
const { existsSync } = require('node:fs');
const chrome = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
// Optional: a preinstalled Chromium whose build differs from the pinned Playwright.
const executablePath = process.env.PLAYWRIGHT_CHROMIUM_EXECUTABLE_PATH || (existsSync(chrome) ? chrome : undefined);

module.exports = defineConfig({
  testDir: __dirname,
  testMatch: 'navigation.spec.cjs',
  outputDir: '../../.build/navigation-test-results',
  workers: 1,
  timeout: 15000,
  expect: { timeout: 2000 },
  reporter: 'list',
  use: {
    headless: true,
    launchOptions: executablePath ? { executablePath } : {},
  },
});

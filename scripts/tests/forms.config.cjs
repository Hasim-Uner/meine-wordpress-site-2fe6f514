const { defineConfig } = require('@playwright/test');
const { existsSync } = require('node:fs');
const chrome = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';

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
    launchOptions: existsSync(chrome) ? { executablePath: chrome } : {},
  },
});

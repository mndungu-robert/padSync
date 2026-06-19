export default {
  testDir: '.',
  testMatch: '**/*.mjs',
  timeout: 120000,
  use: {
    browserName: 'chromium',
    headless: true,
    launchOptions: { args: ['--no-sandbox'] },
  },
};

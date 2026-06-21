module.exports = {
  testDir: './.tmp_pw',
  timeout: 120000,
  use: {
    browserName: 'chromium',
    headless: true,
    launchOptions: { args: ['--no-sandbox'] },
  },
};

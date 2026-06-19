const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true, args: ['--no-sandbox'] });
  const page = await browser.newPage();

  async function measure(url, label) {
    const t0 = Date.now();
    await page.goto(url, { waitUntil: 'domcontentloaded' });
    const domMs = Date.now() - t0;
    await page.waitForLoadState('load');
    const loadMs = Date.now() - t0;

    const nav = await page.evaluate(() => {
      const e = performance.getEntriesByType('navigation')[0];
      if (!e) return null;
      return {
        ttfb: e.responseStart - e.requestStart,
        domContentLoaded: e.domContentLoadedEventEnd - e.startTime,
        loadEvent: e.loadEventEnd - e.startTime,
      };
    });

    const paint = await page.evaluate(() => {
      const out = {};
      for (const p of performance.getEntriesByType('paint')) out[p.name] = p.startTime;
      return out;
    });

    console.log(JSON.stringify({
      label,
      url,
      domContentLoadedWaitMs: domMs,
      loadWaitMs: loadMs,
      nav,
      paint,
    }));
  }

  await measure('http://127.0.0.1:8000/login', 'login');
  await measure('http://127.0.0.1:8000/', 'home');

  await browser.close();
})().catch((e) => {
  console.error(e);
  process.exit(1);
});

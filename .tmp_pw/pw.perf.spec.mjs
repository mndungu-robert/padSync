import { test } from '@playwright/test';

async function measure(page, url, label) {
  const t0 = Date.now();
  await page.goto(url, { waitUntil: 'domcontentloaded' });
  const domMs = Date.now() - t0;
  await page.waitForLoadState('load');
  const loadMs = Date.now() - t0;

  const nav = await page.evaluate(() => {
    const e = performance.getEntriesByType('navigation')[0];
    if (!e) return null;
    return {
      dns: e.domainLookupEnd - e.domainLookupStart,
      connect: e.connectEnd - e.connectStart,
      ttfb: e.responseStart - e.requestStart,
      download: e.responseEnd - e.responseStart,
      domContentLoaded: e.domContentLoadedEventEnd - e.startTime,
      loadEvent: e.loadEventEnd - e.startTime,
    };
  });

  const paint = await page.evaluate(() => {
    const out = {};
    for (const p of performance.getEntriesByType('paint')) out[p.name] = p.startTime;
    return out;
  });

  console.log('PERF ' + JSON.stringify({ label, url, domContentLoadedWaitMs: domMs, loadWaitMs: loadMs, nav, paint }));
}

test('perf login and home', async ({ page }) => {
  await measure(page, 'http://127.0.0.1:8000/login', 'login');
  await measure(page, 'http://127.0.0.1:8000/', 'home');
});

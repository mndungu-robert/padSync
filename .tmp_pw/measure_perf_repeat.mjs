import { chromium } from 'playwright';

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
      ttfb: +(e.responseStart - e.requestStart).toFixed(2),
      dcl: +(e.domContentLoadedEventEnd - e.startTime).toFixed(2),
      load: +(e.loadEventEnd - e.startTime).toFixed(2),
    };
  });

  return {
    label,
    domWait: domMs,
    loadWait: loadMs,
    nav,
  };
}

const results = [];
for (let i = 0; i < 3; i++) {
  results.push(await measure('http://127.0.0.1:8000/login', `login-${i + 1}`));
}
for (let i = 0; i < 3; i++) {
  results.push(await measure('http://127.0.0.1:8000/', `home-${i + 1}`));
}

function avg(rows, key) {
  return +(rows.reduce((s, r) => s + r[key], 0) / rows.length).toFixed(2);
}

const login = results.filter(r => r.label.startsWith('login-'));
const home = results.filter(r => r.label.startsWith('home-'));

console.log(JSON.stringify({
  samples: results,
  summary: {
    login: {
      avgDomWaitMs: avg(login, 'domWait'),
      avgLoadWaitMs: avg(login, 'loadWait'),
      avgNavTtfbMs: +(login.reduce((s, r) => s + (r.nav?.ttfb ?? 0), 0) / login.length).toFixed(2),
      avgNavDclMs: +(login.reduce((s, r) => s + (r.nav?.dcl ?? 0), 0) / login.length).toFixed(2),
      avgNavLoadMs: +(login.reduce((s, r) => s + (r.nav?.load ?? 0), 0) / login.length).toFixed(2),
    },
    home: {
      avgDomWaitMs: avg(home, 'domWait'),
      avgLoadWaitMs: avg(home, 'loadWait'),
      avgNavTtfbMs: +(home.reduce((s, r) => s + (r.nav?.ttfb ?? 0), 0) / home.length).toFixed(2),
      avgNavDclMs: +(home.reduce((s, r) => s + (r.nav?.dcl ?? 0), 0) / home.length).toFixed(2),
      avgNavLoadMs: +(home.reduce((s, r) => s + (r.nav?.load ?? 0), 0) / home.length).toFixed(2),
    },
  },
}, null, 2));

await browser.close();

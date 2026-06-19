import { chromium } from 'playwright';

const browser = await chromium.launch({ headless: true, args: ['--no-sandbox'] });
const page = await browser.newPage();

const reqStarts = new Map();
const rows = [];

page.on('request', (req) => {
  reqStarts.set(req, Date.now());
});

page.on('requestfinished', async (req) => {
  const start = reqStarts.get(req) ?? Date.now();
  const ms = Date.now() - start;
  const resp = await req.response();
  rows.push({
    url: req.url(),
    method: req.method(),
    status: resp ? resp.status() : null,
    resourceType: req.resourceType(),
    durationMs: ms,
  });
});

page.on('requestfailed', (req) => {
  const start = reqStarts.get(req) ?? Date.now();
  const ms = Date.now() - start;
  rows.push({
    url: req.url(),
    method: req.method(),
    status: 'FAILED',
    resourceType: req.resourceType(),
    durationMs: ms,
    error: req.failure()?.errorText ?? 'unknown',
  });
});

const t0 = Date.now();
await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'domcontentloaded' });
const domMs = Date.now() - t0;
await page.waitForLoadState('load');
const loadMs = Date.now() - t0;

// give late requests a short moment to settle
await page.waitForTimeout(500);

rows.sort((a, b) => b.durationMs - a.durationMs);

console.log(JSON.stringify({ domMs, loadMs, topRequests: rows.slice(0, 15) }, null, 2));

await browser.close();

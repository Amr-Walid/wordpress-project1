const { chromium } = require('playwright');
const path = require('path');

const FILE = 'file://' + path.resolve(__dirname, 'mockups/mockup.html');
const OUT = path.resolve(__dirname, 'mockups');

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ deviceScaleFactor: 2 });
  const page = await ctx.newPage();
  await page.goto(FILE, { waitUntil: 'networkidle' });
  await page.waitForTimeout(800);

  for (const id of ['cover', 'full', 'duo']) {
    const el = await page.$('#' + id);
    if (el) {
      const box = await el.boundingBox();
      await el.screenshot({ path: `${OUT}/mockup-${id}.png`, timeout: 120000, animations: 'disabled' });
      console.log('rendered', id, box && Math.round(box.width) + 'x' + Math.round(box.height));
    }
  }
  await browser.close();
  console.log('MOCKUPS_DONE');
})().catch((e) => { console.error(e); process.exit(1); });

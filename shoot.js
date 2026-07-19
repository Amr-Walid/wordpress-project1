const { chromium } = require('playwright');

const URL = 'http://localhost:8080/greenio-preview.html';
const OUT = '/home/user/webapp/screenshots';

(async () => {
  const browser = await chromium.launch();

  // ---------- DESKTOP FULL PAGE ----------
  const dCtx = await browser.newContext({
    viewport: { width: 1440, height: 900 },
    deviceScaleFactor: 2,
  });
  const dPage = await dCtx.newPage();
  await dPage.goto(URL, { waitUntil: 'networkidle' });
  // trigger reveal animations by scrolling through the whole page
  await dPage.evaluate(async () => {
    await new Promise((resolve) => {
      let y = 0;
      const step = () => {
        window.scrollTo(0, y);
        y += 400;
        if (y < document.body.scrollHeight) setTimeout(step, 60);
        else { window.scrollTo(0, 0); setTimeout(resolve, 400); }
      };
      step();
    });
  });
  // Force every reveal element to its final (visible) state so the fullPage
  // capture doesn't include un-triggered opacity:0 sections.
  await dPage.evaluate(() => {
    document.querySelectorAll('[data-reveal]').forEach((el) => {
      el.classList.add('in');
      el.style.opacity = '1';
      el.style.transform = 'none';
    });
  });
  await dPage.waitForTimeout(1200);
  await dPage.evaluate(() => window.scrollTo(0, 0));
  await dPage.waitForTimeout(300);
  await dPage.screenshot({ path: `${OUT}/desktop-full.png`, fullPage: true, animations: 'disabled' });

  // Section crops (desktop)
  const sections = [
    ['#home', 'hero'],
    ['#services', 'services'],
    ['#about', 'about'],
    ['.band', 'band'],
    ['#projects', 'projects'],
    ['#contact', 'cta'],
    ['.site-footer', 'footer'],
  ];
  for (const [sel, name] of sections) {
    const el = await dPage.$(sel);
    if (el) {
      await el.scrollIntoViewIfNeeded();
      await dPage.waitForTimeout(500);
      await el.screenshot({ path: `${OUT}/section-${name}.png` });
    }
  }

  // Viewport (above-the-fold) hero shot
  await dPage.evaluate(() => window.scrollTo(0, 0));
  await dPage.waitForTimeout(600);
  await dPage.screenshot({ path: `${OUT}/desktop-hero-fold.png`, fullPage: false });
  await dCtx.close();

  // ---------- MOBILE FULL PAGE ----------
  const mCtx = await browser.newContext({
    viewport: { width: 390, height: 844 },
    deviceScaleFactor: 3,
    isMobile: true,
  });
  const mPage = await mCtx.newPage();
  await mPage.goto(URL, { waitUntil: 'networkidle' });
  await mPage.evaluate(async () => {
    await new Promise((resolve) => {
      let y = 0;
      const step = () => {
        window.scrollTo(0, y);
        y += 300;
        if (y < document.body.scrollHeight) setTimeout(step, 60);
        else { window.scrollTo(0, 0); setTimeout(resolve, 400); }
      };
      step();
    });
  });
  await mPage.evaluate(() => {
    document.querySelectorAll('[data-reveal]').forEach((el) => {
      el.classList.add('in');
      el.style.opacity = '1';
      el.style.transform = 'none';
    });
  });
  await mPage.waitForTimeout(1000);
  await mPage.evaluate(() => window.scrollTo(0, 0));
  await mPage.waitForTimeout(300);
  await mPage.screenshot({ path: `${OUT}/mobile-full.png`, fullPage: true, animations: 'disabled' });
  await mPage.screenshot({ path: `${OUT}/mobile-fold.png`, fullPage: false });
  await mCtx.close();

  await browser.close();
  console.log('SCREENSHOTS_DONE');
})().catch((e) => { console.error(e); process.exit(1); });

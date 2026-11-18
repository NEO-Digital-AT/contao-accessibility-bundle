/*
 * Browser test of the text size on a design with font sizes in px (see
 * README.md in this folder). Without the module option "Enlarge the whole
 * page" the root font size grows and text in px stays as it is (the known
 * limit). With the option the content of the page is zoomed: text grows, the
 * panel keeps its size, nothing scrolls sideways at 1440 and 390 px, the panel
 * opens above the zoomed settings button of the buttons at the edge of the
 * screen (NEO Basic), side by side the accessibility button sits next to the
 * zoomed cookie button, the reading guide stays under the pointer, and Reset
 * all takes the zoom back.
 *
 *   node tests/e2e/text-size.e2e.cjs <screenshot-dir>
 *
 * Environment: NAX_PAGE (a German page with paragraphs; default
 * http://127.0.0.1:8099/a11y-test), NAX_SWITCH (a command that sets up the
 * installation; called with "menu" (option off), "zoom" (option on), "zoom-stacked"
 * (option on, buttons side by side in the page layout), and "done" at the end;
 * without it only the scenarios in NAX_SCENARIOS run), NAX_CHROME,
 * NAX_PLAYWRIGHT as in panel.e2e.cjs. The page needs NEO Cookie Consent with
 * its button to reopen bottom left and the panel module bottom left (NEO
 * Basic with the dock); the test makes the paragraphs px-sized.
 */
'use strict';

const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');
const { chromium } = require(process.env.NAX_PLAYWRIGHT || 'playwright');

const PAGE = process.env.NAX_PAGE || 'http://127.0.0.1:8099/a11y-test';
const SHOTS = process.argv[2];
const SWITCH = process.env.NAX_SWITCH || '';
const ONLY = (process.env.NAX_SCENARIOS || '').split(',').filter(Boolean);
const WIDTHS = [{ name: 'desktop', width: 1440, height: 900 }, { name: 'phone', width: 390, height: 844 }];

fs.mkdirSync(SHOTS, { recursive: true });

let passes = 0;
let failures = 0;
function check(label, ok, detail) {
  if (ok) passes++; else failures++;
  console.log((ok ? 'PASS ' : 'FAIL ') + label + (!ok && detail !== undefined ? '  -> ' + JSON.stringify(detail) : ''));
}

function scenario(name) {
  if (ONLY.length && ONLY.indexOf(name) === -1) return false;
  if (SWITCH) execSync(SWITCH + ' ' + name, { stdio: 'pipe' });
  console.log('-- ' + name);
  return true;
}

const PX = '<style>main p, main li { font-size: 16px; line-height: 24px; }</style>';
// Settings button of the buttons at the edge of the screen (NEO Basic), bottom left.
const SETTINGS = '[data-neo-dock-button="bottom-left"]';

async function open(browser, vp, stored) {
  const context = await browser.newContext({ viewport: { width: vp.width, height: vp.height } });
  await context.addInitScript((s) => { try { localStorage.setItem('nax-settings', JSON.stringify(s)); } catch (e) { /* blocked */ } }, stored);
  const page = await context.newPage();
  await page.route(PAGE, async (route) => {
    const res = await route.fetch();
    const html = (await res.text()).replace('</head>', PX + '</head>');
    await route.fulfill({ response: res, body: html });
  });
  await page.goto(PAGE);
  await page.waitForSelector('[data-nax]', { state: 'attached' });
  if (await page.locator('[data-ncc]').count()) {
    await page.waitForSelector('[data-ncc]:not([hidden])', { timeout: 3000 }).catch(() => {});
    const necessary = page.locator('[data-ncc-action="necessary"]').first();
    if (await necessary.isVisible()) await necessary.click();
  }
  await page.waitForTimeout(300);
  return { context, page };
}

function state(page) {
  return page.evaluate(() => {
    const p = document.querySelector('main p');
    const root = document.querySelector('[data-nax]');
    const html = document.documentElement;
    return {
      font: html.getAttribute('data-a11y-font'),
      zoomAttr: html.hasAttribute('data-a11y-font-zoom'),
      rootFont: getComputedStyle(html).fontSize,
      pZoom: p.currentCSSZoom,
      // Font size as drawn: the computed size times the zoom on the way to it.
      pFont: Math.round(parseFloat(getComputedStyle(p).fontSize) * (p.currentCSSZoom || 1) * 10) / 10,
      panelZoom: root.currentCSSZoom,
      scrollW: html.scrollWidth,
      clientW: html.clientWidth,
    };
  });
}

async function rect(page, sel) {
  return page.evaluate((s) => { const r = document.querySelector(s).getBoundingClientRect(); return { left: Math.round(r.left), right: Math.round(r.right), top: Math.round(r.top), bottom: Math.round(r.bottom), width: Math.round(r.width) }; }, sel);
}

async function remMode(browser, vp) {
  const tag = 'option off, ' + vp.name;
  const { context, page } = await open(browser, vp, { font: 150 });
  const s = await state(page);
  check(tag + ': root font size grows (' + s.rootFont + ')', s.font === '150' && !s.zoomAttr && s.rootFont === '24px', s);
  check(tag + ': text in px stays as it is (' + s.pFont + 'px drawn)', s.pZoom === 1 && s.pFont === 16, s);
  await context.close();
}

async function zoomMode(browser, vp) {
  const tag = 'option on, ' + vp.name;
  const { context, page } = await open(browser, vp, { font: 150, guide: true });
  let s = await state(page);
  check(tag + ': the content is zoomed (zoom ' + s.pZoom + ', text ' + s.pFont + 'px drawn)', s.font === '150' && s.zoomAttr && s.pZoom === 1.5 && s.pFont === 24, s);
  check(tag + ': the root font size stays (' + s.rootFont + ')', s.rootFont === '16px', s.rootFont);
  check(tag + ': the panel keeps its size (zoom ' + s.panelZoom + ')', s.panelZoom === 1, s.panelZoom);
  check(tag + ': nothing scrolls sideways (' + s.scrollW + ' / ' + s.clientW + ')', s.scrollW <= s.clientW, s);
  const button = await rect(page, SETTINGS);
  check(tag + ': the settings button is zoomed with the page and inside the window', button.width === 66 && button.left >= 0 && button.bottom <= vp.height, button);
  await page.mouse.move(vp.width / 2, 300);
  await page.waitForTimeout(150);
  const guide = await rect(page, '.nax__guide');
  // The guide runs 12 px below the pointer (as without zoom).
  check(tag + ': the reading guide stays under the pointer (top ' + guide.top + ')', Math.abs(guide.top - 312) <= 2, guide);
  await page.click(SETTINGS);
  await page.click('[data-neo-dock-for="accessibility"]');
  await page.waitForTimeout(300);
  const panel = await rect(page, '[data-nax-panel]');
  const b2 = await rect(page, SETTINGS);
  check(tag + ': the panel opens above the zoomed settings button, inside the window', panel.bottom <= b2.top && panel.top >= 0 && panel.left >= 0 && panel.right <= vp.width, { panel, button: b2 });
  await page.screenshot({ path: path.join(SHOTS, 'zoom-panel-' + vp.name + '.png') });
  await page.click('[data-nax-reset]');
  await page.waitForTimeout(150);
  s = await state(page);
  check(tag + ': Reset all takes the zoom back', !s.zoomAttr && s.font === null && s.pZoom === 1, s);
  // A+ in the panel zooms step by step.
  await page.click('[data-nax-font="1"]');
  await page.click('[data-nax-font="1"]');
  s = await state(page);
  check(tag + ': A+ twice: 130 %, zoom 1.3', s.font === '130' && s.zoomAttr && Math.abs(s.pZoom - 1.3) < 0.001, s);
  await context.close();
}

async function zoomStacked(browser, vp) {
  const tag = 'option on, side by side, ' + vp.name;
  const { context, page } = await open(browser, vp, { font: 150 });
  const toggle = await rect(page, '[data-nax-toggle]');
  const cookie = await rect(page, '[data-ncc-reopen]');
  // The panel button sits in the corner (lower order), the zoomed cookie button beside it.
  check(tag + ': the zoomed cookie button sits next to the accessibility button', cookie.left > toggle.right && Math.abs(cookie.left - toggle.right - 12) <= 1 && Math.abs(cookie.bottom - toggle.bottom) <= 1, { toggle, cookie });
  const s = await state(page);
  check(tag + ': nothing scrolls sideways', s.scrollW <= s.clientW, s);
  await page.screenshot({ path: path.join(SHOTS, 'zoom-stacked-' + vp.name + '.png') });
  await context.close();
}

(async () => {
  const browser = await chromium.launch(process.env.NAX_CHROME ? { executablePath: process.env.NAX_CHROME } : {});
  const run = async (label, part) => {
    try { await part(); } catch (e) { check(label + ': runs to the end', false, String(e && e.message || e).split('\n')[0]); }
  };
  try {
    if (scenario('menu')) for (const vp of WIDTHS) await run('option off ' + vp.name, () => remMode(browser, vp));
    if (scenario('zoom')) for (const vp of WIDTHS) await run('option on ' + vp.name, () => zoomMode(browser, vp));
    if (scenario('zoom-stacked')) for (const vp of WIDTHS) await run('side by side ' + vp.name, () => zoomStacked(browser, vp));
  } finally {
    await browser.close();
    if (SWITCH) execSync(SWITCH + ' done', { stdio: 'pipe' });
  }
  console.log('\n' + passes + ' passed, ' + failures + ' failed');
  process.exit(failures ? 1 : 0);
})();

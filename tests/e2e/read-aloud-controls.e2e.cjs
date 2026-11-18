/*
 * Browser test of read aloud on controls (see README.md in this folder): with
 * read aloud on, a click on the question of an accordion, on the label of a
 * checkbox and on the text of a card whose whole surface is a link reads that
 * text and does nothing else; a hint, also a status message, says that the
 * next click uses it, and the next click does. Keyboard clicks are never held
 * back, and what gets the keyboard focus is read. A field reads its label when
 * it gets the focus. Read aloud off: every click acts at once. Desktop (mouse)
 * and phone (touch); speech output replaced by a recorder.
 *
 *   node tests/e2e/read-aloud-controls.e2e.cjs <screenshot-dir>
 *
 * Environment: NAX_PAGE (a German page with the Contao element "Accordion" and
 * a form with a text field and a checkbox; default
 * http://127.0.0.1:8099/vorlesen-test), NAX_CARD_HREF (where the card links;
 * default the root of the page's site), NAX_PAGE_HTML (a saved copy of that
 * page, served in its place, for an installation where the content cannot
 * stay), NAX_CHROME, NAX_PLAYWRIGHT as in panel.e2e.cjs. The card is added to
 * the page by the test.
 */
'use strict';

const fs = require('fs');
const path = require('path');
const { chromium } = require(process.env.NAX_PLAYWRIGHT || 'playwright');

const PAGE = process.env.NAX_PAGE || 'http://127.0.0.1:8099/vorlesen-test';
const CARD_HREF = process.env.NAX_CARD_HREF || new URL(PAGE).origin + '/';
const SAVED = process.env.NAX_PAGE_HTML ? fs.readFileSync(process.env.NAX_PAGE_HTML, 'utf8') : null;
const SHOTS = process.argv[2];
const HINT = 'Zum Benutzen noch einmal klicken oder tippen.';

fs.mkdirSync(SHOTS, { recursive: true });

let passes = 0;
let failures = 0;
function check(label, ok, detail) {
  if (ok) passes++; else failures++;
  console.log((ok ? 'PASS ' : 'FAIL ') + label + (!ok && detail !== undefined ? '  -> ' + JSON.stringify(detail) : ''));
}

// Speech output replaced by a recorder with a German voice of the device;
// read aloud switched on, as a visitor stored it.
const SETUP = (on) => {
  const log = { spoken: [] };
  const voices = [{ name: 'Device German', lang: 'de-DE', localService: true, voiceURI: 'device-de', default: true }];
  const synth = {
    getVoices: () => voices,
    addEventListener: () => {},
    speak(u) { log.spoken.push(u.text); },
    cancel() {},
    pause() {},
    resume() {},
  };
  function Utterance(text) { this.text = text; this.lang = ''; this.voice = null; this.rate = 1; }
  Object.defineProperty(window, 'speechSynthesis', { value: synth, configurable: true });
  window.SpeechSynthesisUtterance = Utterance;
  window.__speech = log;
  try {
    localStorage.setItem('nax-settings', JSON.stringify(on ? { speak: true } : {}));
  } catch (e) { /* storage blocked */ }
};

// A card whose whole surface is a link (the link's ::before covers the card).
const CARD = '<style>.t-card{position:relative;max-width:420px;margin:24px 0;padding:16px;border:1px solid #888}'
  + '.t-card a::before{content:"";position:absolute;inset:0}</style>'
  + '<div class="t-card"><h3><a href="' + CARD_HREF + '">Zimmer mit Seeblick</a></h3>'
  + '<p class="t-card__text">Ruhig gelegen, mit Balkon und Frühstück.</p></div>';

async function open(browser, viewport, on, mobile) {
  const context = await browser.newContext({ viewport, hasTouch: !!mobile, isMobile: !!mobile });
  await context.addInitScript(SETUP, on);
  const page = await context.newPage();
  await page.route(PAGE, async (route) => {
    if (SAVED) {
      await route.fulfill({ status: 200, contentType: 'text/html; charset=utf-8', body: SAVED.replace('</main>', CARD + '</main>') });

      return;
    }

    const res = await route.fetch();
    const html = (await res.text()).replace('</main>', CARD + '</main>');
    await route.fulfill({ response: res, body: html });
  });
  await page.goto(PAGE);
  await page.waitForSelector('[data-nax]', { state: 'attached' });
  // A cookie banner would cover part of the page: answer it ("necessary only").
  if (await page.locator('[data-ncc]').count()) {
    await page.waitForSelector('[data-ncc]:not([hidden])', { timeout: 3000 }).catch(() => {});
    // With read aloud on, the first click reads the button, the second uses it.
    const banner = page.locator('[data-ncc-action="necessary"]').first();
    for (let i = 0; i < 2 && await banner.isVisible(); i++) {
      await banner.click();
      await page.waitForTimeout(150);
    }
  }
  await page.waitForTimeout(300);
  return { context, page };
}

async function spoken(page) {
  return page.evaluate(() => window.__speech.spoken.slice(-1)[0] || null);
}

async function hint(page) {
  return page.evaluate(() => {
    const h = document.querySelector('[data-nax-hint]');
    if (!h) return { shown: false, text: '' };
    const r = h.getBoundingClientRect();
    return { shown: h.classList.contains('nax__hint--shown') && r.width > 10, text: h.textContent, role: h.getAttribute('role') };
  });
}

async function expanded(page, i) {
  return page.evaluate((n) => document.querySelectorAll('.handorgel__header__button')[n].getAttribute('aria-expanded'), i);
}

// A click or tap at the centre of the element, like a visitor: on the card the
// link that covers the text gets it. The element is scrolled to the middle of
// the window first, away from the floating buttons.
async function press(page, selector, mobile) {
  const el = page.locator(selector).first();
  await el.evaluate((node) => node.scrollIntoView({ block: 'center' }));
  const box = await el.boundingBox();
  const x = box.x + Math.min(box.width / 2, 40);
  const y = box.y + box.height / 2;
  if (mobile) await page.touchscreen.tap(x, y); else await page.mouse.click(x, y);
  await page.waitForTimeout(150);
}

async function pointer(browser, viewport, name, mobile) {
  const tag = 'read aloud on, ' + name;
  const { context, page } = await open(browser, viewport, true, mobile);

  // A plain paragraph: read at once, as before.
  await press(page, 'main p', mobile);
  check(tag + ': a paragraph is read', (await spoken(page)) === 'Ein ganz normaler Absatz zum Vorlesen.', await spoken(page));

  // The question of an accordion.
  const q = '.handorgel__header__button >> nth=1';
  const before = await expanded(page, 1);
  await press(page, q, mobile);
  check(tag + ': the question of the accordion is read', (await spoken(page)) === 'Gibt es Parkplätze?', await spoken(page));
  check(tag + ': ... and does not open yet', (await expanded(page, 1)) === before, { before, after: await expanded(page, 1) });
  const h = await hint(page);
  check(tag + ': hint "' + h.text + '" shown, as a status message', h.shown && h.text === HINT && h.role === 'status', h);
  await page.screenshot({ path: path.join(SHOTS, 'hint-accordion-' + name + '.png') });
  await press(page, q, mobile);
  check(tag + ': the second click opens it', (await expanded(page, 1)) !== before);
  check(tag + ': hint gone', !(await hint(page)).shown);

  // The label of a checkbox.
  const box = '#opt_17_0, input[type="checkbox"][name="agree"]';
  await press(page, 'label[for="' + (await page.getAttribute('input[type="checkbox"][name="agree"]', 'id')) + '"]', mobile);
  check(tag + ': the label of the checkbox is read', (await spoken(page)) === 'Ich habe die Hinweise gelesen', await spoken(page));
  check(tag + ': ... and does not tick it yet', !(await page.isChecked(box)));
  check(tag + ': hint shown', (await hint(page)).shown);
  await press(page, 'label[for="' + (await page.getAttribute('input[type="checkbox"][name="agree"]', 'id')) + '"]', mobile);
  check(tag + ': the second click ticks it', await page.isChecked(box));

  // A field: its label is read when it gets the focus; typing works.
  await press(page, 'input[name="name"]', mobile);
  check(tag + ': a text field reads its label', (await spoken(page)) === 'Ihr Name', await spoken(page));
  await page.keyboard.type('Max');
  check(tag + ': ... and takes typing at once', (await page.inputValue('input[name="name"]')) === 'Max');

  // The card: the text under the pointer, beneath the link that covers it.
  const url = page.url();
  await press(page, '.t-card__text', mobile);
  check(tag + ': the text of the card under the pointer is read', (await spoken(page)) === 'Ruhig gelegen, mit Balkon und Frühstück.', await spoken(page));
  check(tag + ': ... and the link does not open yet', page.url() === url, page.url());
  await page.screenshot({ path: path.join(SHOTS, 'hint-card-' + name + '.png') });
  await Promise.all([page.waitForURL((u) => u.toString() !== url, { timeout: 5000 }).catch(() => {}), press(page, '.t-card__text', mobile)]);
  check(tag + ': the second click opens the link', page.url() !== url, page.url());
  await context.close();
}

async function keyboard(browser) {
  const tag = 'read aloud on, keyboard';
  const { context, page } = await open(browser, { width: 1440, height: 900 }, true, false);
  await page.focus('main p');
  let found = false;
  for (let i = 0; i < 30 && !found; i++) {
    await page.keyboard.press('Tab');
    found = await page.evaluate(() => document.activeElement && document.activeElement.classList.contains('handorgel__header__button'));
  }
  check(tag + ': Tab reaches the question of the accordion', found);
  await page.waitForTimeout(100);
  check(tag + ': ... and reads it when it gets the focus', (await spoken(page)) === 'Wie lange dauert die Anreise?', await spoken(page));
  const before = await expanded(page, 0);
  await page.keyboard.press('Enter');
  await page.waitForTimeout(150);
  check(tag + ': Enter uses it at once', (await expanded(page, 0)) !== before);
  check(tag + ': no hint for keyboard use', !(await hint(page)).shown, await hint(page));

  // Esc takes the hint back: the next click reads again.
  const q = '.handorgel__header__button >> nth=1';
  const b2 = await expanded(page, 1);
  await press(page, q, false);
  check(tag + ': a click on the second question shows the hint', (await hint(page)).shown);
  await page.keyboard.press('Escape');
  check(tag + ': Esc takes the hint back', !(await hint(page)).shown);
  await press(page, q, false);
  await page.waitForTimeout(150);
  check(tag + ': after Esc the next click reads again instead of opening', (await expanded(page, 1)) === b2);
  await context.close();
}

async function off(browser) {
  const tag = 'read aloud off';
  const { context, page } = await open(browser, { width: 1440, height: 900 }, false, false);
  const before = await expanded(page, 1);
  await press(page, '.handorgel__header__button >> nth=1', false);
  check(tag + ': a click opens the accordion at once', (await expanded(page, 1)) !== before);
  const id = await page.getAttribute('input[type="checkbox"][name="agree"]', 'id');
  await press(page, 'label[for="' + id + '"]', false);
  check(tag + ': a click on the label ticks the box at once', await page.isChecked('input[type="checkbox"][name="agree"]'));
  check(tag + ': nothing was read', (await spoken(page)) === null, await spoken(page));
  const url = page.url();
  await Promise.all([page.waitForURL((u) => u.toString() !== url, { timeout: 5000 }).catch(() => {}), press(page, '.t-card__text', false)]);
  check(tag + ': a click on the card opens the link at once', page.url() !== url, page.url());
  await context.close();
}

(async () => {
  const browser = await chromium.launch(process.env.NAX_CHROME ? { executablePath: process.env.NAX_CHROME } : {});
  const parts = [
    ['desktop', () => pointer(browser, { width: 1440, height: 900 }, 'desktop', false)],
    ['phone', () => pointer(browser, { width: 390, height: 844 }, 'phone', true)],
    ['keyboard', () => keyboard(browser)],
    ['off', () => off(browser)],
  ];
  for (const [label, part] of parts) {
    try {
      await part();
    } catch (e) {
      check(label + ': runs to the end', false, String(e && e.message || e).split('\n')[0]);
    }
  }
  await browser.close();
  console.log('\n' + passes + ' passed, ' + failures + ' failed');
  process.exit(failures ? 1 : 0);
})();

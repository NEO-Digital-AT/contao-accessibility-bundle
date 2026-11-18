/*
 * Browser test of the accessibility panel against a Contao test installation
 * (see README.md in this folder): every setting and quick option, keyboard
 * use, reading guide and mask, page structure, read aloud (speech output
 * replaced by a recorder), reset, stored settings, counter, axe and contrast.
 *
 *   node tests/e2e/panel.e2e.cjs <axe.min.js> <screenshot-dir>
 *
 * Environment: NAX_BASE (default http://127.0.0.1:8099), NAX_CHROME (browser
 * executable, default: Playwright's Chromium), NAX_PLAYWRIGHT (path of the
 * playwright package, default "playwright").
 */
'use strict';

const fs = require('fs');
const path = require('path');
const { chromium } = require(process.env.NAX_PLAYWRIGHT || 'playwright');

const BASE = (process.env.NAX_BASE || 'http://127.0.0.1:8099').replace(/\/$/, '');
const AXE = fs.readFileSync(process.argv[2], 'utf8');
const SHOTS = process.argv[3];
const PAGE_DE = BASE + '/a11y-test';
const PAGE_EN = BASE + '/en/a11y-test';

const SWITCHES = ['typeface', 'dyslexia', 'readable', 'align', 'headings', 'links', 'noimages', 'cursor',
  'contrast', 'lowcontrast', 'lowsaturation', 'grayscale', 'motion', 'focus', 'guide', 'mask', 'speak'];
const PROFILES = {
  vision: { font: 130, contrast: true, cursor: true, focus: true },
  dyslexia: { dyslexia: true, readable: true, align: true },
  concentration: { motion: true, mask: true },
  epilepsy: { motion: true, lowsaturation: true },
  motor: { cursor: true, focus: true },
  reader: { speak: true },
};

let failures = 0;
let passes = 0;

function check(label, ok, detail) {
  if (ok) {
    passes++;
  } else {
    failures++;
  }

  console.log((ok ? 'PASS ' : 'FAIL ') + label + (detail !== undefined && !ok ? '  -> ' + JSON.stringify(detail) : ''));
}

// Replaces the browser's speech output with a recorder: speak() keeps the
// utterance, finish() ends it like the browser would. Options: "voices"
// (names of the voices below the browser has), "lateMs" (the voices are
// reported only after this time, with "voiceschanged"; -1: never),
// "allowOnline" (the page comes with online voices allowed, as the module
// setting writes it).
const SPEECH_STUB = (options) => {
  const opts = options || {};
  const log = { spoken: [], cancelled: 0, paused: 0, resumed: 0 };
  const all = [
    { name: 'Online German', lang: 'de-DE', localService: false, voiceURI: 'online-de', default: false },
    { name: 'Device German', lang: 'de-DE', localService: true, voiceURI: 'device-de', default: false },
    { name: 'Device English', lang: 'en-GB', localService: true, voiceURI: 'device-en', default: true },
  ];
  const voices = all.filter((v) => !opts.voices || opts.voices.indexOf(v.name) !== -1);
  const late = typeof opts.lateMs === 'number' ? opts.lateMs : 0;
  let reported = late === 0;
  const handlers = [];
  let current = null;
  const synth = {
    getVoices: () => (reported ? voices : []),
    addEventListener: (type, handler) => { if (type === 'voiceschanged') handlers.push(handler); },
    speak(u) { log.spoken.push({ text: u.text, lang: u.lang, voice: u.voice ? u.voice.name : null }); current = u; },
    cancel() { log.cancelled++; const u = current; current = null; if (u && u.onerror) setTimeout(() => u.onerror({ error: 'interrupted' }), 0); },
    pause() { log.paused++; },
    resume() { log.resumed++; },
    finish() { const u = current; current = null; if (u && u.onend) u.onend({}); },
  };
  function Utterance(text) { this.text = text; this.lang = ''; this.voice = null; this.rate = 1; }
  Object.defineProperty(window, 'speechSynthesis', { value: synth, configurable: true });
  window.SpeechSynthesisUtterance = Utterance;
  window.__speech = log;

  if (late > 0) {
    setTimeout(() => { reported = true; handlers.forEach((h) => h({})); }, late);
  }

  // The module setting "Allow the browser's online voices", as written in the page.
  if (opts.allowOnline) {
    new MutationObserver(() => {
      const root = document.querySelector('[data-nax]');

      if (root) {
        root.setAttribute('data-nax-voices', 'any');
      }
    }).observe(document, { childList: true, subtree: true });
  }
};

const NO_SPEECH = () => {
  Object.defineProperty(window, 'speechSynthesis', { value: undefined, configurable: true });
  window.SpeechSynthesisUtterance = undefined;
};

// A cookie banner of the test installation would cover the panel: answer it
// ("necessary only") once per browser context.
async function settle(page) {
  await page.waitForSelector('.nax:not([hidden])');

  const banner = page.locator('[data-ncc-action="necessary"]');

  if (await banner.count() && await banner.first().isVisible()) {
    await banner.first().click();
  }
}

async function open(page) {
  if (await page.locator('[data-nax-panel]').isHidden()) {
    await page.click('[data-nax-toggle]');
  }

  await page.waitForSelector('[data-nax-panel]:not([hidden])');
}

async function close(page) {
  if (await page.locator('[data-nax-panel]').isVisible()) {
    await page.keyboard.press('Escape');
  }
}

async function flip(page, flag) {
  await open(page);
  await page.locator('[data-nax-flag="' + flag + '"]').scrollIntoViewIfNeeded();
  await page.click('[data-nax-flag="' + flag + '"]');
}

async function htmlFlags(page) {
  return page.evaluate(() => Array.from(document.documentElement.attributes)
    .filter((a) => a.name.indexOf('data-a11y-') === 0)
    .map((a) => a.name.slice(10) + (a.value ? '=' + a.value : '')).sort());
}

async function stored(page) {
  return page.evaluate(() => localStorage.getItem('nax-settings'));
}

async function reset(page) {
  await open(page);
  await page.click('[data-nax-reset]');
  await close(page);
  await page.evaluate(() => window.scrollTo(0, 0));
}

async function shot(page, name) {
  await page.waitForTimeout(150);
  await page.screenshot({ path: path.join(SHOTS, name + '.png') });
}

async function axe(page, label, scopeOnly) {
  await page.addScriptTag({ content: AXE });
  const result = await page.evaluate(async () => {
    const r = await window.axe.run(document, { runOnly: { type: 'tag', values: ['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'] } });

    return r.violations.map((v) => ({ id: v.id, nodes: v.nodes.map((n) => n.target.join(' ')) }));
  });
  const panel = result.map((v) => ({ id: v.id, nodes: v.nodes.filter((n) => n.indexOf('nax') !== -1) })).filter((v) => v.nodes.length);
  check('axe ' + label + ': 0 findings in the panel', panel.length === 0, panel);

  if (!scopeOnly && result.length) {
    console.log('     page-wide (test content): ' + JSON.stringify(result));
  }

  return panel.length;
}

// Contrast of every text in the open panel against its real background.
async function panelContrast(page, label) {
  const result = await page.evaluate(() => {
    function rgb(value) {
      const m = value.match(/rgba?\(([^)]+)\)/);

      if (!m) {
        return null;
      }

      const p = m[1].split(/[ ,/]+/).filter(Boolean).map(Number);

      return { r: p[0], g: p[1], b: p[2], a: p.length > 3 ? p[3] : 1 };
    }

    function lum(c) {
      const f = (v) => { v /= 255; return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4); };

      return 0.2126 * f(c.r) + 0.7152 * f(c.g) + 0.0722 * f(c.b);
    }

    function background(el) {
      for (let n = el; n; n = n.parentElement) {
        const c = rgb(getComputedStyle(n).backgroundColor);

        if (c && c.a > 0.99) {
          return c;
        }
      }

      return { r: 255, g: 255, b: 255, a: 1 };
    }

    const panel = document.querySelector('[data-nax-panel]');
    const nodes = [];
    const walker = document.createTreeWalker(panel, NodeFilter.SHOW_TEXT);

    while (walker.nextNode()) {
      const el = walker.currentNode.parentElement;

      if (walker.currentNode.nodeValue.trim() && el.getClientRects().length && getComputedStyle(el).visibility !== 'hidden' && !el.closest('.nax__sr')) {
        nodes.push(el);
      }
    }

    let min = 99;
    let worst = '';

    Array.from(new Set(nodes)).forEach((el) => {
      const style = getComputedStyle(el);
      const fg = rgb(style.color);
      const bg = background(el);
      const opacity = parseFloat(getComputedStyle(el.closest('button, a, div') || el).opacity);

      if (opacity < 1) {
        return; // aria-disabled controls: exempt (WCAG 1.4.3, inactive components)
      }

      const a = lum(fg);
      const b = lum(bg);
      const ratio = (Math.max(a, b) + 0.05) / (Math.min(a, b) + 0.05);

      if (ratio < min) {
        min = ratio;
        worst = el.className + ' "' + el.textContent.trim().slice(0, 30) + '"';
      }
    });

    return { min: Math.round(min * 100) / 100, worst, count: nodes.length };
  });
  check('contrast ' + label + ': every panel text >= 4.5:1 (min ' + result.min + ':1 over ' + result.count + ' texts)', result.min >= 4.5, result);

  return result.min;
}

const DARK = `.nax {
  --nax-bg: #1e1e1e; --nax-text: #f2f2f2; --nax-muted: #c8c8c8; --nax-border: #a0a0a0;
  --nax-divider: #4a4a4a; --nax-hover: #2f2f2f; --nax-accent: #f2f2f2; --nax-on-accent: #1e1e1e;
  --nax-focus: #f2f2f2; --nax-toggle-bg: #f2f2f2; --nax-toggle-hover: #d0d0d0; --nax-toggle-color: #1e1e1e;
  --nax-toggle-ring: #1e1e1e;
}`;

// The debug toolbar of a development installation would cover the button.
const NO_TOOLBAR = () => {
  document.addEventListener('DOMContentLoaded', () => {
    const style = document.createElement('style');

    style.textContent = '.sf-toolbar, .sf-minitoolbar { display: none !important; }';
    document.head.appendChild(style);
  });
};

async function newPage(browser, viewport, init, arg) {
  const context = await browser.newContext({ viewport, deviceScaleFactor: 1, hasTouch: viewport.width < 600 });

  await context.addInitScript(NO_TOOLBAR);
  await context.addInitScript(init || SPEECH_STUB, arg);

  const page = await context.newPage();

  page.__errors = [];
  page.on('pageerror', (e) => page.__errors.push(String(e)));
  page.on('console', (m) => { if (m.type() === 'error' && m.text().indexOf('Failed to load resource') === -1) page.__errors.push(m.text()); });
  // Missing files are reported with their address (the console message has none).
  page.on('response', (r) => { if (r.status() >= 400 && !/\/_(wdt|profiler)\//.test(r.url())) page.__errors.push(r.status() + ' ' + r.url()); });

  return { context, page };
}

(async () => {
  fs.mkdirSync(SHOTS, { recursive: true });

  // Headless Chrome blocks autoplay without the flag; the test video must play by itself.
  const args = ['--no-sandbox', '--autoplay-policy=no-user-gesture-required'];
  const browser = await chromium.launch(process.env.NAX_CHROME ? { executablePath: process.env.NAX_CHROME, args } : { args });
  const { context, page } = await newPage(browser, { width: 1280, height: 900 });
  const fontRequests = [];

  page.on('request', (r) => { if (r.url().indexOf('OpenDyslexic') !== -1) fontRequests.push(r.url()); });

  await page.goto(PAGE_DE);
  await settle(page);

  // -- Start ------------------------------------------------------------------
  await page.waitForTimeout(300);
  check('test video plays by itself at start', await page.evaluate(() => !document.querySelector('.t-box video').paused));
  check('nothing stored before the visitor changes something', (await stored(page)) === null);
  check('no data-a11y attribute at start', (await htmlFlags(page)).length === 0, await htmlFlags(page));
  check('dyslexia font not loaded at start', fontRequests.length === 0, fontRequests);
  check('filter on html and body: none', await page.evaluate(() => getComputedStyle(document.documentElement).filter === 'none' && getComputedStyle(document.body).filter === 'none'));
  await shot(page, '00-before-desktop');

  // -- Panel structure ----------------------------------------------------------
  await open(page);
  const structure = await page.evaluate(() => ({
    title: document.querySelector('.nax__title').tagName,
    groups: Array.from(document.querySelectorAll('.nax__group-title')).map((h) => h.tagName + ':' + h.textContent.trim()),
    groupRoles: Array.from(document.querySelectorAll('.nax__group')).every((g) => g.getAttribute('role') === 'group' && document.getElementById(g.getAttribute('aria-labelledby'))),
    switches: Array.from(document.querySelectorAll('[data-nax-flag]')).filter((s) => s.getClientRects().length).map((s) => s.getAttribute('data-nax-flag')),
    roles: Array.from(document.querySelectorAll('[data-nax-flag]')).every((s) => s.getAttribute('role') === 'switch' && s.getAttribute('aria-checked') === 'false'),
    profiles: Array.from(document.querySelectorAll('[data-nax-profile]')).filter((p) => p.getClientRects().length).map((p) => p.getAttribute('data-nax-profile') + ':' + p.getAttribute('aria-pressed')),
    focus: document.activeElement && document.activeElement.hasAttribute('data-nax-close'),
  }));
  check('panel title is h2, groups are h3 headings', structure.title === 'H2' && structure.groups.length === 4 && structure.groups.every((g) => g.indexOf('H3:') === 0), structure.groups);
  check('groups have role=group with a heading as name', structure.groupRoles);
  check('17 switches visible (with speech output)', structure.switches.length === 17 && SWITCHES.every((s) => structure.switches.indexOf(s) !== -1), structure.switches);
  check('switches: role=switch, aria-checked=false', structure.roles);
  check('6 quick options, none pressed', structure.profiles.length === 6 && structure.profiles.every((p) => /:false$/.test(p)), structure.profiles);
  check('focus moves into the panel on open (close button)', structure.focus);
  await shot(page, '01-panel-open-desktop');
  await page.locator('[data-nax-flag="speak"]').scrollIntoViewIfNeeded();
  await shot(page, '01-panel-open-desktop-bottom');
  await axe(page, 'panel open, light');
  await panelContrast(page, 'light');

  // Hover state of a row and of a quick option
  await page.hover('[data-nax-flag="contrast"]');
  await panelContrast(page, 'light, row under the mouse');
  await page.hover('[data-nax-profile="vision"]');
  await panelContrast(page, 'light, quick option under the mouse');

  // -- Keyboard ------------------------------------------------------------------
  await page.keyboard.press('Escape');
  check('Esc closes the panel', await page.locator('[data-nax-panel]').isHidden());
  check('focus back on the button', await page.evaluate(() => document.activeElement.hasAttribute('data-nax-toggle')));
  await page.keyboard.press('Enter');
  check('Enter on the button opens the panel', await page.locator('[data-nax-panel]').isVisible());
  const trap = await page.evaluate(() => {
    const panel = document.querySelector('[data-nax-panel]');
    const items = Array.from(panel.querySelectorAll('button, a[href]')).filter((el) => el.getClientRects().length);

    return items.length;
  });

  for (let i = 0; i < trap + 2; i++) {
    await page.keyboard.press('Tab');
  }

  check('Tab stays inside the open panel (' + trap + ' controls, ' + (trap + 2) + ' presses)', await page.evaluate(() => document.querySelector('[data-nax-panel]').contains(document.activeElement)));
  await page.keyboard.press('Shift+Tab');
  check('Shift+Tab stays inside', await page.evaluate(() => document.querySelector('[data-nax-panel]').contains(document.activeElement)));

  // Space on a focused switch toggles it
  await page.focus('[data-nax-flag="links"]');
  await page.keyboard.press(' ');
  check('Space on a switch toggles it (links on)', (await page.getAttribute('[data-nax-flag="links"]', 'aria-checked')) === 'true');
  await page.keyboard.press('Enter');
  check('Enter on a switch toggles it (links off)', (await page.getAttribute('[data-nax-flag="links"]', 'aria-checked')) === 'false');
  check('storage written on change (and removed when nothing is on)', (await stored(page)) === null);

  // -- Text size ---------------------------------------------------------------------
  const steps = [];

  for (let i = 0; i < 4; i++) {
    // The fourth press hits the limit (aria-disabled, still focusable): forced.
    await page.click('[data-nax-font="1"]', { force: i === 3 });
    steps.push(await page.evaluate(() => [document.documentElement.getAttribute('data-a11y-font'), getComputedStyle(document.documentElement).fontSize, document.querySelector('[data-nax-fontval]').textContent]));
  }

  check('text size steps 115/130/150, stops at 150', steps.map((s) => s[0]).join(',') === '115,130,150,150', steps);
  check('root font size 24px at 150 %', steps[2][1] === '24px', steps[2]);
  check('A+ unavailable at 150 %', (await page.getAttribute('[data-nax-font="1"]', 'aria-disabled')) === 'true');
  await close(page);
  await shot(page, 'feature-font-desktop');
  await open(page);

  for (let i = 0; i < 3; i++) {
    await page.click('[data-nax-font="-1"]');
  }

  check('text size back to 100 %', (await page.evaluate(() => document.documentElement.getAttribute('data-a11y-font'))) === null);
  await reset(page);

  // -- Each switch ---------------------------------------------------------------------
  // Probes run with the setting on; returns [ok, detail].
  const probes = {
    typeface: () => { const p = getComputedStyle(document.querySelector('.t-box p')).fontFamily; const nav = getComputedStyle(document.querySelector('.nax__label')).fontFamily; return [p.indexOf('Verdana') === 0 && nav.indexOf('Verdana') === 0, p]; },
    dyslexia: () => { const p = getComputedStyle(document.querySelector('.t-box p')).fontFamily; return [p.indexOf('OpenDyslexic') !== -1, p]; },
    readable: () => { const p = getComputedStyle(document.querySelector('.t-box p')); return [p.lineHeight === (parseFloat(p.fontSize) * 1.5) + 'px' && p.letterSpacing !== 'normal', [p.lineHeight, p.letterSpacing, p.wordSpacing]]; },
    align: () => { const ps = document.querySelectorAll('.t-box p'); const a = getComputedStyle(ps[0]); const b = getComputedStyle(ps[1]); return [a.textAlign === 'start' && b.textAlign === 'start' && a.hyphens === 'manual', [a.textAlign, b.textAlign, a.hyphens]]; },
    headings: () => { const h = getComputedStyle(document.querySelector('.t-box h2')); return [h.outlineStyle === 'solid' && h.outlineWidth === '2px', [h.outlineStyle, h.outlineWidth]]; },
    links: () => { const a = getComputedStyle(document.querySelector('.t-box p a')); return [a.textDecorationLine === 'underline', a.textDecorationLine]; },
    noimages: () => { const img = document.querySelector('.t-box figure img'); const alt = img.nextElementSibling; const deco = document.querySelector('.t-box img[alt=""]'); return [getComputedStyle(img).display === 'none' && alt && alt.className === 'nax-alt' && alt.textContent.indexOf(img.alt) !== -1 && getComputedStyle(alt).display === 'inline-block' && !(deco.nextElementSibling && deco.nextElementSibling.className === 'nax-alt'), alt && alt.textContent]; },
    cursor: () => { const c = getComputedStyle(document.body).cursor; return [c.indexOf('url(') === 0, c.slice(0, 30)]; },
    contrast: () => { const b = getComputedStyle(document.querySelector('.t-fixed')); return [b.backgroundColor === 'rgb(255, 255, 255)' && b.color === 'rgb(0, 0, 0)', [b.backgroundColor, b.color]]; },
    lowcontrast: () => { const f = getComputedStyle(document.documentElement, '::after').backdropFilter; return [f.indexOf('contrast(0.8)') !== -1 && f.indexOf('brightness(0.92)') !== -1, f]; },
    lowsaturation: () => { const f = getComputedStyle(document.documentElement, '::after').backdropFilter; return [f.indexOf('saturate(0.4)') !== -1, f]; },
    grayscale: () => { const f = getComputedStyle(document.documentElement, '::after').backdropFilter; return [f.indexOf('grayscale(1)') !== -1 && getComputedStyle(document.documentElement).filter === 'none', f]; },
    motion: () => { const v = document.querySelector('.t-box video'); const anim = getComputedStyle(document.querySelector('.t-anim')); return [v.paused && v.hasAttribute('data-nax-paused') && parseFloat(anim.animationDuration) < 0.01, [v.paused, anim.animationDuration]]; },
    focus: () => [true, ''],
    guide: () => [!!document.querySelector('.nax__guide'), ''],
    mask: () => [document.querySelectorAll('.nax__mask').length === 2, ''],
    speak: () => [!document.querySelector('[data-nax-speak-controls]').hidden && document.querySelectorAll('[data-nax-speakable][tabindex="0"]').length > 3, document.querySelectorAll('[data-nax-speakable]').length],
  };

  await page.evaluate(() => {
    // An endless animation started by a script, for "stop animations".
    window.__waapi = document.querySelector('.t-waapi').animate([{ transform: 'translateX(0)' }, { transform: 'translateX(40px)' }], { duration: 800, iterations: Infinity });
  });

  for (const flag of SWITCHES) {
    await flip(page, flag);
    const flags = await htmlFlags(page);
    const checked = await page.getAttribute('[data-nax-flag="' + flag + '"]', 'aria-checked');
    check(flag + ': data-a11y-' + flag + ' set, aria-checked=true', flags.indexOf(flag) !== -1 && checked === 'true', flags);
    const [ok, detail] = await page.evaluate(probes[flag]);
    check(flag + ': effect on the page', ok, detail);

    if (flag === 'dyslexia') {
      await page.waitForTimeout(400);
      check('dyslexia: font file requested only now', fontRequests.length >= 1, fontRequests);
    }

    if (flag === 'motion') {
      const waapi = await page.evaluate(() => window.__waapi.playState);
      check('motion: endless script animation cancelled', waapi === 'idle', waapi);
      const instant = await page.evaluate(() => { window.scrollTo({ top: 300, behavior: 'smooth' }); const y = window.scrollY; window.scrollTo(0, 0); return y; });
      check('motion: smooth scrolling by scripts becomes instant', instant === 300, instant);
    }

    if (['lowcontrast', 'lowsaturation', 'grayscale'].indexOf(flag) !== -1) {
      await close(page);
      await page.evaluate(() => window.scrollTo(0, 600));
      const top = await page.evaluate(() => Math.round(document.querySelector('.t-fixed').getBoundingClientRect().top));
      check(flag + ': fixed element stays fixed while scrolling (top ' + top + ')', top === 0, top);
      await page.evaluate(() => window.scrollTo(0, 0));
    }

    if (flag === 'focus') {
      await close(page);
      await page.focus('.t-box nav a');
      await page.keyboard.press('Tab');
      await page.keyboard.press('Shift+Tab');
      const outline = await page.evaluate(() => { const s = getComputedStyle(document.activeElement); return s.outlineWidth + ' ' + s.outlineStyle; });
      check('focus: 3px frame on the focused link', outline === '3px solid', outline);
    }

    await close(page);

    if (flag === 'guide' || flag === 'mask') {
      await page.mouse.move(400, 330);
      await page.waitForTimeout(100);
    }

    if (flag === 'speak') {
      await page.mouse.move(640, 450);
    }

    await shot(page, 'feature-' + flag + '-desktop');
    await reset(page);
    const after = await htmlFlags(page);
    check(flag + ': off again after reset', after.length === 0, after);
  }

  const resumed = await page.evaluate(() => [document.querySelector('.t-box video').paused, window.__waapi.playState]);
  check('motion off: video and endless animation run again', resumed[0] === false && resumed[1] === 'running', resumed);
  check('noimages off: description elements removed', await page.evaluate(() => document.querySelectorAll('.nax-alt').length === 0));
  check('speak off: blocks no longer focusable', await page.evaluate(() => document.querySelectorAll('[data-nax-speakable]').length === 0));

  // -- Colour modes: axe and panel contrast ------------------------------------------------
  for (const flag of ['contrast', 'lowcontrast', 'lowsaturation', 'grayscale']) {
    await flip(page, flag);
    await axe(page, 'panel open, ' + flag, true);
    await panelContrast(page, flag);
    await shot(page, 'colour-' + flag + '-panel-desktop');
    await reset(page);
  }

  // -- Exclusive pairs -----------------------------------------------------------------------
  await flip(page, 'guide');
  await page.click('[data-nax-flag="mask"]');
  const pair = await page.evaluate(() => [document.querySelector('[data-nax-flag="guide"]').getAttribute('aria-checked'), document.querySelector('[data-nax-flag="mask"]').getAttribute('aria-checked')]);
  check('mask on switches guide off', pair[0] === 'false' && pair[1] === 'true', pair);
  await page.waitForTimeout(200);
  const said = await page.textContent('[data-nax-status]');
  check('status says which setting went off', said.indexOf('Leselinie') !== -1, said);
  await page.click('[data-nax-flag="contrast"]');
  await page.click('[data-nax-flag="lowcontrast"]');
  check('reduce contrast switches high contrast off', (await page.getAttribute('[data-nax-flag="contrast"]', 'aria-checked')) === 'false');
  await page.click('[data-nax-flag="typeface"]');
  await page.click('[data-nax-flag="dyslexia"]');
  check('dyslexia font switches readable font off', (await page.getAttribute('[data-nax-flag="typeface"]', 'aria-checked')) === 'false');
  await reset(page);

  // -- Reading guide and mask: pointer and keyboard ---------------------------------------------
  await flip(page, 'guide');
  await close(page);
  await page.mouse.move(300, 400);
  await page.waitForTimeout(100);
  let y = await page.evaluate(() => Math.round(document.querySelector('.nax__guide').getBoundingClientRect().top));
  check('guide follows the pointer (pointer 400, line ' + y + ')', y === 412, y);
  await shot(page, 'guide-pointer-desktop');
  await page.focus('.t-box nav a');
  await page.waitForTimeout(100);
  const below = await page.evaluate(() => [Math.round(document.querySelector('.nax__guide').getBoundingClientRect().top), Math.round(document.activeElement.getBoundingClientRect().bottom)]);
  check('guide follows the keyboard focus (under the focused link)', below[0] === below[1] + 4, below);
  await shot(page, 'guide-keyboard-desktop');
  await reset(page);

  await flip(page, 'mask');
  await close(page);
  await page.mouse.move(300, 450);
  await page.waitForTimeout(100);
  const mask = await page.evaluate(() => Array.from(document.querySelectorAll('.nax__mask')).map((m) => { const b = m.getBoundingClientRect(); return [Math.round(b.top), Math.round(b.bottom)]; }));
  check('mask leaves a strip around the pointer (top ends at ' + mask[0][1] + ', bottom starts at ' + mask[1][0] + ')', mask[0][1] < 450 && mask[1][0] > 450 && mask[1][0] - mask[0][1] === 112, mask);
  await shot(page, 'mask-pointer-desktop');
  await page.focus('#t-two-h');
  await page.evaluate(() => document.querySelector('#t-two-h').setAttribute('tabindex', '-1'));
  await page.focus('#t-two-h');
  await page.waitForTimeout(100);
  const around = await page.evaluate(() => { const h = document.activeElement.getBoundingClientRect(); const m = Array.from(document.querySelectorAll('.nax__mask')).map((x) => x.getBoundingClientRect()); return [m[0].bottom <= h.top, m[1].top >= h.bottom]; });
  check('mask strip holds the focused element', around[0] && around[1], around);
  await shot(page, 'mask-keyboard-desktop');
  check('mask does not catch the mouse', await page.evaluate(() => getComputedStyle(document.querySelector('.nax__mask')).pointerEvents === 'none'));
  await reset(page);

  // -- Page structure ------------------------------------------------------------------------------
  await open(page);
  await page.locator('[data-nax-structure]').scrollIntoViewIfNeeded();
  await page.click('[data-nax-structure]');
  const list = await page.evaluate(() => ({
    expanded: document.querySelector('[data-nax-structure]').getAttribute('aria-expanded'),
    items: Array.from(document.querySelectorAll('.nax__jump')).map((b) => b.textContent.trim()),
  }));
  check('structure: list opens (aria-expanded=true)', list.expanded === 'true');
  const wantAreas = ['Hauptinhalt', 'Navigation: Inhalt', 'Abschnitt: Einleitung', 'Zusatzinformationen: Randnotiz', 'Formular: Kontakt', 'Abschnitt: Zweiter Abschnitt'];
  check('structure: areas listed', wantAreas.every((a) => list.items.indexOf(a) !== -1), list.items);
  check('structure: headings listed with level', list.items.some((t) => t === 'Ebene 1: Testseite Barrierefreiheit') && list.items.some((t) => t === 'Ebene 3: Ein Unterabschnitt'), list.items);
  check('structure: panel headings not listed', !list.items.some((t) => t.indexOf('Schnellauswahl') !== -1));
  await page.locator('.nax__structure').scrollIntoViewIfNeeded();
  await shot(page, 'structure-open-desktop');
  await page.click('.nax__jump:has-text("Ebene 2: Zweiter Abschnitt")');
  await page.waitForTimeout(700);
  const jumped = await page.evaluate(() => ({ id: document.activeElement.id, closed: document.querySelector('[data-nax-panel]').hidden, top: Math.round(document.activeElement.getBoundingClientRect().top), vh: innerHeight }));
  check('structure: jump closes the panel and focuses the heading in view', jumped.id === 't-two-h' && jumped.closed && jumped.top >= 0 && jumped.top < jumped.vh, jumped);
  await shot(page, 'structure-jump-desktop');
  await reset(page);

  // -- Read aloud ----------------------------------------------------------------------------------
  await flip(page, 'speak');
  await close(page);
  await page.click('.t-box section p');
  let speech = await page.evaluate(() => ({ log: window.__speech.spoken.slice(-1)[0], reading: document.querySelector('.nax-reading') && document.querySelector('.nax-reading').textContent.slice(0, 20) }));
  check('click on a paragraph reads it', speech.log && speech.log.text.indexOf('Blocksatz') !== -1, speech);
  check('voice of the page language, device voice first', speech.log && speech.log.voice === 'Device German' && speech.log.lang === 'de-DE', speech.log);
  check('block being read is highlighted', !!speech.reading, speech.reading);
  await shot(page, 'speak-reading-desktop');
  await page.evaluate(() => window.speechSynthesis.finish());
  check('highlight removed when done', await page.evaluate(() => !document.querySelector('.nax-reading')));
  // A link inside a paragraph: the first click reads the text under the
  // pointer, the second uses the link (the test keeps the page from leaving).
  await page.evaluate(() => { window.__used = 0; document.querySelector('.t-box p a').addEventListener('click', (e) => { e.preventDefault(); window.__used++; }); });
  await page.click('.t-box p a');
  const first = await page.evaluate(() => ({ spoken: window.__speech.spoken.length, used: window.__used, hint: !!document.querySelector('.nax__hint--shown') }));
  check('first click on a link reads, shows the hint and does not use it', first.spoken === 2 && first.used === 0 && first.hint, first);
  await page.click('.t-box p a');
  check('second click on the link uses it', (await page.evaluate(() => window.__used)) === 1);
  await page.evaluate(() => window.scrollTo(0, 0));
  await page.focus('.t-box h1');
  await page.keyboard.press('Tab');
  const focused = await page.evaluate(() => document.activeElement.hasAttribute('data-nax-speakable') ? document.activeElement.tagName : document.activeElement.tagName + ' (not speakable)');
  await page.evaluate(() => { const el = document.querySelector('.t-box section p'); el.focus(); });
  await page.keyboard.press('Enter');
  speech = await page.evaluate(() => window.__speech.spoken.slice(-1)[0]);
  check('Enter on a focused block reads it (Tab reaches blocks: ' + focused + ')', speech.text.indexOf('Blocksatz') !== -1, speech);
  await page.evaluate(() => window.speechSynthesis.finish());
  await open(page);
  await page.locator('[data-nax-speak="page"]').scrollIntoViewIfNeeded();
  const before = await page.evaluate(() => window.__speech.spoken.length);
  await page.click('[data-nax-speak="page"]');
  await page.evaluate(() => window.speechSynthesis.finish());
  await page.evaluate(() => window.speechSynthesis.finish());
  speech = await page.evaluate((n) => window.__speech.spoken.slice(n).map((s) => s.text.slice(0, 25)), before);
  check('"Read page" reads the main content block by block, in order', speech.length === 3 && speech[0].indexOf('Testseite') === 0 && speech[1].indexOf('Erster Abschnitt') === 0, speech);
  await page.click('[data-nax-speak="pause"]');
  const pausedLabel = await page.textContent('[data-nax-speak="pause"] [data-nax-speak-label]');
  check('Pause pauses, button says "Weiter"', (await page.evaluate(() => window.__speech.paused)) === 1 && pausedLabel.trim() === 'Weiter', pausedLabel);
  await page.click('[data-nax-speak="pause"]');
  check('Continue resumes', (await page.evaluate(() => window.__speech.resumed)) === 1);
  await shot(page, 'speak-controls-desktop');
  await page.click('[data-nax-speak="stop"]');
  check('Stop cancels and clears the highlight', await page.evaluate(() => window.__speech.cancelled > 0 && !document.querySelector('.nax-reading')));
  check('Pause and Stop unavailable when idle', (await page.getAttribute('[data-nax-speak="stop"]', 'aria-disabled')) === 'true');
  await close(page);
  await page.click('.t-box blockquote p');
  await page.keyboard.press('Escape');
  check('Esc (panel closed) stops reading', await page.evaluate(() => !document.querySelector('.nax-reading')));
  await reset(page);

  // -- Quick options --------------------------------------------------------------------------------
  for (const [key, set] of Object.entries(PROFILES)) {
    await open(page);
    await page.click('[data-nax-profile="' + key + '"]');
    const on = await htmlFlags(page);
    const pressed = await page.getAttribute('[data-nax-profile="' + key + '"]', 'aria-pressed');
    const want = Object.keys(set).map((k) => (k === 'font' ? 'font=' + set[k] : k)).sort();
    check('quick option ' + key + ' switches ' + want.join(', '), JSON.stringify(on) === JSON.stringify(want) && pressed === 'true', on);
    await close(page);
    await page.mouse.move(400, 330);
    await shot(page, 'profile-' + key + '-desktop');

    if (key === 'vision') {
      const counter = await page.evaluate(() => [document.querySelector('[data-nax-count]').textContent, getComputedStyle(document.querySelector('[data-nax-count]')).display, document.querySelector('[data-nax-toggle]').getAttribute('aria-label')]);
      check('counter on the button shows 4, label says it', counter[0] === '4' && counter[1] === 'flex' && counter[2].indexOf('4 eingeschaltet') !== -1, counter);
      await page.screenshot({ path: path.join(SHOTS, 'counter-desktop.png'), clip: { x: 0, y: 800, width: 120, height: 100 } });
    }

    await open(page);
    await page.click('[data-nax-profile="' + key + '"]');
    check('quick option ' + key + ' chosen again: its settings off', (await htmlFlags(page)).length === 0, await htmlFlags(page));
    await close(page);
  }

  await open(page);
  await page.click('[data-nax-flag="cursor"]');
  await page.click('[data-nax-flag="focus"]');
  check('quick option pressed when all its settings are on by hand (motor)', (await page.getAttribute('[data-nax-profile="motor"]', 'aria-pressed')) === 'true');
  await reset(page);

  // -- Stored settings ------------------------------------------------------------------------------------
  await open(page);
  await page.click('[data-nax-profile="dyslexia"]');
  await page.click('[data-nax-font="1"]');
  await close(page);
  await page.reload();
  const early = await page.evaluate(() => document.documentElement.getAttribute('data-a11y-dyslexia') !== null);
  await page.waitForSelector('.nax:not([hidden])');
  const restored = await page.evaluate(() => ({ flags: Array.from(document.documentElement.attributes).map((a) => a.name).filter((n) => n.indexOf('data-a11y-') === 0).sort(), checked: Array.from(document.querySelectorAll('[data-nax-flag][aria-checked="true"]')).map((s) => s.getAttribute('data-nax-flag')).sort(), stored: localStorage.getItem('nax-settings') }));
  check('settings kept after reload (head script and panel)', early && restored.flags.length === 4 && restored.checked.join() === 'align,dyslexia,readable', restored);
  await shot(page, 'reload-kept-desktop');
  await page.evaluate(() => localStorage.setItem('nax-settings', JSON.stringify({ font: 145, readable: true, motion: true })));
  await page.reload();
  await page.waitForSelector('.nax:not([hidden])');
  const legacy = await page.evaluate(() => [document.documentElement.getAttribute('data-a11y-font'), document.querySelector('[data-nax-fontval]').textContent, document.documentElement.hasAttribute('data-a11y-readable')]);
  check('stored value of the first version (145 %) becomes 150 %', legacy[0] === '150' && legacy[1].indexOf('150') === 0 && legacy[2], legacy);
  await reset(page);
  check('reset removes the stored entry', (await stored(page)) === null);
  check('no script errors on the desktop page', page.__errors.length === 0, page.__errors);

  // -- Dark design through the variables -----------------------------------------------------------------
  await page.addStyleTag({ content: DARK });
  await open(page);
  await page.click('[data-nax-profile="motor"]');
  await axe(page, 'panel open, dark variables', true);
  await panelContrast(page, 'dark');
  await page.hover('[data-nax-flag="contrast"]');
  await panelContrast(page, 'dark, row under the mouse');
  await shot(page, 'panel-dark-desktop');
  await reset(page);
  await context.close();

  // -- Phone (390 px) -------------------------------------------------------------------------------------------
  const phone = await newPage(browser, { width: 390, height: 844 });

  await phone.page.goto(PAGE_DE);
  await settle(phone.page);
  await shot(phone.page, '00-before-phone');
  await open(phone.page);
  await shot(phone.page, '01-panel-open-phone');
  const targets = await phone.page.evaluate(() => Array.from(document.querySelectorAll('[data-nax-panel] button, [data-nax-panel] a[href]')).filter((el) => el.getClientRects().length).map((el) => { const b = el.getBoundingClientRect(); return [el.className || el.getAttribute('data-nax-flag'), Math.round(b.width), Math.round(b.height)]; }).filter((t) => t[1] < 44 || t[2] < 44));
  check('phone: every control at least 44 x 44 px', targets.length === 0, targets);
  await axe(phone.page, 'phone, panel open', true);

  for (const flag of SWITCHES.concat(['font'])) {
    if (flag === 'font') {
      await open(phone.page);
      await phone.page.click('[data-nax-font="1"]');
      await phone.page.click('[data-nax-font="1"]');
    } else {
      await flip(phone.page, flag);
    }

    await close(phone.page);
    await shot(phone.page, 'feature-' + flag + '-phone');
    await reset(phone.page);
  }

  for (const key of Object.keys(PROFILES)) {
    await open(phone.page);
    await phone.page.click('[data-nax-profile="' + key + '"]');
    await close(phone.page);
    await shot(phone.page, 'profile-' + key + '-phone');
    await reset(phone.page);
  }

  check('no script errors on the phone page', phone.page.__errors.length === 0, phone.page.__errors);
  await phone.context.close();

  // -- 320 px ------------------------------------------------------------------------------------------------------
  const narrow = await newPage(browser, { width: 320, height: 640 });

  await narrow.page.goto(PAGE_DE);
  await settle(narrow.page);
  await open(narrow.page);
  const fit = await narrow.page.evaluate(() => { const p = document.querySelector('[data-nax-panel]'); const b = p.getBoundingClientRect(); return { left: Math.round(b.left), right: Math.round(b.right), overflow: p.scrollWidth - p.clientWidth, doc: document.documentElement.scrollWidth }; });
  check('320 px: panel inside the screen, no sideways scrolling', fit.left >= 0 && fit.right <= 320 && fit.overflow <= 0 && fit.doc <= 320, fit);
  await shot(narrow.page, '01-panel-open-320');
  await narrow.context.close();

  // -- Without speech output -------------------------------------------------------------------------------------
  const mute = await newPage(browser, { width: 1280, height: 900 }, NO_SPEECH);

  await mute.page.goto(PAGE_DE);
  await settle(mute.page);
  await open(mute.page);
  const mute1 = await mute.page.evaluate(() => ({
    switchShown: !!document.querySelector('[data-nax-flag="speak"]').getClientRects().length,
    disabled: document.querySelector('[data-nax-flag="speak"]').getAttribute('aria-disabled'),
    reason: document.querySelector('#' + document.querySelector('[data-nax]').id + '-speak-reason').textContent,
    profile: document.querySelector('[data-nax-profile="reader"]').getAttribute('aria-disabled'),
  }));
  check('without speech output: read aloud and "Vorlesen" disabled with the reason', mute1.switchShown && mute1.disabled === 'true' && mute1.profile === 'true' && mute1.reason === 'Dieser Browser kann nicht vorlesen.', mute1);
  await mute.page.evaluate(() => localStorage.setItem('nax-settings', JSON.stringify({ speak: true, links: true })));
  await mute.page.reload();
  await mute.page.waitForSelector('.nax:not([hidden])');
  const muteCount = await mute.page.evaluate(() => [document.querySelector('[data-nax-count]').textContent, document.documentElement.hasAttribute('data-a11y-speak')]);
  check('without speech output: stored read aloud neither applied nor counted', muteCount[0] === '1' && !muteCount[1], muteCount);
  check('no script errors without speech output', mute.page.__errors.length === 0, mute.page.__errors);
  await mute.context.close();

  // -- Read aloud: only voices of the device ---------------------------------------------------------------------
  // The browser has an online German voice and a device voice for English only.
  for (const [width, name] of [[1280, 'desktop'], [390, 'phone']]) {
    const nv = await newPage(browser, { width, height: width < 600 ? 844 : 900 }, SPEECH_STUB, { voices: ['Online German', 'Device English'] });

    await nv.page.goto(PAGE_DE);
    await settle(nv.page);
    await nv.page.evaluate(() => localStorage.setItem('nax-settings', JSON.stringify({ speak: true, links: true })));
    await nv.page.reload();
    await nv.page.waitForSelector('.nax:not([hidden])');
    await open(nv.page);
    await nv.page.locator('[data-nax-flag="speak"]').scrollIntoViewIfNeeded();
    const state = await nv.page.evaluate(() => {
      const uid = document.querySelector('[data-nax]').id;
      const sw = document.querySelector('[data-nax-flag="speak"]');
      const reason = document.getElementById(uid + '-speak-reason');
      const profileReason = document.getElementById(uid + '-profile-reader-reason');

      return {
        disabled: sw.getAttribute('aria-disabled'), checked: sw.getAttribute('aria-checked'),
        reason: reason.hidden ? null : reason.textContent, described: sw.getAttribute('aria-describedby').indexOf(uid + '-speak-reason') !== -1,
        profile: document.querySelector('[data-nax-profile="reader"]').getAttribute('aria-disabled'),
        profileReason: profileReason.hidden ? null : profileReason.textContent,
        attr: document.documentElement.hasAttribute('data-a11y-speak'),
        count: document.querySelector('[data-nax-count]').textContent,
        controls: document.querySelector('[data-nax-speak-controls]').hidden,
        speakable: document.querySelectorAll('[data-nax-speakable]').length,
      };
    });

    if (name === 'desktop') {
      check('no device voice for German: switch disabled, not on', state.disabled === 'true' && state.checked === 'false', state);
      check('no device voice: reason next to the switch, in the visitor\'s language', state.reason === 'Auf diesem Gerät ist keine Stimme für Deutsch installiert.' && state.described, state.reason);
      check('no device voice: quick option "Vorlesen" disabled with the reason', state.profile === 'true' && state.profileReason === 'Vorlesen: Auf diesem Gerät ist keine Stimme für Deutsch installiert.', state);
      check('no device voice: stored read aloud not applied, not counted, no controls, no marked texts', !state.attr && state.count === '1' && state.controls && state.speakable === 0, state);
      await nv.page.click('[data-nax-flag="speak"]', { force: true });
      await nv.page.click('[data-nax-profile="reader"]', { force: true });
      check('no device voice: clicks on the disabled switch and quick option change nothing', (await nv.page.getAttribute('[data-nax-flag="speak"]', 'aria-checked')) === 'false' && (await stored(nv.page)) === JSON.stringify({ speak: true, links: true }));
      await close(nv.page);
      await nv.page.click('.t-box section p');
      check('no device voice: nothing is spoken (no online voice, no unset voice)', (await nv.page.evaluate(() => window.__speech.spoken.length)) === 0);
      await open(nv.page);
      await nv.page.locator('[data-nax-flag="speak"]').scrollIntoViewIfNeeded();
      await axe(nv.page, 'read aloud disabled', true);
      await panelContrast(nv.page, 'read aloud disabled (reason texts)');
    }

    await nv.page.locator('[data-nax-flag="speak"]').scrollIntoViewIfNeeded();
    await shot(nv.page, 'speak-disabled-' + name);
    await nv.page.locator('[data-nax-profile="reader"]').scrollIntoViewIfNeeded();
    await shot(nv.page, 'speak-disabled-profile-' + name);
    check('no script errors, read aloud disabled (' + name + ')', nv.page.__errors.length === 0, nv.page.__errors);

    if (name === 'desktop') {
      // Same browser, English page: the device has an English voice.
      await nv.page.goto(PAGE_EN);
      await nv.page.waitForSelector('.nax:not([hidden])');
      const en = await nv.page.evaluate(() => [document.querySelector('[data-nax-flag="speak"]').getAttribute('aria-disabled'), document.querySelector('[data-nax-flag="speak"]').getAttribute('aria-checked')]);
      check('same device, English page: read aloud usable (device voice for English)', en[0] === null && en[1] === 'true', en);
      await nv.page.click('.t-box section p');
      const voice = await nv.page.evaluate(() => window.__speech.spoken.slice(-1)[0]);
      check('English page: read with the device voice', voice && voice.voice === 'Device English', voice);
    }

    await nv.context.close();
  }

  // -- Read aloud: online voices allowed by the module ----------------------------------------------------------------
  const online = await newPage(browser, { width: 1280, height: 900 }, SPEECH_STUB, { voices: ['Online German', 'Device English'], allowOnline: true });

  await online.page.goto(PAGE_DE);
  await settle(online.page);
  check('online voices allowed: the page says so (data-nax-voices="any")', (await online.page.getAttribute('[data-nax]', 'data-nax-voices')) === 'any');
  await flip(online.page, 'speak');
  check('online voices allowed: read aloud usable', (await online.page.getAttribute('[data-nax-flag="speak"]', 'aria-checked')) === 'true' && !(await online.page.getAttribute('[data-nax-flag="speak"]', 'aria-disabled')));
  await close(online.page);
  await online.page.click('.t-box section p');
  const onlineVoice = await online.page.evaluate(() => window.__speech.spoken.slice(-1)[0]);
  check('online voices allowed: the online voice reads when the device has none', onlineVoice && onlineVoice.voice === 'Online German', onlineVoice);
  await online.context.close();

  const both = await newPage(browser, { width: 1280, height: 900 }, SPEECH_STUB, { allowOnline: true });

  await both.page.goto(PAGE_DE);
  await settle(both.page);
  await flip(both.page, 'speak');
  await close(both.page);
  await both.page.click('.t-box section p');
  const bothVoice = await both.page.evaluate(() => window.__speech.spoken.slice(-1)[0]);
  check('online voices allowed: a device voice still comes first', bothVoice && bothVoice.voice === 'Device German', bothVoice);
  await both.context.close();

  // -- Read aloud: voices reported late, or never -----------------------------------------------------------------
  const late = await newPage(browser, { width: 1280, height: 900 }, SPEECH_STUB, { lateMs: 1200 });

  await late.page.goto(PAGE_DE);
  await settle(late.page);
  await flip(late.page, 'speak');
  await close(late.page);
  await late.page.click('.t-box section p');
  const beforeVoices = await late.page.evaluate(() => window.__speech.spoken.length);
  check('voices reported late: nothing spoken before they are known', beforeVoices === 0, beforeVoices);
  await late.page.waitForTimeout(1500);
  const lateSpoken = await late.page.evaluate(() => window.__speech.spoken);
  check('voices reported late: the waiting text is read once they are known, with the device voice', lateSpoken.length === 1 && lateSpoken[0].voice === 'Device German' && lateSpoken[0].text.indexOf('Blocksatz') !== -1, lateSpoken);
  check('voices reported late: switch never disabled', !(await late.page.getAttribute('[data-nax-flag="speak"]', 'aria-disabled')));
  await late.context.close();

  const never = await newPage(browser, { width: 1280, height: 900 }, SPEECH_STUB, { lateMs: -1 });

  await never.page.goto(PAGE_DE);
  await settle(never.page);
  await flip(never.page, 'speak');
  await close(never.page);
  await never.page.click('.t-box section p');
  await never.page.waitForTimeout(3000);
  const neverState = await never.page.evaluate(() => [window.__speech.spoken.length, document.querySelector('[data-nax-flag="speak"]').getAttribute('aria-disabled'), document.querySelector('#' + document.querySelector('[data-nax]').id + '-speak-reason').textContent]);
  check('voices never reported: after the wait nothing spoken, switch disabled with the reason', neverState[0] === 0 && neverState[1] === 'true' && neverState[2].indexOf('Deutsch') !== -1, neverState);
  await never.context.close();

  // -- Focus back to the trigger, several runs ----------------------------------------------------------------------
  const fc = await newPage(browser, { width: 1280, height: 900 });

  await fc.page.goto(PAGE_DE);
  await settle(fc.page);
  // A visible link that opens the panel, and a menu like a speed dial: its
  // entry moves the focus to the menu button and fades out (0.4 s) when chosen.
  await fc.page.evaluate(() => {
    const style = document.createElement('style');

    style.textContent = '.t-dial{position:fixed;right:20px;bottom:20px;z-index:100}.t-act{transition:opacity .4s,visibility 0s .4s}.t-dial.t-collapsed .t-act{opacity:0;visibility:hidden}';
    document.head.appendChild(style);

    const box = document.querySelector('.t-box');
    const link = document.createElement('a');

    link.href = '#';
    link.setAttribute('role', 'button');
    link.setAttribute('data-nax-open', '');
    link.className = 't-link';
    link.textContent = 'Barrierefreiheit (Link)';
    box.insertBefore(link, box.firstChild);

    const dial = document.createElement('div');

    dial.className = 't-dial';
    dial.innerHTML = '<button type="button" class="t-menu">Menü</button> <button type="button" class="t-act" data-nax-open>Barrierefreiheit</button>';
    document.body.appendChild(dial);
    dial.querySelector('.t-act').addEventListener('click', () => {
      dial.classList.add('t-collapsed');
      dial.querySelector('.t-menu').focus();
    });
    dial.querySelector('.t-menu').addEventListener('click', () => dial.classList.remove('t-collapsed'));
  });

  const activeIs = (selector) => fc.page.evaluate((sel) => document.activeElement && document.activeElement.matches(sel), selector);
  const runs = [];

  for (let run = 1; run <= 3; run++) {
    await fc.page.focus('[data-nax-toggle]');
    await fc.page.keyboard.press('Enter');
    await fc.page.keyboard.press('Escape');
    await fc.page.waitForTimeout(50);
    runs.push(['toggle+Esc', run, await activeIs('[data-nax-toggle]')]);

    await fc.page.click('[data-nax-toggle]');
    await fc.page.click('[data-nax-close]');
    await fc.page.waitForTimeout(50);
    runs.push(['toggle+close', run, await activeIs('[data-nax-toggle]')]);

    await fc.page.focus('.t-link');
    await fc.page.keyboard.press('Enter');
    await fc.page.keyboard.press('Escape');
    await fc.page.waitForTimeout(50);
    runs.push(['link+Esc', run, await activeIs('.t-link')]);

    // Menu entry fading out: Esc at once (mid-fade), after 150 ms, and the close button after the fade.
    for (const [label, delay, how] of [['menu+Esc at once', 0, 'Escape'], ['menu+Esc 150 ms', 150, 'Escape'], ['menu+close after fade', 600, 'close']]) {
      await fc.page.click('.t-menu');
      await fc.page.waitForTimeout(450);
      await fc.page.click('.t-act');
      await fc.page.waitForTimeout(delay);

      if (how === 'close') {
        await fc.page.click('[data-nax-close]');
      } else {
        await fc.page.keyboard.press('Escape');
      }

      await fc.page.waitForTimeout(900);
      runs.push([label, run, await activeIs('.t-menu')]);
    }
  }

  const failedRuns = runs.filter((r) => !r[2]);
  check('focus back to the trigger (or the menu button) in ' + runs.length + ' closings (toggle, link, fading menu; Esc and close button; 3 runs)', failedRuns.length === 0, failedRuns);
  check('no script errors in the focus runs', fc.page.__errors.length === 0, fc.page.__errors);
  await fc.context.close();

  // -- English page ------------------------------------------------------------------------------------------------
  const en = await newPage(browser, { width: 1280, height: 900 });

  await en.page.goto(PAGE_EN);
  await settle(en.page);
  await open(en.page);
  const enTexts = await en.page.evaluate(() => [document.querySelector('.nax__title').textContent, document.querySelector('[data-nax-profile="vision"]').textContent.trim()]);
  check('English page: English texts', enTexts[0] === 'Accessibility' && enTexts[1] === 'Low vision', enTexts);
  await shot(en.page, '01-panel-open-desktop-en');
  await en.page.click('[data-nax-flag="speak"]');
  await close(en.page);
  await en.page.click('.t-box section p');
  const enVoice = await en.page.evaluate(() => window.__speech.spoken.slice(-1)[0]);
  check('English page: English voice', enVoice && enVoice.voice === 'Device English', enVoice);
  await reset(en.page);
  await en.context.close();

  await browser.close();
  console.log('\n' + passes + ' passed, ' + failures + ' failed');
  process.exit(failures ? 1 : 0);
})().catch((e) => {
  console.error(e);
  process.exit(2);
});

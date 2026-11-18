/*
 * Takes the screenshots of the documentation (docs/<lang>/bilder/) from a
 * Contao test installation with the test pages of tests/e2e (README.md there)
 * and marks them with the annotation layer of the neo-doku plugin.
 *
 *   NAX_ADMIN_USER=… NAX_ADMIN_PASSWORD=… node tools/docs-images/capture.cjs
 *
 * Environment: NAX_BASE (default http://127.0.0.1:8099), NAX_ANNOTATE (path of
 * annotate.js, default ../NEO-Claude-Plugins/plugins/neo-doku/scripts/annotate.js
 * next to this repository), NAX_CHROME, NAX_PLAYWRIGHT (as in tests/e2e),
 * NAX_LANG (only this language: en or de), NAX_PAGE_EN and NAX_PAGE_DE (other
 * pages than the test pages), NAX_IMAGES (only these images, comma separated:
 * panel, structure, speak, speak-disabled, mask, phone, module-settings, menu). For the
 * image of the back-end form,
 * NAX_ADMIN_USER / NAX_ADMIN_PASSWORD (an administrator whose back-end language
 * is the language captured) and NAX_MODULE (ID of the panel module; it is only
 * opened, nothing is saved).
 */
'use strict';

const fs = require('fs');
const path = require('path');
const { chromium } = require(process.env.NAX_PLAYWRIGHT || 'playwright');

const ROOT = path.resolve(__dirname, '..', '..');
const BASE = (process.env.NAX_BASE || 'http://127.0.0.1:8099').replace(/\/$/, '');
const ANNOTATE = process.env.NAX_ANNOTATE || path.resolve(ROOT, '..', 'NEO-Claude-Plugins/plugins/neo-doku/scripts/annotate.js');
const USER = process.env.NAX_ADMIN_USER;
const PASSWORD = process.env.NAX_ADMIN_PASSWORD;
const MODULE = process.env.NAX_MODULE || '';
const ONLY = process.env.NAX_LANG || '';
const IMAGES = (process.env.NAX_IMAGES || '').split(',').filter(Boolean);

const LANGS = {
  en: { page: process.env.NAX_PAGE_EN || BASE + '/en/a11y-test' },
  de: { page: process.env.NAX_PAGE_DE || BASE + '/a11y-test' },
};

function want(image) {
  return !IMAGES.length || IMAGES.indexOf(image) !== -1;
}

// Speech output replaced by a recorder, so "read aloud" can be shown without
// sound: device voices for German and English, or (argument "none") only a
// voice of another language, for the image of read aloud greyed out.
const SPEECH = (variant) => {
  const voices = variant === 'none'
    ? [{ name: 'Device French', lang: 'fr-FR', localService: true }]
    : [{ name: 'Device German', lang: 'de-DE', localService: true }, { name: 'Device English', lang: 'en-GB', localService: true }];
  const synth = { getVoices: () => voices, addEventListener: () => {}, speak(u) { this.u = u; }, cancel() {}, pause() {}, resume() {} };
  Object.defineProperty(window, 'speechSynthesis', { value: synth, configurable: true });
  window.SpeechSynthesisUtterance = function (text) { this.text = text; };
};

const QUIET = () => {
  document.addEventListener('DOMContentLoaded', () => {
    const style = document.createElement('style');

    style.textContent = '.sf-toolbar, .sf-minitoolbar { display: none !important; }';
    document.head.appendChild(style);
  });
};

async function context(browser, viewport, voices) {
  const ctx = await browser.newContext({ viewport, deviceScaleFactor: 1 });

  await ctx.addInitScript(QUIET);
  await ctx.addInitScript(SPEECH, voices || 'device');

  return ctx;
}

async function page(ctx, url) {
  const p = await ctx.newPage();

  await p.goto(url);
  // Attached is enough: as an entry of the menu of the settings button (NEO Basic) the widget takes no room.
  await p.waitForSelector('.nax:not([hidden])', { state: 'attached' });

  const banner = p.locator('[data-ncc-action="necessary"]');

  if (await banner.count() && await banner.first().isVisible()) {
    await banner.first().click();
  }

  return p;
}

// Opens the panel from the floating button, or from the menu of the settings
// button where other buttons share its corner (NEO Basic).
async function openPanel(p) {
  const settings = p.locator('[data-neo-dock-button]:not([hidden])');

  if (await settings.count() && await settings.first().isVisible()) {
    await settings.first().click();
    await p.click('[data-neo-dock-for="accessibility"]');
  } else {
    await p.click('[data-nax-toggle]');
  }
}

async function annotate(p, calls) {
  await p.addScriptTag({ path: ANNOTATE });
  await p.evaluate(calls);
}

function clipOf(box, padding, viewport) {
  const x = Math.max(0, Math.floor(box.x - padding));
  const y = Math.max(0, Math.floor(box.y - padding));

  return { x, y, width: Math.min(viewport.width - x, Math.ceil(box.width + 2 * padding)), height: Math.min(viewport.height - y, Math.ceil(box.height + 2 * padding)) };
}

(async () => {
  const browser = await chromium.launch(process.env.NAX_CHROME ? { executablePath: process.env.NAX_CHROME, args: ['--no-sandbox'] } : {});

  for (const [lang, conf] of Object.entries(LANGS)) {
    if (ONLY && ONLY !== lang) {
      continue;
    }

    const out = path.join(ROOT, 'docs', lang, 'bilder');

    fs.mkdirSync(out, { recursive: true });

    const tall = { width: 1280, height: 2400 };
    const wide = { width: 1280, height: 900 };
    let ctx;
    let p;
    let box;

    // 1. The whole panel, tall window so nothing scrolls.
    if (want('panel') || want('structure')) {
      ctx = await context(browser, tall);
      p = await page(ctx, conf.page);

      await openPanel(p);
      await p.waitForTimeout(300);
      await annotate(p, () => {
        neoAnnotate
          .frame('.nax__group--profiles', { number: 1, padding: 2 })
          .frame('.nax__group:not(.nax__group--profiles) .nax__group-title', { number: 2, padding: 2 })
          .frame('[data-nax-flag="typeface"]', { number: 3, padding: 2 })
          .frame('[data-nax-structure]', { number: 4, padding: 2 })
          .frame('[data-nax-reset]', { number: 5, padding: 2 });
      });
      box = await p.locator('[data-nax-panel]').boundingBox();

      if (want('panel')) {
        await p.screenshot({ path: path.join(out, 'panel.png'), clip: clipOf(box, 16, tall) });
      }

      // 2. Page structure, opened (lists the test page).
      if (want('structure')) {
        await p.evaluate(() => neoAnnotate.clear());
        await p.click('[data-nax-structure]');
        await p.mouse.move(900, 50);
        await p.locator('[data-nax-structure-list]').scrollIntoViewIfNeeded();
        await p.waitForTimeout(300);
        await annotate(p, () => {
          neoAnnotate.frame('[data-nax-structure-list]', { number: 1, padding: 4 });
        });
        box = await p.locator('[data-nax-panel]').boundingBox();
        const opener = await p.locator('[data-nax-structure]').boundingBox();
        const list = await p.locator('[data-nax-structure-list]').boundingBox();
        await p.screenshot({ path: path.join(out, 'structure.png'), clip: clipOf({ x: box.x, y: opener.y, width: box.width, height: list.y + list.height - opener.y }, 12, tall) });
      }

      await ctx.close();
    }

    // 3. Read aloud: switch, controls, the block being read (wide window, so
    //    the panel does not cover the text).
    if (want('speak')) {
      ctx = await context(browser, { width: 1600, height: 900 });
      p = await page(ctx, conf.page);
      await openPanel(p);
      await p.locator('[data-nax-flag="speak"]').scrollIntoViewIfNeeded();
      await p.click('[data-nax-flag="speak"]');
      await p.evaluate(() => document.querySelector('.t-box section p').click());
      await p.waitForTimeout(200);
      await annotate(p, () => {
        neoAnnotate
          .frame('[data-nax-flag="speak"]', { number: 1, padding: 2 })
          .frame('[data-nax-speak-controls]', { number: 2, padding: 2 })
          .frame('.nax-reading', { number: 3, padding: 6 });
      });
      await p.screenshot({ path: path.join(out, 'speak.png') });
      await ctx.close();
    }

    // 3b. Read aloud greyed out: no voice for the page language on the device.
    if (want('speak-disabled')) {
      ctx = await context(browser, tall, 'none');
      p = await page(ctx, conf.page);
      await openPanel(p);
      await p.waitForTimeout(300);
      await annotate(p, () => {
        neoAnnotate
          .frame('[data-nax-flag="speak"]', { number: 1, padding: 2 })
          .frame('.nax__group--profiles', { number: 2, padding: 2 });
      });
      box = await p.locator('[data-nax-panel]').boundingBox();
      const profilesBox = await p.locator('.nax__group--profiles').boundingBox();
      const speakBox = await p.locator('[data-nax-flag="speak"]').boundingBox();
      await p.screenshot({ path: path.join(out, 'speak-disabled-profile.png'), clip: clipOf({ x: box.x, y: box.y, width: box.width, height: profilesBox.y + profilesBox.height - box.y }, 12, tall) });
      await p.screenshot({ path: path.join(out, 'speak-disabled.png'), clip: clipOf({ x: box.x, y: speakBox.y - 60, width: box.width, height: speakBox.height + 80 }, 12, tall) });
      await ctx.close();
    }

    // 4. Reading mask on the page.
    if (want('mask')) {
      ctx = await context(browser, wide);
      p = await page(ctx, conf.page);
      await openPanel(p);
      await p.locator('[data-nax-flag="mask"]').scrollIntoViewIfNeeded();
      await p.click('[data-nax-flag="mask"]');
      await p.keyboard.press('Escape');
      await p.evaluate(() => document.activeElement.blur());
      await p.mouse.move(520, 220);
      await p.waitForTimeout(200);
      await p.screenshot({ path: path.join(out, 'mask.png') });
      await ctx.close();
    }

    // 5. Phone.
    if (want('phone')) {
      const phone = { width: 390, height: 844 };

      ctx = await context(browser, phone);
      p = await page(ctx, conf.page);
      await openPanel(p);
      await p.click('[data-nax-profile="vision"]');
      await p.waitForTimeout(200);
      await p.screenshot({ path: path.join(out, 'phone.png') });
      await ctx.close();
    }

    // 6. Back end: the module form, section "Settings for visitors".
    if (want('module-settings') && USER && PASSWORD && MODULE) {
      const back = { width: 1280, height: 1400 };

      ctx = await context(browser, back);
      p = await ctx.newPage();
      await p.goto(BASE + '/contao/login');
      await p.fill('input[name="username"]', USER);
      await p.fill('input[name="password"]', PASSWORD);
      await Promise.all([p.waitForNavigation(), p.click('button[type="submit"]')]);
      await p.goto(BASE + '/contao?do=themes&table=tl_module&act=edit&id=' + MODULE);
      await p.waitForSelector('#ctrl_nax_features');
      await annotate(p, () => {
        neoAnnotate
          .frame('#ctrl_nax_features', { number: 1, padding: 4 })
          .frame('#ctrl_nax_profiles', { number: 2, padding: 4 })
          .frame('#ctrl_nax_online_voices', { number: 3, padding: 4 });
      });
      const a = await p.locator('#ctrl_nax_features').boundingBox();
      const b = await p.locator('#ctrl_nax_online_voices').boundingBox();
      await p.screenshot({ path: path.join(out, 'module-settings.png'), clip: clipOf({ x: a.x, y: a.y - 40, width: Math.max(a.width, b.width), height: b.y + b.height - a.y + 70 }, 16, back) });
      await ctx.close();
    }

    // 7. The settings button of the buttons at the edge of the screen (NEO
    //    Basic) with its menu. The page needs a second button in the corner of
    //    the panel button, e.g. the button to reopen of NEO Cookie Consent, and
    //    the page layout on "One settings button with a menu"; otherwise the
    //    image stays as it is.
    if (want('menu')) {
      const small = { width: 1280, height: 720 };

      ctx = await context(browser, small);
      p = await page(ctx, conf.page);

      const settings = p.locator('[data-neo-dock-button]:not([hidden])');

      if (await settings.count() && await settings.first().isVisible()) {
        await settings.first().click();
        await p.mouse.move(900, 50);
        await p.waitForTimeout(300);
        await annotate(p, () => {
          neoAnnotate
            .frame('[data-neo-dock-button]:not([hidden])', { number: 1, padding: 2 })
            .frame('[data-neo-dock-for="accessibility"]', { number: 2, padding: 0 });
        });
        const menu = await p.locator('[data-neo-dock-menu]:not([hidden])').boundingBox();
        const button = await settings.first().boundingBox();
        const left = Math.min(menu.x, button.x);

        await p.screenshot({ path: path.join(out, 'menu.png'), clip: clipOf({ x: left, y: menu.y, width: Math.max(menu.x + menu.width, button.x + button.width) - left, height: button.y + button.height - menu.y }, 40, small) });
      } else {
        console.log(lang + ': no settings button on the page, menu.png left as it is');
      }

      await ctx.close();
    }

    console.log(lang + ': images in ' + path.relative(ROOT, out));
  }

  await browser.close();
})().catch((e) => {
  console.error(e);
  process.exit(1);
});


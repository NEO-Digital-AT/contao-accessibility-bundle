/*
 * NEO Accessibility — accessibility panel (front-end module "neo_accessibility").
 *
 * Wires the server-rendered markup (data-nax-* hooks): opens and closes the
 * panel, toggles the display settings and keeps them in localStorage. Each
 * switch sets the attribute data-a11y-<flag> on <html>; the text size scales
 * the root font size. Most effects are CSS (accessibility.css); this script
 * adds what CSS cannot do:
 *  - quick options (profiles) that switch several settings at once;
 *  - reading guide and reading mask, which follow the pointer and the focus;
 *  - page structure: a list of the page's areas and headings to jump to;
 *  - read aloud with the browser's speech output (Web Speech API);
 *  - hide images: the description (alt text) shown in place of each image;
 *  - stop animations: videos that play by themselves and script animations
 *    are paused, smooth scrolling by scripts becomes instant;
 *  - high contrast: icons drawn as CSS masks keep the text colour.
 *
 * The panel opens from the floating button or from any element with
 * data-nax-open (the insert tag {{accessibility::link}} writes such a link);
 * the focus goes back to that element when the panel closes. With the overlay
 * option the panel is a modal dialog: the rest of the page is made inert.
 *
 * The floating button takes part in the buttons at the edge of the screen of
 * NEO Basic (data-neo-dock-*, bundles/neobasic/dock.js): with buttons of
 * other extensions in its corner it becomes an entry ("Accessibility") of one
 * settings button with a menu, or is stacked with them. This script only
 * writes the number of settings that are on for that entry (see "Buttons at
 * the edge of the screen" below).
 *
 * Every part of the panel except the dialog itself is optional: a website that
 * overrides the template keeps working with the parts it has.
 *
 * The stored choices are applied earlier already, by a small inline script in
 * the <head> (Neo\AccessibilityBundle\Panel\HeadScript); applySettings() and
 * normaliseFont() below must stay in line with it. The choices are written to
 * localStorage only when the visitor changes something; nothing is sent
 * anywhere by this script.
 *
 * Plain ES5, no dependencies. Loaded with "defer".
 */
(function () {
  'use strict';

  var STORAGE_KEY = 'nax-settings';
  // Every on/off setting. The names are attribute names (data-a11y-<flag>) and
  // storage keys: they never change, new ones are added.
  var FLAGS = [
    'contrast', 'grayscale', 'readable', 'links', 'cursor', 'focus', 'motion',
    'typeface', 'dyslexia', 'align', 'headings', 'noimages', 'lowcontrast', 'lowsaturation',
    'guide', 'mask', 'speak'
  ];
  // Settings that exclude each other: switching one on switches the other off.
  var EXCLUSIVE = {
    typeface: 'dyslexia',
    dyslexia: 'typeface',
    contrast: 'lowcontrast',
    lowcontrast: 'contrast',
    guide: 'mask',
    mask: 'guide'
  };
  var FONT_STEPS = [100, 115, 130, 150];
  var FONT_MIN = FONT_STEPS[0];
  var FONT_MAX = FONT_STEPS[FONT_STEPS.length - 1];
  var FOCUSABLE = 'button, a[href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
  var OPENER = '[data-nax-open]';
  // Button of NEO Cookie Consent to reopen the cookie settings.
  var COOKIE_BUTTON = '[data-ncc-reopen]';
  // Settings buttons and menus of the buttons at the edge of the screen (NEO Basic).
  var DOCK = '[data-neo-dock-root]';
  // Text blocks for read aloud.
  var BLOCKS = 'h1, h2, h3, h4, h5, h6, [role="heading"], p, li, dt, dd, blockquote, figcaption, caption, td, th, address';
  // Controls. With read aloud on, a click on one reads it, the next click uses it.
  var INTERACTIVE = 'a[href], button, input, select, textarea, label, summary, [role="button"], [role="link"], [role="switch"], [role="checkbox"], [role="tab"], [role="menuitem"], [contenteditable=""], [contenteditable="true"]';
  // Fields that take typing or a choice from a list: never held back; their
  // label is read when they get the focus.
  var FIELDS = 'input:not([type="checkbox"]):not([type="radio"]):not([type="button"]):not([type="submit"]):not([type="reset"]):not([type="image"]), select, textarea, [contenteditable=""], [contenteditable="true"]';
  var HAS_SPEECH = !!(window.speechSynthesis && typeof window.SpeechSynthesisUtterance === 'function');
  // How long read aloud waits for the browser to report its voices (ms).
  var VOICE_WAIT = 2500;
  var VOICE_POLL = 250;

  function toArray(list) {
    return Array.prototype.slice.call(list);
  }

  function readSettings() {
    try {
      var value = JSON.parse(window.localStorage.getItem(STORAGE_KEY) || '{}');

      return value && typeof value === 'object' && !Array.isArray(value) ? value : {};
    } catch (e) {
      return {};
    }
  }

  function writeSettings(settings) {
    try {
      if (Object.keys(settings).length) {
        window.localStorage.setItem(STORAGE_KEY, JSON.stringify(settings));
      } else {
        window.localStorage.removeItem(STORAGE_KEY);
      }
    } catch (e) {
      // Storage blocked (private mode, browser setting): the choices last for this page only.
    }
  }

  // Text size in percent: the nearest step. Values of the first version
  // (145, 160) become 150; anything at or below 100 is the normal size.
  function normaliseFont(value) {
    var n = parseInt(value, 10);

    if (!(n > FONT_MIN)) {
      return FONT_MIN;
    }

    var best = FONT_MIN;
    var distance = Infinity;

    FONT_STEPS.forEach(function (step) {
      if (Math.abs(step - n) < distance) {
        distance = Math.abs(step - n);
        best = step;
      }
    });

    return best;
  }

  function format(text, values) {
    return String(text || '').replace(/\{(\w+)\}/g, function (all, key) {
      return Object.prototype.hasOwnProperty.call(values, key) ? String(values[key]) : all;
    });
  }

  // Rendered and visible. getClientRects() alone also counts elements with
  // visibility: hidden, which cannot take the focus.
  function isVisible(el) {
    if (typeof el.checkVisibility === 'function') {
      return el.checkVisibility({ visibilityProperty: true, checkVisibilityCSS: true, opacityProperty: false });
    }

    return el.getClientRects().length > 0 && window.getComputedStyle(el).visibility !== 'hidden';
  }

  function reducedMotion() {
    return document.documentElement.hasAttribute('data-a11y-motion')
      || !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
  }

  function textOf(el) {
    return String(el.innerText || el.textContent || '').replace(/\s+/g, ' ').trim();
  }

  // ---------------------------------------------------------------------------
  // High contrast: icons drawn as CSS masks
  //
  // High contrast paints every background white. Icons drawn as CSS masks take
  // their colour from the background and would vanish: such elements are marked
  // with data-nax-mask and keep the text colour (accessibility.css). An icon is
  // an empty element of at most 240 px whose mask is an image; masks that fade
  // out decorations or sliders (gradients, elements with content) stay as they
  // are.
  // ---------------------------------------------------------------------------

  var ICON_MAX = 240;

  function hasMask(el) {
    if (el.children.length || (el.textContent || '').trim() !== '') {
      return false;
    }

    var style = window.getComputedStyle(el);
    var mask = style.maskImage || style.webkitMaskImage || '';

    if (mask.indexOf('url(') === -1) {
      return false;
    }

    var box = el.getBoundingClientRect();

    return box.width <= ICON_MAX && box.height <= ICON_MAX;
  }

  function markMasks(scope) {
    if (hasMask(scope)) {
      scope.setAttribute('data-nax-mask', '');
    }

    var list = scope.getElementsByTagName('*');

    for (var i = 0; i < list.length; i++) {
      if (!list[i].hasAttribute('data-nax-mask') && hasMask(list[i])) {
        list[i].setAttribute('data-nax-mask', '');
      }
    }
  }

  // ---------------------------------------------------------------------------
  // Hide images: the alt text in place of each image
  // ---------------------------------------------------------------------------

  var altFormat = '{alt}';

  function altAnchor(img) {
    var parent = img.parentNode;

    return parent && parent.nodeName === 'PICTURE' ? parent : img;
  }

  function addAlt(img) {
    if (img.hasAttribute('data-nax-alt-done') || img.closest('.nax')) {
      return;
    }

    img.setAttribute('data-nax-alt-done', '');

    var alt = (img.getAttribute('alt') || '').trim();

    // Empty alt: a decorative image, nothing to show.
    if (!alt) {
      return;
    }

    var note = document.createElement('span');

    note.className = 'nax-alt';
    note.setAttribute('data-nax-alt', '');
    note.textContent = format(altFormat, { alt: alt });
    altAnchor(img).insertAdjacentElement('afterend', note);
  }

  function addAlts(scope) {
    if (scope.nodeName === 'IMG' || (scope.nodeName === 'INPUT' && scope.type === 'image')) {
      addAlt(scope);
    }

    toArray(scope.querySelectorAll('img, input[type="image"]')).forEach(addAlt);
  }

  function removeAlts() {
    toArray(document.querySelectorAll('[data-nax-alt]')).forEach(function (el) {
      el.parentNode.removeChild(el);
    });
    toArray(document.querySelectorAll('[data-nax-alt-done]')).forEach(function (el) {
      el.removeAttribute('data-nax-alt-done');
    });
  }

  // ---------------------------------------------------------------------------
  // Elements added later (chat buttons, sliders, lazy content) are handled as
  // they come, while a setting needs it.
  // ---------------------------------------------------------------------------

  var observer = null;
  var watching = { masks: false, alts: false };

  function watchPage(masks, alts) {
    if (!document.body) {
      return;
    }

    if (masks && !watching.masks) {
      markMasks(document.body);
    }

    if (alts && !watching.alts) {
      addAlts(document.body);
    } else if (!alts && watching.alts) {
      removeAlts();
    }

    watching.masks = masks;
    watching.alts = alts;

    if (!masks && !alts) {
      if (observer) {
        observer.disconnect();
        observer = null;
      }

      return;
    }

    if (observer || !window.MutationObserver) {
      return;
    }

    observer = new MutationObserver(function (records) {
      records.forEach(function (record) {
        toArray(record.addedNodes).forEach(function (node) {
          if (node.nodeType !== 1 || (node.hasAttribute && node.hasAttribute('data-nax-alt'))) {
            return;
          }

          if (watching.masks) {
            markMasks(node);
          }

          if (watching.alts) {
            addAlts(node);
          }
        });
      });
    });
    observer.observe(document.body, { childList: true, subtree: true });
  }

  // ---------------------------------------------------------------------------
  // Stop animations: what CSS alone cannot stop
  // ---------------------------------------------------------------------------

  var motionOn = false;
  var pausedAnimations = [];

  // A video that started by itself: autoplay, or a muted loop started by a script.
  function playsByItself(video) {
    return video.autoplay || (video.loop && video.muted);
  }

  function userStarted() {
    return !!(navigator.userActivation && navigator.userActivation.isActive);
  }

  function pauseMotion() {
    toArray(document.querySelectorAll('video')).forEach(function (video) {
      if (!video.paused && playsByItself(video) && !video.closest('.nax')) {
        video.pause();
        video.setAttribute('data-nax-paused', '');
      }
    });

    // Animations started by scripts (element.animate()). CSS animations and
    // transitions are left to the style sheet, which ends them at once.
    // Pausing would freeze an animation half-way (a fade-in at opacity 0), so
    // a finite one jumps to its end and an endless one is cancelled: the
    // element shows its normal look.
    if (typeof document.getAnimations === 'function') {
      document.getAnimations().forEach(function (animation) {
        var effect = animation.effect;
        var target = effect && effect.target;

        if (animation.playState !== 'running'
          || (window.CSSAnimation && animation instanceof window.CSSAnimation)
          || (window.CSSTransition && animation instanceof window.CSSTransition)
          || (target && target.closest && target.closest('.nax'))) {
          return;
        }

        try {
          if (effect && effect.getComputedTiming && effect.getComputedTiming().endTime === Infinity) {
            animation.cancel();
            pausedAnimations.push(animation);
          } else {
            animation.finish();
          }
        } catch (e) {
          // An animation that cannot be stopped keeps running.
        }
      });
    }
  }

  function resumeMotion() {
    toArray(document.querySelectorAll('video[data-nax-paused]')).forEach(function (video) {
      video.removeAttribute('data-nax-paused');

      var played = video.play();

      if (played && typeof played.catch === 'function') {
        played.catch(function () {});
      }
    });

    // Endless animations start again.
    pausedAnimations.forEach(function (animation) {
      try {
        animation.play();
      } catch (e) {
        // Gone meanwhile.
      }
    });
    pausedAnimations = [];
  }

  function setMotion(on) {
    if (on === motionOn) {
      return;
    }

    motionOn = on;

    if (on) {
      pauseMotion();
    } else {
      resumeMotion();
    }
  }

  // Videos that start later by themselves are paused at once; a video the
  // visitor starts (with a click or a key) plays.
  document.addEventListener('play', function (e) {
    var video = e.target;

    if (motionOn && video && video.nodeName === 'VIDEO' && playsByItself(video) && !userStarted()) {
      video.pause();
      video.setAttribute('data-nax-paused', '');
    }
  }, true);

  // Scripts that scroll smoothly (scrollTo/scrollBy/scrollIntoView with
  // behavior "smooth") scroll at once while animations are stopped.
  function instantScroll(owner, name) {
    var original = owner && owner[name];

    if (typeof original !== 'function') {
      return;
    }

    owner[name] = function () {
      var args = toArray(arguments);
      var options = args[0];

      if (motionOn && options && typeof options === 'object' && options.behavior === 'smooth') {
        var copy = {};

        Object.keys(options).forEach(function (key) { copy[key] = options[key]; });
        copy.behavior = 'auto';
        args[0] = copy;
      }

      return original.apply(this === undefined || this === null ? window : this, args);
    };
  }

  // ---------------------------------------------------------------------------
  // Apply the settings
  // ---------------------------------------------------------------------------

  // Sets the attributes on <html> for the offered settings only; a setting the
  // website does not offer is never touched (the visitor could not turn it off).
  // offered.zoom (module option "Enlarge the whole page"): the text size zooms
  // the content of the page (accessibility.css) instead of scaling the root
  // font size, for designs with font sizes in px.
  function applySettings(settings, offered) {
    var html = document.documentElement;

    offered.flags.forEach(function (flag) {
      if (settings[flag] === true) {
        html.setAttribute('data-a11y-' + flag, '');
      } else {
        html.removeAttribute('data-a11y-' + flag);
      }
    });

    watchPage(html.hasAttribute('data-a11y-contrast'), html.hasAttribute('data-a11y-noimages'));
    setMotion(html.hasAttribute('data-a11y-motion'));

    if (!offered.font) {
      return;
    }

    var font = normaliseFont(settings.font);

    if (font > FONT_MIN) {
      html.setAttribute('data-a11y-font', String(font));

      if (offered.zoom) {
        html.setAttribute('data-a11y-font-zoom', '');
        html.style.setProperty('--nax-font-zoom', String(font / 100));
        html.style.removeProperty('font-size');
      } else {
        html.style.fontSize = 'calc(var(--nax-font-base, 100%) * ' + font / 100 + ')';
      }
    } else if (html.hasAttribute('data-a11y-font')) {
      html.removeAttribute('data-a11y-font');
      html.removeAttribute('data-a11y-font-zoom');
      html.style.removeProperty('font-size');
      html.style.removeProperty('--nax-font-zoom');
    }
  }

  function countActive(settings, offered) {
    var count = offered.flags.filter(function (flag) { return settings[flag] === true; }).length;

    return count + (offered.font && normaliseFont(settings.font) > FONT_MIN ? 1 : 0);
  }

  // A cookie consent tool that offers window.cookieConsent.open() (optional).
  function consentApi() {
    var api = window.cookieConsent;

    return api && typeof api.open === 'function' ? api : null;
  }

  function init(root) {
    // The floating button is optional ("no floating button": links open the panel).
    var toggle = root.querySelector('[data-nax-toggle]');
    var panel = root.querySelector('[data-nax-panel]');

    if (!panel) {
      return;
    }

    var backdrop = root.querySelector('[data-nax-backdrop]');
    var modal = panel.getAttribute('aria-modal') === 'true';
    var opener = null;
    var focusedBefore = null;
    var inertElements = [];

    var closeButton = root.querySelector('[data-nax-close]');
    var resetButton = root.querySelector('[data-nax-reset]');
    var cookiesButton = root.querySelector('[data-nax-cookies]');
    var status = root.querySelector('[data-nax-status]');
    var counts = toArray(root.querySelectorAll('[data-nax-count]'));
    var fontValue = root.querySelector('[data-nax-fontval]');
    var fontDown = root.querySelector('[data-nax-font="-1"]');
    var fontUp = root.querySelector('[data-nax-font="1"]');
    var structureButton = root.querySelector('[data-nax-structure]');
    var structureList = root.querySelector('[data-nax-structure-list]');
    var speakControls = root.querySelector('[data-nax-speak-controls]');

    // Parts of read aloud stay visible everywhere: where it cannot work, they
    // are disabled with the reason (see "blocked" below). Templates of an
    // earlier version hid them until the script showed them.
    toArray(root.querySelectorAll('[data-nax-needs-speech]')).forEach(function (el) {
      el.hidden = false;
    });

    var switches = toArray(root.querySelectorAll('[data-nax-flag]')).filter(function (sw) {
      return FLAGS.indexOf(sw.getAttribute('data-nax-flag')) !== -1;
    });
    var profiles = toArray(root.querySelectorAll('[data-nax-profile]')).map(function (button) {
      var set = {};

      try {
        set = JSON.parse(button.getAttribute('data-nax-set') || '{}') || {};
      } catch (e) {
        set = {};
      }

      return { button: button, set: set };
    });

    var offered = {
      flags: switches.map(function (sw) { return sw.getAttribute('data-nax-flag'); }),
      font: !!(fontDown && fontUp),
      zoom: root.hasAttribute('data-nax-font-zoom')
    };

    // A profile switches only what this panel offers (the server already
    // leaves out what the module does not offer; speech may be missing here).
    profiles = profiles.filter(function (profile) {
      Object.keys(profile.set).forEach(function (key) {
        if (key === 'font' ? !offered.font : offered.flags.indexOf(key) === -1) {
          delete profile.set[key];
        }
      });

      if (!Object.keys(profile.set).length) {
        profile.button.hidden = true;

        return false;
      }

      return true;
    });

    // Offered settings that cannot work here, with the reason shown to the
    // visitor: read aloud without speech output or without a suitable voice.
    // They are disabled, not applied and not counted; the stored choice stays.
    var blocked = {};
    var reasons = toArray(root.querySelectorAll('[data-nax-speech-reason]'));

    function live() {
      return {
        flags: offered.flags.filter(function (flag) { return !blocked[flag]; }),
        font: offered.font,
        zoom: offered.zoom
      };
    }

    function usable(flag) {
      return offered.flags.indexOf(flag) !== -1 && !blocked[flag];
    }

    function applyAll() {
      Object.keys(blocked).forEach(function (flag) {
        document.documentElement.removeAttribute('data-a11y-' + flag);
      });
      applySettings(settings, live());
    }

    // The settings of a quick option that can work here.
    function liveKeys(profile) {
      return Object.keys(profile.set).filter(function (key) { return !blocked[key]; });
    }

    var fontFormat = (fontValue && fontValue.getAttribute('data-nax-format')) || '{value} %';
    var toggleLabel = toggle ? toggle.getAttribute('aria-label') : '';
    var toggleLabelActive = toggle ? toggle.getAttribute('data-nax-label-active') : '';
    var settings = readSettings();
    var announceTimer = null;

    if (status && status.getAttribute('data-nax-msg-alt')) {
      altFormat = status.getAttribute('data-nax-msg-alt');
    }

    // Position: fixed would be relative to a transformed or filtered ancestor,
    // and styles scoped to the layout section (e.g. "#footer a") would leak
    // into the panel. At the end of <body> neither can happen.
    var host = root.parentNode && root.parentNode.classList && root.parentNode.classList.contains('module-neo-accessibility') ? root.parentNode : root;

    if (host.parentNode !== document.body) {
      document.body.appendChild(host);
    }

    // Left out when the text size zooms the content of the page.
    host.setAttribute('data-nax-host', '');

    function message(name) {
      return status ? status.getAttribute('data-nax-msg-' + name) || '' : '';
    }

    function labelOf(el) {
      var label = el && el.querySelector('.nax__label, .nax__profile-label');

      return label ? textOf(label) : (el ? el.getAttribute('aria-label') || textOf(el) : '');
    }

    function formatFont(value) {
      return format(fontFormat, { value: value });
    }

    function announce(text) {
      if (!status || !text) {
        return;
      }

      // Empty first, so the same message is announced again when repeated.
      status.textContent = '';
      window.clearTimeout(announceTimer);
      announceTimer = window.setTimeout(function () { status.textContent = text; }, 100);
    }

    function setUnavailable(button, unavailable) {
      if (button) {
        button.setAttribute('aria-disabled', unavailable ? 'true' : 'false');
      }
    }

    function switchFor(flag) {
      for (var i = 0; i < switches.length; i++) {
        if (switches[i].getAttribute('data-nax-flag') === flag) {
          return switches[i];
        }
      }

      return null;
    }

    // Switches a setting on or off in the settings object; returns the setting
    // that had to go off because it excludes this one.
    function setFlag(s, flag, on) {
      if (!on) {
        delete s[flag];

        return null;
      }

      s[flag] = true;

      var other = EXCLUSIVE[flag];

      if (other && s[other] === true) {
        delete s[other];

        return other;
      }

      return null;
    }

    function profileOn(profile) {
      var keys = liveKeys(profile);

      return keys.length > 0 && keys.every(function (key) {
        if (key === 'font') {
          return normaliseFont(settings.font) >= normaliseFont(profile.set[key]);
        }

        return settings[key] === true;
      });
    }

    function sync() {
      switches.forEach(function (sw) {
        var flag = sw.getAttribute('data-nax-flag');

        sw.setAttribute('aria-checked', settings[flag] === true && !blocked[flag] ? 'true' : 'false');

        if (blocked[flag]) {
          sw.setAttribute('aria-disabled', 'true');
        } else {
          sw.removeAttribute('aria-disabled');
        }
      });

      profiles.forEach(function (profile) {
        profile.button.setAttribute('aria-pressed', profileOn(profile) ? 'true' : 'false');

        if (liveKeys(profile).length) {
          profile.button.removeAttribute('aria-disabled');
        } else {
          profile.button.setAttribute('aria-disabled', 'true');
        }
      });

      // The reason next to the switch and below the quick options.
      reasons.forEach(function (el) {
        var reason = blocked.speak || '';
        var fmt = el.getAttribute('data-nax-format');

        el.textContent = reason && fmt ? format(fmt, { label: el.getAttribute('data-nax-label') || '', reason: reason }) : reason;
        el.hidden = !reason;
      });

      var font = normaliseFont(settings.font);

      if (fontValue) {
        fontValue.textContent = formatFont(font);
      }

      // aria-disabled instead of disabled: the button keeps the focus at the limit.
      setUnavailable(fontDown, font <= FONT_MIN);
      setUnavailable(fontUp, font >= FONT_MAX);

      var active = countActive(settings, live());

      if (active) {
        root.setAttribute('data-nax-active', '');
      } else {
        root.removeAttribute('data-nax-active');
      }

      counts.forEach(function (el) {
        el.textContent = active ? String(active) : '';
      });

      // For the entry in the menu of the settings button (NEO Basic).
      if (root.hasAttribute('data-neo-dock')) {
        if (active) {
          root.setAttribute('data-neo-dock-badge', String(active));
          root.setAttribute('data-neo-dock-badge-label', toggleLabelActive ? format(toggleLabelActive, { count: active }) : String(active));
        } else {
          root.removeAttribute('data-neo-dock-badge');
          root.removeAttribute('data-neo-dock-badge-label');
        }
      }

      if (toggle) {
        toggle.setAttribute('aria-label', active && toggleLabelActive ? format(toggleLabelActive, { count: active }) : toggleLabel);
      }

      if (speakControls) {
        speakControls.hidden = settings.speak !== true || !usable('speak');
      }

      aids.render();
      speech.mode(settings.speak === true && usable('speak'));
    }

    function update(change, text) {
      change(settings);
      writeSettings(settings);
      applyAll();
      sync();
      announce(text);
    }

    // -------------------------------------------------------------------------
    // Buttons at the edge of the screen (NEO Basic)
    //
    // The floating button carries data-neo-dock-launcher, the widget the
    // attributes of its corner: the dock of NEO Basic arranges it with the
    // buttons of other extensions there. In the menu its button is hidden and
    // the entry clicks it; the focus comes back to the settings button, which
    // had it when the panel opened. The entry "Cookie settings" in the panel
    // is shown only while the page has no button of its own for them.
    // -------------------------------------------------------------------------

    // The settings button of the dock in this widget's corner, while it is shown.
    function dockButton() {
      var corner = root.getAttribute('data-neo-dock-corner');
      var button = corner ? document.querySelector(DOCK + ' [data-neo-dock-button="' + corner + '"]') : null;

      return button && !button.hidden ? button : null;
    }

    function syncCookies() {
      if (cookiesButton) {
        cookiesButton.hidden = !consentApi() || !!document.querySelector(COOKIE_BUTTON);
      }
    }

    function isOpen() {
      return !panel.hidden;
    }

    // An element that can take the focus back: in the page, rendered, not
    // transparent, not hidden, not inert, not disabled.
    function canTakeFocus(el) {
      if (!el || !document.body.contains(el) || el.disabled || (el.closest && el.closest('[inert]')) || root.contains(el) && !el.hasAttribute('data-nax-toggle')) {
        return false;
      }

      if (typeof el.checkVisibility === 'function') {
        return el.checkVisibility({ checkOpacity: true, checkVisibilityCSS: true, opacityProperty: true, visibilityProperty: true });
      }

      if (!el.getClientRects().length || window.getComputedStyle(el).visibility === 'hidden') {
        return false;
      }

      for (var node = el; node && node.nodeType === 1; node = node.parentNode) {
        if (window.getComputedStyle(node).opacity === '0') {
          return false;
        }
      }

      return true;
    }

    // Finite animations and transitions that are still running on an element
    // or around it (a menu that fades out after the visitor chose the panel):
    // until they end, the element may still look visible and be gone a moment
    // later. Endless animations are left out, they never end.
    function settling(el) {
      if (typeof document.getAnimations !== 'function') {
        return [];
      }

      return document.getAnimations().filter(function (animation) {
        var effect = animation.effect;
        var target = effect && effect.target;

        return animation.playState === 'running'
          && target && target.contains && target.contains(el)
          && !(effect.getComputedTiming && effect.getComputedTiming().endTime === Infinity);
      });
    }

    function focusLost() {
      var active = document.activeElement;

      return !active || active === document.body || active === document.documentElement || panel.contains(active);
    }

    // Gives the focus back to the first candidate that can take it: the
    // trigger the visitor used, the element that had the focus when the
    // panel opened, the floating button, another opener on the page. When a
    // candidate is still fading in or out, the decision waits until it has
    // settled (at most one second), and is skipped if the visitor has put
    // the focus somewhere else meanwhile.
    function restoreFocus(candidates) {
      var list = [];

      candidates.forEach(function (el) {
        if (el && list.indexOf(el) === -1) {
          list.push(el);
        }
      });

      function attempt() {
        for (var i = 0; i < list.length; i++) {
          if (canTakeFocus(list[i])) {
            list[i].focus();

            if (document.activeElement === list[i]) {
              return;
            }
          }
        }
      }

      var waits = [];

      list.forEach(function (el) {
        settling(el).forEach(function (animation) { waits.push(animation.finished); });
      });

      if (!waits.length || typeof Promise === 'undefined') {
        attempt();

        return;
      }

      var done = false;

      function later() {
        if (!done) {
          done = true;

          if (focusLost()) {
            attempt();
          }
        }
      }

      Promise.all(waits.map(function (finished) {
        return finished.catch(function () {});
      })).then(later);
      window.setTimeout(later, 1000);
    }

    function focusables() {
      return toArray(panel.querySelectorAll(FOCUSABLE)).filter(function (el) {
        return !el.disabled && isVisible(el);
      });
    }

    function findOpener(target) {
      return target && target.closest ? target.closest(OPENER) : null;
    }

    function setExpanded(expanded) {
      var value = expanded ? 'true' : 'false';

      if (toggle) {
        toggle.setAttribute('aria-expanded', value);
      }

      toArray(document.querySelectorAll(OPENER)).forEach(function (el) {
        el.setAttribute('aria-expanded', value);
      });
    }

    // Modal: everything outside the widget, and the floating button under the
    // backdrop, cannot be reached while the panel is open.
    function lockBackground() {
      if (!modal) {
        return;
      }

      toArray(document.body.children).concat(toggle ? [toggle] : []).forEach(function (el) {
        if (el !== host && !el.hasAttribute('inert')) {
          el.setAttribute('inert', '');
          inertElements.push(el);
        }
      });
    }

    function unlockBackground() {
      inertElements.forEach(function (el) { el.removeAttribute('inert'); });
      inertElements = [];
    }

    function openPanel(trigger) {
      if (isOpen()) {
        return;
      }

      opener = trigger || null;
      // The element with the focus now: a menu may have moved it from the
      // trigger to its own button before the panel opened.
      focusedBefore = document.activeElement && document.activeElement !== document.body && !panel.contains(document.activeElement) ? document.activeElement : null;
      syncCookies();
      panel.hidden = false;

      if (backdrop) {
        backdrop.hidden = false;
      }

      setExpanded(true);
      root.classList.add('nax--open');
      lockBackground();

      var first = focusables()[0];

      if (first) {
        first.focus();
      }
    }

    // returnFocus: back to the element that opened the panel (floating button or link).
    function closePanel(returnFocus) {
      if (!isOpen()) {
        return;
      }

      var target = opener;
      var before = focusedBefore;

      opener = null;
      focusedBefore = null;
      panel.hidden = true;

      if (backdrop) {
        backdrop.hidden = true;
      }

      setExpanded(false);
      root.classList.remove('nax--open');
      unlockBackground();
      structure.collapse();

      if (!returnFocus) {
        return;
      }

      // Back to the trigger; if it is gone, hidden or fading out, to what had
      // the focus before, the panel's own button or another opener.
      restoreFocus([target, before, toggle, dockButton()].concat(toArray(document.querySelectorAll(OPENER))));
    }

    function toggleFrom(trigger) {
      if (isOpen()) {
        closePanel(true);
      } else {
        openPanel(trigger);
      }
    }

    function stepFont(direction) {
      var current = normaliseFont(settings.font);
      var index = FONT_STEPS.indexOf(current);
      var next = FONT_STEPS[Math.max(0, Math.min(FONT_STEPS.length - 1, index + direction))];

      if (next === current) {
        return;
      }

      update(function (s) {
        if (next === FONT_MIN) {
          delete s.font;
        } else {
          s.font = next;
        }
      }, format(message('font'), { value: formatFont(next) }));
    }

    // -------------------------------------------------------------------------
    // Reading guide and reading mask
    // -------------------------------------------------------------------------

    var aids = (function () {
      var guide = null;
      var mask = null;
      var y = null;
      var band = 0;
      var frame = null;

      function element(className) {
        var el = document.createElement('div');

        el.className = className;
        el.setAttribute('aria-hidden', 'true');
        root.insertBefore(el, root.firstChild);

        return el;
      }

      function position() {
        frame = null;

        root.style.setProperty('--nax-aid-y', (y === null ? window.innerHeight / 2 : y) + 'px');

        if (band) {
          root.style.setProperty('--nax-aid-band', band + 'px');
        } else {
          root.style.removeProperty('--nax-aid-band');
        }
      }

      function schedule() {
        if (frame === null) {
          frame = window.requestAnimationFrame(position);
        }
      }

      function render() {
        var wantGuide = settings.guide === true && offered.flags.indexOf('guide') !== -1;
        var wantMask = settings.mask === true && offered.flags.indexOf('mask') !== -1;

        if (wantGuide && !guide) {
          guide = element('nax__guide');
        } else if (!wantGuide && guide) {
          root.removeChild(guide);
          guide = null;
        }

        if (wantMask && !mask) {
          var bottom = element('nax__mask nax__mask--bottom');
          var top = element('nax__mask nax__mask--top');

          mask = [top, bottom];
        } else if (!wantMask && mask) {
          mask.forEach(function (el) { root.removeChild(el); });
          mask = null;
        }

        if (guide || mask) {
          position();
        }
      }

      function active() {
        return !!(guide || mask);
      }

      // The guide sits just below the pointer, under the line being read; the
      // mask's open strip is centred on it.
      function fromPointer(e) {
        if (!active() || e.pointerType === 'touch' && e.type === 'pointermove' && !e.isPrimary) {
          return;
        }

        y = guide ? e.clientY + 12 : e.clientY;
        band = 0;
        schedule();
      }

      // Keyboard focus (or the block being read): below it for the guide, the
      // whole element in the strip of the mask.
      function fromElement(el) {
        if (!active() || !el || !el.getBoundingClientRect || root.contains(el)) {
          return;
        }

        var box = el.getBoundingClientRect();

        y = guide ? box.bottom + 4 : box.top + box.height / 2;
        band = Math.min(box.height + 16, window.innerHeight * 0.6);
        schedule();
      }

      window.addEventListener('pointermove', fromPointer, { passive: true });
      window.addEventListener('pointerdown', fromPointer, { passive: true });
      document.addEventListener('focusin', function (e) { fromElement(e.target); });

      return { render: render, fromElement: fromElement };
    })();

    // -------------------------------------------------------------------------
    // Page structure: areas (landmarks) and headings to jump to
    // -------------------------------------------------------------------------

    var structure = (function () {
      var texts = {};

      if (structureList) {
        try {
          texts = JSON.parse(structureList.getAttribute('data-nax-texts') || '{}') || {};
        } catch (e) {
          texts = {};
        }
      }

      var ROLES = ['banner', 'navigation', 'main', 'complementary', 'contentinfo', 'search', 'form', 'region'];
      var SECTIONING = 'article, aside, main, nav, section';

      function outsidePanel(el) {
        return !el.closest('.nax') && !el.closest('[aria-hidden="true"]') && isVisible(el);
      }

      function nameOf(el) {
        var label = (el.getAttribute('aria-label') || '').trim();
        var ids = (el.getAttribute('aria-labelledby') || '').trim();

        if (!label && ids) {
          label = ids.split(/\s+/).map(function (id) {
            var ref = document.getElementById(id);

            return ref ? textOf(ref) : '';
          }).join(' ').trim();
        }

        return label || (el.getAttribute('title') || '').trim();
      }

      function roleOf(el) {
        var role = (el.getAttribute('role') || '').trim().split(/\s+/)[0];

        var parent = el.parentElement;

        if (role) {
          return ROLES.indexOf(role) !== -1 ? role : null;
        }

        switch (el.nodeName) {
          case 'HEADER':
            return parent && parent.closest(SECTIONING) ? null : 'banner';
          case 'FOOTER':
            return parent && parent.closest(SECTIONING) ? null : 'contentinfo';
          case 'NAV':
            return 'navigation';
          case 'MAIN':
            return 'main';
          case 'ASIDE':
            return 'complementary';
          case 'SEARCH':
            return 'search';
          case 'FORM':
            return 'form';
          case 'SECTION':
            return 'region';
          default:
            return null;
        }
      }

      function landmarks() {
        var selector = 'header, footer, nav, main, aside, search, form, section, [role]';

        return toArray(document.querySelectorAll(selector)).filter(outsidePanel).map(function (el) {
          var role = roleOf(el);
          var name = role ? nameOf(el) : '';

          // A form or a section is an area of its own only with a name.
          if (!role || ((role === 'form' || role === 'region') && !name)) {
            return null;
          }

          return { el: el, text: (texts[role] || role) + (name ? ': ' + name : ''), level: 0 };
        }).filter(Boolean);
      }

      function headings() {
        return toArray(document.querySelectorAll('h1, h2, h3, h4, h5, h6, [role="heading"]')).filter(outsidePanel).map(function (el) {
          var level = /^H[1-6]$/.test(el.nodeName) ? parseInt(el.nodeName.charAt(1), 10) : parseInt(el.getAttribute('aria-level'), 10) || 2;
          var text = textOf(el) || nameOf(el);

          return text ? { el: el, text: text, level: Math.max(1, Math.min(6, level)) } : null;
        }).filter(Boolean);
      }

      // Into view and focused; an element that cannot take the focus gets
      // tabindex="-1" for it.
      function jump(el) {
        closePanel(false);

        if (!el.matches(FOCUSABLE) && !el.hasAttribute('tabindex')) {
          el.setAttribute('tabindex', '-1');
        }

        var small = el.getBoundingClientRect().height < window.innerHeight / 2;

        el.scrollIntoView({ block: small ? 'center' : 'start', behavior: reducedMotion() ? 'auto' : 'smooth' });

        try {
          el.focus({ preventScroll: true });
        } catch (e) {
          el.focus();
        }
      }

      function list(title, items, empty) {
        var wrap = document.createElement('div');
        var heading = document.createElement('h4');

        heading.className = 'nax__structure-title';
        heading.textContent = title;
        wrap.appendChild(heading);

        if (!items.length) {
          var note = document.createElement('p');

          note.className = 'nax__empty';
          note.textContent = empty;
          wrap.appendChild(note);

          return wrap;
        }

        var ul = document.createElement('ul');

        items.forEach(function (item) {
          var li = document.createElement('li');
          var button = document.createElement('button');

          button.type = 'button';
          button.className = 'nax__jump';

          if (item.level) {
            var level = document.createElement('span');

            button.setAttribute('data-level', String(item.level));
            level.className = 'nax__sr';
            level.textContent = format(texts.level || '{level}', { level: item.level }) + ': ';
            button.appendChild(level);
          }

          button.appendChild(document.createTextNode(item.text));
          button.addEventListener('click', function () { jump(item.el); });
          li.appendChild(button);
          ul.appendChild(li);
        });

        wrap.appendChild(ul);

        return wrap;
      }

      function build() {
        structureList.textContent = '';
        structureList.appendChild(list(texts.areas || '', landmarks(), texts.empty || ''));
        structureList.appendChild(list(texts.headings || '', headings(), texts.empty || ''));
      }

      function expanded() {
        return !!structureButton && structureButton.getAttribute('aria-expanded') === 'true';
      }

      function collapse() {
        if (expanded()) {
          structureButton.setAttribute('aria-expanded', 'false');
          structureList.hidden = true;
        }
      }

      if (structureButton && structureList) {
        structureButton.addEventListener('click', function () {
          if (expanded()) {
            collapse();

            return;
          }

          build();
          structureButton.setAttribute('aria-expanded', 'true');
          structureList.hidden = false;
        });
      }

      return { collapse: collapse };
    })();

    // -------------------------------------------------------------------------
    // Read aloud (Web Speech API): a click, or Enter on a marked block, reads
    // that block; "Read page" reads the main content block by block.
    // -------------------------------------------------------------------------

    var speech = (function () {
      var synth = HAS_SPEECH ? window.speechSynthesis : null;
      var on = false;
      var queue = [];
      var current = null;
      var reading = null;
      var paused = false;
      var voices = [];
      // Privacy by default: only voices installed on the visitor's device,
      // which read the text on the device. Online voices of the browser send
      // the text to its maker; the module allows them explicitly ("any").
      var onlineAllowed = root.getAttribute('data-nax-voices') === 'any';
      // Voices are reported late in some browsers (Chrome: after a moment,
      // with "voiceschanged"; Safari sometimes without the event). Nothing is
      // spoken before they are known, and never with an unset voice.
      var ready = false;
      var pending = null;
      var listeners = [];
      var reasonTexts = reasons[0] || null;
      var pageButton = speakControls && speakControls.querySelector('[data-nax-speak="page"]');
      var pauseButton = speakControls && speakControls.querySelector('[data-nax-speak="pause"]');
      var stopButton = speakControls && speakControls.querySelector('[data-nax-speak="stop"]');

      function controlText(name) {
        return speakControls ? speakControls.getAttribute('data-nax-msg-' + name) || '' : '';
      }

      function reasonText(name, lang) {
        var text = reasonTexts ? reasonTexts.getAttribute('data-nax-msg-' + name) || '' : '';

        return format(text, { language: languageName(lang) });
      }

      // The name of a language in the language of the page ("Deutsch").
      function languageName(lang) {
        var code = normaliseLang(lang).split('-')[0];

        try {
          if (window.Intl && typeof window.Intl.DisplayNames === 'function') {
            var name = new window.Intl.DisplayNames([document.documentElement.lang || code], { type: 'language' }).of(code);

            if (name) {
              return name;
            }
          }
        } catch (e) {
          // Unknown code: the code itself.
        }

        return code;
      }

      function pageLang() {
        return document.documentElement.lang || 'en';
      }

      // Why read aloud cannot be used here, or '' when it can; null while the
      // voices are not known yet.
      function reason() {
        if (!synth) {
          return reasonText('unsupported', pageLang()) || ' ';
        }

        if (!ready) {
          return null;
        }

        return voiceFor(pageLang()) ? '' : (reasonText(onlineAllowed ? 'browser' : 'device', pageLang()) || ' ');
      }

      function report() {
        var value = reason();

        if (value !== null) {
          listeners.forEach(function (listener) { listener(value); });
        }
      }

      function loadVoices() {
        try {
          voices = synth ? synth.getVoices() || [] : [];
        } catch (e) {
          voices = [];
        }

        return voices.length > 0;
      }

      function becomeReady() {
        if (ready) {
          return;
        }

        ready = true;
        report();

        // Waiting reads go now, or say why they cannot.
        if (pending) {
          var blocks = pending;

          pending = null;

          if (voiceFor(pageLang())) {
            read(blocks);
          } else {
            announce(reason());
          }
        }
      }

      if (synth) {
        if (loadVoices()) {
          ready = true;
        } else {
          var waited = 0;
          var poll = window.setInterval(function () {
            waited += VOICE_POLL;

            if (loadVoices() || waited >= VOICE_WAIT) {
              window.clearInterval(poll);
              becomeReady();
            }
          }, VOICE_POLL);
        }

        // Voices can change later (installed, language packs loaded).
        var onVoices = function () {
          loadVoices();

          if (ready) {
            report();
          } else if (voices.length) {
            becomeReady();
          }
        };

        if (typeof synth.addEventListener === 'function') {
          synth.addEventListener('voiceschanged', onVoices);
        } else {
          synth.onvoiceschanged = onVoices;
        }
      }

      function normaliseLang(lang) {
        return String(lang || '').replace(/_/g, '-').toLowerCase();
      }

      // A voice of the language: the exact one first, then the same language
      // in another region. Only voices of the device, unless online voices
      // are allowed; then the device's still come first.
      function voiceFor(lang) {
        var wanted = normaliseLang(lang);
        var primary = wanted.split('-')[0];
        var exact = [];
        var same = [];

        voices.forEach(function (voice) {
          var code = normaliseLang(voice.lang);

          if (code === wanted) {
            exact.push(voice);
          } else if (code.split('-')[0] === primary) {
            same.push(voice);
          }
        });

        function local(list) {
          return list.filter(function (voice) { return voice.localService; })[0] || null;
        }

        if (!onlineAllowed) {
          return local(exact) || local(same);
        }

        return local(exact) || local(same) || exact[0] || same[0] || null;
      }

      function langOf(el) {
        var scope = el.closest('[lang]');

        return (scope && scope.getAttribute('lang')) || document.documentElement.lang || 'en';
      }

      function mark(el) {
        if (reading) {
          reading.classList.remove('nax-reading');
        }

        reading = el;

        if (el) {
          el.classList.add('nax-reading');
          aids.fromElement(el);

          var box = el.getBoundingClientRect();

          if (box.top < 0 || box.bottom > window.innerHeight) {
            el.scrollIntoView({ block: 'center', behavior: reducedMotion() ? 'auto' : 'smooth' });
          }
        }
      }

      function syncControls() {
        var busy = !!current;

        if (pauseButton) {
          pauseButton.setAttribute('aria-disabled', busy ? 'false' : 'true');

          var label = pauseButton.querySelector('[data-nax-speak-label]');

          if (label) {
            label.textContent = pauseButton.getAttribute(paused ? 'data-nax-label-resume' : 'data-nax-label-pause') || label.textContent;
          }
        }

        if (stopButton) {
          stopButton.setAttribute('aria-disabled', busy ? 'false' : 'true');
        }
      }

      // An item is a block (its text is read) or {el, text} (a control: its name).
      function next() {
        var item = queue.shift();
        var el = item && item.el ? item.el : item;

        if (!el) {
          current = null;
          paused = false;
          mark(null);
          syncControls();

          return;
        }

        var text = item.el ? item.text : textOf(el);

        if (!text) {
          next();

          return;
        }

        var voice = voiceFor(langOf(el));

        // No suitable voice for the language of this block: it is left out.
        if (!voice) {
          next();

          return;
        }

        var utterance = new window.SpeechSynthesisUtterance(text);

        utterance.voice = voice;
        utterance.lang = voice.lang;

        utterance.rate = 0.95;
        utterance.onend = function () {
          if (current === utterance) {
            next();
          }
        };
        utterance.onerror = utterance.onend;

        current = utterance;
        mark(el);
        syncControls();
        synth.speak(utterance);
      }

      function stop(quiet) {
        var wasBusy = !!current;

        queue = [];
        pending = null;
        current = null;
        paused = false;

        if (synth) {
          synth.cancel();
        }

        mark(null);
        syncControls();

        if (wasBusy && !quiet) {
          announce(controlText('stopped'));
        }
      }

      function read(blocks) {
        if (!synth) {
          return;
        }

        stop(true);

        // Voices not known yet: this read waits for them (becomeReady()).
        if (!ready) {
          pending = blocks.slice();

          return;
        }

        if (!voiceFor(pageLang())) {
          announce(reason());

          return;
        }

        queue = blocks.slice();

        if (!queue.length) {
          announce(controlText('empty'));

          return;
        }

        next();
      }

      // Blocks without blocks inside, in reading order, visible, outside the panel.
      function leafBlocks(scope) {
        return toArray(scope.querySelectorAll(BLOCKS)).filter(function (el) {
          return !el.closest('.nax, nav, [aria-hidden="true"], [hidden], script, style, template, noscript')
            && !el.querySelector(BLOCKS)
            && isVisible(el)
            && textOf(el) !== '';
        });
      }

      function contentRoot() {
        return document.querySelector('main, [role="main"]') || document.body;
      }

      // Text of a block outside its links and buttons: a block that only holds
      // a link is reached through the link itself.
      function ownText(el) {
        var walker = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, null);
        var text = '';

        while (walker.nextNode()) {
          var parent = walker.currentNode.parentNode;

          if (!(parent && parent.closest && parent.closest(INTERACTIVE))) {
            text += walker.currentNode.nodeValue;
          }
        }

        return text.trim();
      }

      // Keyboard: the blocks of the main content can be reached with Tab while
      // read aloud is on (and only then).
      function markBlocks(enable) {
        if (!enable) {
          toArray(document.querySelectorAll('[data-nax-speakable]')).forEach(function (el) {
            el.removeAttribute('data-nax-speakable');

            if (el.getAttribute('data-nax-speakable-tab') !== null) {
              el.removeAttribute('tabindex');
              el.removeAttribute('data-nax-speakable-tab');
            }
          });

          return;
        }

        leafBlocks(contentRoot()).forEach(function (el) {
          if (ownText(el) === '') {
            return;
          }

          el.setAttribute('data-nax-speakable', '');

          if (!el.hasAttribute('tabindex')) {
            el.setAttribute('tabindex', '0');
            el.setAttribute('data-nax-speakable-tab', '');
          }
        });
      }

      function mode(enable) {
        if (!HAS_SPEECH || enable === on) {
          return;
        }

        on = enable;
        markBlocks(enable);

        if (!enable) {
          disarm();
          stop(true);
        }
      }

      function blockFrom(target) {
        if (!target || !target.closest || target.closest('.nax') || target.closest(INTERACTIVE)) {
          return null;
        }

        var block = target.closest(BLOCKS);

        return block && !block.closest('.nax') ? block : null;
      }

      document.addEventListener('click', function (e) {
        if (!on) {
          return;
        }

        var block = blockFrom(e.target);

        if (block) {
          read([block]);
        }
      });

      // ---- Controls: read first, use on the second click ------------------
      //
      // A click with the mouse, the finger or the pen on a control (link,
      // button, label, the question of an accordion, a checkbox …) reads it
      // and does nothing else; a hint next to it, which screen readers
      // announce too, says that the next click uses it. The next click on the
      // same control goes through as usual. The text read is the text block
      // under the pointer, found through the stack of elements at that point,
      // so a card whose whole surface is a link reads the text the visitor
      // pointed at; without a block there, the name of the control. Clicks
      // from the keyboard (Enter, Space) and from scripts are never held
      // back: with the keyboard, a control is read when it gets the focus.
      // Fields for typing and lists are never held back either; their label
      // is read when they get the focus.

      var armed = null;
      var armedScroll = 0;
      var passing = false;
      var byKeyboard = false;
      var hint = null;

      // What a control says: its label, otherwise its text, title or value.
      function nameOfControl(el) {
        var label = (el.getAttribute('aria-label') || '').trim();
        var ids = (el.getAttribute('aria-labelledby') || '').trim();

        if (!label && ids) {
          label = ids.split(/\s+/).map(function (id) {
            var ref = document.getElementById(id);

            return ref ? textOf(ref) : '';
          }).join(' ').trim();
        }

        if (!label && el.labels && el.labels.length) {
          label = textOf(el.labels[0]);
        }

        if (!label) {
          var img = el.querySelector && el.querySelector('img[alt]');

          label = textOf(el) || (el.getAttribute('title') || '').trim() || (img ? img.getAttribute('alt').trim() : '') || String(el.value || '').trim();
        }

        return label;
      }

      // Controls of the panel itself and the way to it are used at once.
      function exempt(el) {
        return !!el.closest('.nax, [data-nax-open], ' + DOCK);
      }

      // The text block under the point, also beneath a link that covers it.
      function blockAt(x, y) {
        var stack = typeof document.elementsFromPoint === 'function' ? document.elementsFromPoint(x, y) : [];

        for (var i = 0; i < stack.length; i++) {
          var el = stack[i];

          if (el.closest('.nax')) {
            continue;
          }

          var block = el.matches(BLOCKS) ? el : null;

          if (block && textOf(block) !== '') {
            return block;
          }
        }

        return null;
      }

      function showHint(control) {
        var text = controlText('again');

        if (!text) {
          return;
        }

        if (!hint) {
          // A polite status message: screen readers announce the hint too.
          hint = document.createElement('div');
          hint.className = 'nax__hint';
          hint.setAttribute('role', 'status');
          hint.setAttribute('data-nax-hint', '');
          root.appendChild(hint);
        }

        hint.textContent = '';
        hint.classList.add('nax__hint--shown');

        var box = control.getBoundingClientRect();

        // Above the control, or below it where there is no room above.
        window.setTimeout(function () {
          hint.textContent = text;

          var height = hint.offsetHeight;
          var width = hint.offsetWidth;
          var top = box.top - height - 8 >= 0 ? box.top - height - 8 : box.bottom + 8;
          var left = Math.max(8, Math.min(box.left, (window.innerWidth || document.documentElement.clientWidth) - width - 8));

          hint.style.top = Math.round(top) + 'px';
          hint.style.left = Math.round(left) + 'px';
        }, 50);
      }

      function disarm() {
        armed = null;

        if (hint) {
          hint.classList.remove('nax__hint--shown');
          hint.textContent = '';
        }
      }

      window.addEventListener('click', function (e) {
        // Without the text of the hint (a template of an earlier version)
        // controls act at once, as they did then.
        if (!on || passing || !(e.detail > 0) || !controlText('again')) {
          return;
        }

        var target = e.target;
        var control = target && target.closest ? target.closest(INTERACTIVE) : null;

        if (!control || exempt(control) || control.matches(FIELDS)) {
          disarm();

          return;
        }

        if (control === armed) {
          // The second click: used as usual, also by what it passes on (a
          // label to its checkbox).
          disarm();
          passing = true;
          window.setTimeout(function () { passing = false; }, 0);

          return;
        }

        e.preventDefault();
        e.stopPropagation();

        var block = blockAt(e.clientX, e.clientY);

        read([block ? { el: block, text: textOf(block) } : { el: control, text: nameOfControl(control) }]);
        armed = control;
        armedScroll = window.pageYOffset || 0;
        showHint(control);
      }, true);

      // Keyboard: what gets the focus is read; a field also on a click.
      document.addEventListener('keydown', function (e) {
        byKeyboard = e.key === 'Tab' || e.key.indexOf('Arrow') === 0 || byKeyboard && e.key !== 'Escape';
      }, true);

      document.addEventListener('pointerdown', function () {
        byKeyboard = false;
      }, true);

      document.addEventListener('focusin', function (e) {
        var target = e.target;

        if (!on || !target || !target.closest || target === armed) {
          return;
        }

        // The focus moved on: the hint no longer points at it.
        if (armed) {
          disarm();
        }

        if (exempt(target)) {
          return;
        }

        if (target.matches(FIELDS)) {
          read([{ el: target, text: nameOfControl(target) }]);
        } else if (byKeyboard && target.matches(INTERACTIVE)) {
          read([{ el: target, text: nameOfControl(target) }]);
        } else if (byKeyboard && target.hasAttribute('data-nax-speakable')) {
          read([target]);
        }
      });

      // The hint belongs to the place it points at.
      window.addEventListener('scroll', function () {
        if (armed && Math.abs((window.pageYOffset || 0) - armedScroll) > 24) {
          disarm();
        }
      }, { passive: true });

      window.addEventListener('resize', function () {
        if (armed) {
          disarm();
        }
      });

      document.addEventListener('keydown', function (e) {
        if (!on || (e.key !== 'Enter' && e.key !== ' ' && e.key !== 'Spacebar')) {
          return;
        }

        var target = e.target;

        if (target && target.hasAttribute && target.hasAttribute('data-nax-speakable')) {
          e.preventDefault();
          read([target]);
        }
      });

      if (pageButton) {
        pageButton.addEventListener('click', function () {
          read(leafBlocks(contentRoot()));
          announce(controlText('reading'));
        });
      }

      if (pauseButton) {
        pauseButton.addEventListener('click', function () {
          if (!current) {
            return;
          }

          if (paused) {
            synth.resume();
            paused = false;
            announce(controlText('reading'));
          } else {
            synth.pause();
            paused = true;
            announce(controlText('paused'));
          }

          syncControls();
        });
      }

      if (stopButton) {
        stopButton.addEventListener('click', function () { stop(false); });
      }

      // Nothing keeps speaking after the visitor left the page.
      window.addEventListener('pagehide', function () {
        if (synth && current) {
          synth.cancel();
        }
      });

      return {
        mode: mode,
        stop: stop,
        disarm: disarm,
        busy: function () { return !!current; },
        // Calls back with the reason why read aloud cannot be used ('' when it
        // can), now if known, and whenever it changes.
        availability: function (listener) {
          listeners.push(listener);

          var value = reason();

          if (value !== null) {
            listener(value);
          }
        }
      };
    })();

    // -------------------------------------------------------------------------
    // Events
    // -------------------------------------------------------------------------

    if (toggle) {
      toggle.addEventListener('click', function () { toggleFrom(toggle); });
    }

    // Links and buttons elsewhere on the page (event delegation, so elements
    // added later work too). A link with role="button" also reacts to Space.
    document.addEventListener('click', function (e) {
      var trigger = findOpener(e.target);

      if (trigger) {
        e.preventDefault();
        toggleFrom(trigger);
      }
    });

    document.addEventListener('keydown', function (e) {
      var trigger = (e.key === ' ' || e.key === 'Spacebar') ? findOpener(e.target) : null;

      if (trigger && trigger.tagName !== 'BUTTON') {
        e.preventDefault();
        toggleFrom(trigger);
      }
    });

    toArray(document.querySelectorAll(OPENER)).forEach(function (el) {
      el.setAttribute('aria-haspopup', 'dialog');
      el.setAttribute('aria-controls', panel.id);
      el.setAttribute('aria-expanded', 'false');
    });

    if (backdrop) {
      backdrop.addEventListener('click', function () { closePanel(true); });
    }

    if (closeButton) {
      closeButton.addEventListener('click', function () { closePanel(true); });
    }

    if (fontDown) {
      fontDown.addEventListener('click', function () { stepFont(-1); });
    }

    if (fontUp) {
      fontUp.addEventListener('click', function () { stepFont(1); });
    }

    switches.forEach(function (sw) {
      sw.addEventListener('click', function () {
        var flag = sw.getAttribute('data-nax-flag');
        var replaced = null;

        // Disabled: the reason stands next to it.
        if (blocked[flag]) {
          return;
        }

        update(function (s) {
          replaced = setFlag(s, flag, s[flag] !== true);
        });

        // A setting that excludes this one went off: say which.
        if (replaced && switchFor(replaced)) {
          announce(format(message('replaced'), { label: labelOf(switchFor(replaced)) }));
        }
      });
    });

    profiles.forEach(function (profile) {
      profile.button.addEventListener('click', function () {
        var wasOn = profileOn(profile);
        var keys = liveKeys(profile);

        if (!keys.length) {
          return;
        }

        update(function (s) {
          keys.forEach(function (key) {
            if (key === 'font') {
              var size = normaliseFont(profile.set[key]);

              if (wasOn) {
                delete s.font;
              } else if (normaliseFont(s.font) < size) {
                s.font = size;
              }

              return;
            }

            setFlag(s, key, !wasOn);
          });
        }, format(message(wasOn ? 'off' : 'on'), { label: labelOf(profile.button) }));
      });
    });

    if (resetButton) {
      resetButton.addEventListener('click', function () {
        speech.stop(true);
        update(function (s) {
          Object.keys(s).forEach(function (key) { delete s[key]; });
        }, message('reset'));
      });
    }

    if (cookiesButton) {
      cookiesButton.addEventListener('click', function () {
        var api = consentApi();

        if (!api) {
          syncCookies();

          return;
        }

        // Close first without taking the focus back: the consent dialog takes it.
        closePanel(false);

        try {
          api.open();
        } catch (e) {
          // The consent tool failed; nothing else to do here.
        }
      });
    }

    // Keyboard: Esc closes the panel (or, with the panel closed, stops reading
    // aloud), Tab and Shift+Tab stay inside the open panel.
    document.addEventListener('keydown', function (e) {
      if (!isOpen()) {
        if (e.key === 'Escape' || e.key === 'Esc') {
          speech.disarm();

          if (speech.busy()) {
            speech.stop(false);
          }
        }

        return;
      }

      if (e.key === 'Escape' || e.key === 'Esc') {
        closePanel(true);

        return;
      }

      if (e.key !== 'Tab') {
        return;
      }

      var items = focusables();

      if (!items.length) {
        return;
      }

      var first = items[0];
      var last = items[items.length - 1];
      var active = document.activeElement;

      if (!panel.contains(active)) {
        e.preventDefault();
        (e.shiftKey ? last : first).focus();
      } else if (e.shiftKey && active === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && active === last) {
        e.preventDefault();
        first.focus();
      }
    });

    // Without overlay, a click or tap outside closes the panel; the focus goes
    // where the visitor clicked. (With overlay, the backdrop handles it.)
    document.addEventListener('pointerdown', function (e) {
      if (isOpen() && !modal && !root.contains(e.target) && !findOpener(e.target)) {
        closePanel(false);
      }
    });

    // Choices changed in another tab of the same website.
    window.addEventListener('storage', function (e) {
      if (e.key === null || e.key === STORAGE_KEY) {
        settings = readSettings();
        applyAll();
        sync();
      }
    });

    // Read aloud can only be used with a suitable voice: until the browser
    // has reported its voices it stays as it is; then it is disabled with
    // the reason, or enabled again (voices can be added later).
    speech.availability(function (reason) {
      if (!offered.flags.length || offered.flags.indexOf('speak') === -1) {
        return;
      }

      if (reason) {
        blocked.speak = reason;
      } else {
        delete blocked.speak;
      }

      applyAll();
      sync();
    });

    if (offered.flags.indexOf('motion') !== -1) {
      instantScroll(window, 'scrollTo');
      instantScroll(window, 'scrollBy');

      if (window.Element) {
        instantScroll(window.Element.prototype, 'scrollTo');
        instantScroll(window.Element.prototype, 'scrollBy');
        instantScroll(window.Element.prototype, 'scrollIntoView');
      }
    }

    // The consent tool may load after this script: look again once the
    // document is parsed, once everything is loaded and whenever the panel opens.
    syncCookies();
    document.addEventListener('DOMContentLoaded', syncCookies);
    window.addEventListener('load', function () {
      syncCookies();

      // Animations and videos that started while the page loaded.
      if (motionOn) {
        pauseMotion();
      }
    });

    applyAll();
    sync();
    root.hidden = false;
  }

  function boot() {
    var root = document.querySelector('[data-nax]');

    // One panel per page: a second module would only repeat the first.
    if (root && !root.hasAttribute('data-nax-ready')) {
      root.setAttribute('data-nax-ready', '');
      init(root);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();

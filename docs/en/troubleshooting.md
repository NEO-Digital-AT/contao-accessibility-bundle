---
title: Troubleshooting
summary: Common problems with the accessibility panel as symptom, cause and solution.
audience: users
language: en
updated: 2026-11-14
---

# Troubleshooting

| Symptom | Cause | Solution |
| --- | --- | --- |
| The button does not appear. | The module is not in the page layout of this page, or JavaScript is blocked or failed. | Add the module to the layout ([Setup](setup.md)); look at the browser console. The widget only appears once its script runs. |
| The link `{{accessibility::link}}` only jumps to the top of the page. | The module is not on this page. | Add it to the page layout. |
| The button covers a cookie banner or another button. | Same corner, and one of the two takes no part in the buttons at the edge of the screen (an overridden template without the `data-neo-dock` attributes, or an older extension); or a banner with a higher stacking order. | Take over the attributes ([Customising](customising.md#buttons-at-the-edge-of-the-screen-neo-basic)), choose another corner, or change `--nax-z-index`, `--nax-offset-x`, `--nax-offset-y` ([Customising](customising.md)). |
| The accessibility button is missing; a settings button with a cogwheel sits in its corner. | Other buttons share the corner, and the page layout arranges them as *One settings button with a menu* (the default): its menu leads to the panel. | Intended. Another arrangement is set in the page layout (**Buttons at the edge of the screen**), another corner here ([Setup](setup.md#together-with-other-buttons-in-the-same-corner)). |
| The settings are forgotten. | The browser blocks `localStorage` (private window, strict settings). | Nothing to do on the website; the choices last for the current page. |
| A setting or a quick option is missing in the panel. | It is not ticked in the module; a quick option none of whose settings is offered is left out — the module form names it in a note below **Quick options**. | Tick it under **Offered settings** or **Quick options**. |
| **Read aloud** and the quick option "Read aloud" are missing. | The setting is not ticked in the module. | Tick **Read aloud** under **Offered settings**. |
| **Read aloud** is greyed out: "No voice for … is installed on this device." | The visitor's device has no voice for the language of the page; by default only device voices are used. | The visitor installs a voice for the language in the speech settings of the device; or the website allows the browser's online voices (privacy policy!). |
| **Read aloud** is greyed out: "This browser cannot read aloud." | The browser has no speech output (Web Speech API). | Use another browser. |
| With read aloud on, a link, button or checkbox does nothing on the first click. | Read aloud reads it first; the hint next to it says that the next click uses it. | Intended: click again, or use it with Enter or Space. Switching read aloud off makes every click act at once. |
| With read aloud on, a control of the website reacts at once although it should be read first. | The website starts it on a press of the mouse or finger (`pointerdown`, `mousedown`, `touchstart`) instead of on the click, or it is not a link, button, label or element with a role of a control. | Let it react to the click, or give it the matching element or `role` (for example `role="button"`). |
| Read aloud says nothing, the text is not marked. | The page is still loading the voices (up to 2.5 seconds), or the text is in a language without a device voice (it is skipped). | Wait a moment; mark the language of the text with `lang`; install a voice for it. |
| Read aloud marks the text but is silent. | The device is muted, or the voice is broken. | Check the volume and the speech settings of the device. |
| Read aloud uses a voice of the wrong language. | The page or the part of the page is not marked with its language. | Set the language of the page (root page), mark other languages with `lang`. |
| Text of a different language is read with the page's voice. | The part is not marked with its language. | Mark it with `lang="…"` (for example in the text editor). |
| The dyslexia font does not appear. | The font files are missing under `bundles/neoaccessibility/fonts/`, or a Content Security Policy blocks fonts from the website. | Run `assets:install public`; allow `'self'` in `font-src`. |
| With **Readable font** or **Dyslexia font** an icon turns into letters or a box. | The website's icon font is on an element the panel does not recognise as an icon. | Add `data-nax-keep-font` to that element ([Customising](customising.md)). |
| The text gets far too large. | The website sets its own root font size. | Set `--nax-font-base` to the same value. |
| The text size changes nothing in some places. | Those places use sizes in `px`. | Only sizes in `rem`, `em` or `%` grow with it. Change the design to `rem`, or tick **Enlarge the whole page** in the module ([Setup](setup.md)). |
| The text size changes nothing on the page, only the panel grows. | The design of the website sets all font sizes in `px`. | Tick **Enlarge the whole page** in the module, or change the design to `rem`. |
| An element changed its look at 145 % or 160 %. | Since 1.0.0 the text size has the steps 115, 130 and 150; stored 145 and 160 become 150. | Style against `data-a11y-font="150"`. |
| High contrast looks wrong in parts. | The mode is generic: black text on white for everything. | Adjust those places with rules under `html[data-a11y-contrast]`. |
| A part of the page is not filtered by Reduce contrast, Reduce colours or Greyscale. | It lies above the panel (`z-index` higher than `--nax-z-index` − 1). | Lower its `z-index` or raise `--nax-z-index`. |
| Scrolling feels slower with a colour filter. | The filter layer is redrawn on every scroll; on weak devices this costs time. | Nothing to change in the website; the visitor can switch the filter off. |
| **Hide images** leaves pictures visible. | They are background pictures of the design (CSS), which stay on purpose. | Hide them with a rule under `html[data-a11y-noimages]` if no text sits on them. |
| **Hide images** shows no description for an image. | The image has no alt text (decorative image). | Give content images a description in the file manager or the content element. |
| **Stop animations** leaves something moving. | The website moves it with its own timers (some slide shows, scripts that scroll). | Pause it in the website's script while `html[data-a11y-motion]` is set. |
| The reading guide or mask is hidden behind an element. | The element has a `z-index` higher than the panel. | Lower its `z-index` or raise `--nax-z-index`. |
| **Page structure** lists no areas. | The page has no `header`, `nav`, `main`, `aside`, `footer` or named sections. | Use these elements in the layout; Contao's layout sections are `header`, `main` and `footer` already. |
| The large cursor does not appear. | A Content Security Policy blocks `data:` images. | Allow `data:` in `img-src`. |
| The inline script in the `<head>` is blocked. | A Content Security Policy from the web server or another extension. | With Contao's own CSP it is allowed automatically; otherwise allow its hash (`'sha256-…'`). |
| "Cookie settings" is missing in the panel. | The button of the cookie banner (on its own or in the menu of the settings button) leads to the cookie settings (then it is left out on purpose); or the cookie tool of the website does not provide `window.cookieConsent.open()`. | Use that button or the menu; or use a cookie tool that provides it. |

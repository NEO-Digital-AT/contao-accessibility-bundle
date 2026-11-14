---
title: Customising
summary: Colours and sizes of the panel through CSS variables, a dark design, adjusting the effects on the page, overriding the template and the texts.
audience: operations
language: en
updated: 2026-11-14
---

# Customising

The panel comes in a neutral design. Everything is adjusted in the website:
CSS variables, rules under the attributes of `<html>`, an own template and
own texts. Nothing in the extension needs to be changed.

## CSS variables

All defaults are declared with zero specificity (`:where(:root)`). Override
them in the website's CSS on `:root` or on `.nax`:

```css
:root {
    --nax-toggle-bg: #123456;
    --nax-accent: #123456;
    --nax-offset-y: 24px;
}
```

### The panel and the button

| Variable | Default | Used for |
| --- | --- | --- |
| `--nax-font-family` | not set (font of the page) | Font of the panel |
| `--nax-font-size` | `1rem` | Base text size of the panel |
| `--nax-bg` | `#ffffff` | Panel background, background of the controls |
| `--nax-text` | `#1a1a1a` | Panel text and icons |
| `--nax-muted` | `#4d4d4d` | Descriptions, group headings, notes |
| `--nax-border` | `#767676` | Edges of the panel and of the controls, switch knob when off |
| `--nax-divider` | `#d9d9d9` | Lines between head, groups and footer |
| `--nax-hover` | `#f0f0f0` | Background of a row or button under the mouse |
| `--nax-accent` | `#1a1a1a` | Switch when on, quick option when pressed |
| `--nax-on-accent` | `#ffffff` | Knob of a switch that is on, text of a pressed quick option |
| `--nax-focus` | `#1a1a1a` | Focus frame inside the panel |
| `--nax-radius` | `12px` | Corners of the panel |
| `--nax-control-radius` | `8px` | Corners of rows and buttons |
| `--nax-shadow` | `0 8px 28px rgba(0, 0, 0, 0.18)` | Shadow of the panel and the button |
| `--nax-panel-width` | `23rem` | Width next to the button (never wider than the screen) |
| `--nax-panel-gap` | `12px` | Space between button and panel |
| `--nax-drawer-width` | `24rem` | Width of the drawer |
| `--nax-center-width` | `32rem` | Width in the centre of the screen |
| `--nax-overlay` | `rgba(0, 0, 0, 0.45)` | Dimmed layer with "Darken the background" |
| `--nax-toggle-size` | `56px` | Size of the round button |
| `--nax-toggle-bg` | `#1a1a1a` | Background of the button, edge of the counter |
| `--nax-toggle-hover` | `#404040` | Background of the button under the mouse |
| `--nax-toggle-color` | `#ffffff` | Icon of the button |
| `--nax-toggle-ring` | `#ffffff` | Ring around the button (keeps it visible on dark pages) |
| `--nax-badge-bg` | not set (`--nax-toggle-color`) | Background of the counter on the button |
| `--nax-badge-text` | not set (`--nax-toggle-bg`) | Number of the counter |
| `--nax-offset-x` | `16px` | Distance from the left or right edge |
| `--nax-offset-y` | `16px` | Distance from the bottom or top edge |
| `--nax-z-index` | `9000` | Stacking order; the colour filters lie one below it |

The default design meets these contrast ratios (WCAG 2.1 AA): panel text
17.4:1, descriptions and group headings 8.45:1 (7.42:1 on the hover
background), pressed quick option 17.4:1, edges of controls 4.54:1, counter
17.4:1, focus frame 17.4:1. If you change the colours, check the new pairs
again, including the hover background.

### A dark design

The panel has no dark mode of its own; a website with a dark design sets the
variables, for example:

```css
.nax {
    --nax-bg: #1e1e1e;
    --nax-text: #f2f2f2;
    --nax-muted: #c8c8c8;
    --nax-border: #a0a0a0;
    --nax-divider: #4a4a4a;
    --nax-hover: #2f2f2f;
    --nax-accent: #f2f2f2;
    --nax-on-accent: #1e1e1e;
    --nax-focus: #f2f2f2;
    --nax-toggle-bg: #f2f2f2;
    --nax-toggle-hover: #d0d0d0;
    --nax-toggle-color: #1e1e1e;
    --nax-toggle-ring: #1e1e1e;
}
```

Measured with this set: text 14.89:1, descriptions 9.96:1 (8.00:1 on the
hover background), edges 6.38:1. Put the block inside
`@media (prefers-color-scheme: dark) { … }` if the website follows the
visitor's system setting.

### The effects on the page

| Variable | Default | Used for |
| --- | --- | --- |
| `--nax-font-base` | `100%` | Root font size of the website, see below |
| `--nax-readable-font` | `Verdana, Tahoma, "Segoe UI", "DejaVu Sans", "Noto Sans", Arial, sans-serif` | Readable font |
| `--nax-dyslexia-font` | `"OpenDyslexic", Verdana, …` | Dyslexia font |
| `--nax-lowcontrast-filter` | `brightness(0.92) contrast(0.8)` | Reduce contrast |
| `--nax-lowsaturation-filter` | `saturate(0.4)` | Reduce colours |
| `--nax-focus-color` | `#000000` | Frame of "Strong focus" and of the button's focus |
| `--nax-focus-halo` | `#ffffff` | Light edge around that frame |
| `--nax-mark-color` | `#c45500` | Outline of buttons (Highlight links), frame of headings, text under the mouse in read aloud |
| `--nax-guide-color` | `#1a1a1a` | Reading guide |
| `--nax-guide-halo` | `#ffffff` | Edge of the reading guide |
| `--nax-guide-size` | `6px` | Thickness of the reading guide |
| `--nax-mask-color` | `rgba(0, 0, 0, 0.6)` | Darkened part of the reading mask |
| `--nax-mask-band` | `7rem` | Height of the bright strip of the reading mask |
| `--nax-reading-bg` | `#ffe066` | Background of the text being read aloud |
| `--nax-reading-text` | `#000000` | Text being read aloud (16.1:1) |
| `--nax-reading-outline` | `#1a1a1a` | Frame of the text being read aloud |
| `--nax-contrast-text` | `#000000` | Text in "High contrast" |
| `--nax-contrast-muted` | `#4d4d4d` | Placeholder text of form fields in "High contrast" |
| `--nax-contrast-bg` | `#ffffff` | Background in "High contrast" |
| `--nax-contrast-link` | `#0000cc` | Links in "High contrast" |
| `--nax-contrast-border` | `#000000` | Borders and edges in "High contrast" |

**Text size on websites with their own root font size.** The panel sets
`font-size: calc(var(--nax-font-base) * factor)` on `<html>`. If the website
sets the root font size itself, for example `html { font-size: 62.5%; }`, set
the same value as the base:

```css
:root {
    --nax-font-base: 62.5%;
}
```

**Text size on websites with font sizes in `px`.** Scaling the root font size
only reaches sizes in `rem`, `em` or `%`. With the module option **Enlarge the
whole page** the text size zooms the content instead: `<html>` gets
`data-a11y-font-zoom` and `--nax-font-zoom` (`1.15`, `1.3` or `1.5`), and every
child of `<body>` except the widget of the panel (`data-nax-host`, set by the
script) gets `zoom: var(--nax-font-zoom)`. Fixed elements in the zoomed content
(the button of NEO Cookie Consent, a chat) grow with it; the buttons at the
edge of the screen of NEO Basic measure them as they are drawn. Media queries do
not change with this zoom, unlike with the zoom of the browser.

## The attributes on `<html>`

Every setting sets an attribute on `<html>`; websites can style against it.
The names do not change between versions.

| Setting | Attribute |
| --- | --- |
| Text size | `data-a11y-font="115"` (`115`, `130` or `150`) and `font-size` on `<html>`; with **Enlarge the whole page** `data-a11y-font-zoom` and `--nax-font-zoom` instead of `font-size` |
| Readable font | `data-a11y-typeface` |
| Dyslexia font | `data-a11y-dyslexia` |
| More spacing | `data-a11y-readable` |
| Align text left | `data-a11y-align` |
| Highlight headings | `data-a11y-headings` |
| Highlight links | `data-a11y-links` |
| Hide images | `data-a11y-noimages` |
| Large cursor | `data-a11y-cursor` |
| High contrast | `data-a11y-contrast` |
| Reduce contrast | `data-a11y-lowcontrast` |
| Reduce colours | `data-a11y-lowsaturation` |
| Greyscale | `data-a11y-grayscale` |
| Stop animations | `data-a11y-motion` |
| Strong focus | `data-a11y-focus` |
| Reading guide | `data-a11y-guide` |
| Reading mask | `data-a11y-mask` |
| Read aloud | `data-a11y-speak` |

Page structure is an action and sets no attribute.

### Adjusting an effect

All effect rules have zero specificity (they are wrapped in `:where()`) and
use `!important`. To change an effect for a part of the website, write a rule
under the same attribute with `!important`:

```css
/* High contrast: keep the dark header, with white text */
html[data-a11y-contrast] .site-header,
html[data-a11y-contrast] .site-header * {
    background-color: #000000 !important;
    color: #ffffff !important;
}

/* More spacing: leave the main navigation as it is */
html[data-a11y-readable] .main-nav li {
    letter-spacing: normal !important;
    word-spacing: normal !important;
}
```

### Icon fonts and the readable fonts

**Readable font** and **Dyslexia font** change the font of all text. Elements
that usually carry an icon font keep theirs: `<i>`, elements with a class
containing "icon" or "symbol", Font Awesome (`fa…`), Bootstrap Icons (`bi`),
elements with `aria-hidden="true"`, and code. If an icon of the website still
turns into letters, mark the element (or a container) with
`data-nax-keep-font`:

```html
<span class="brand-glyph" data-nax-keep-font>logo</span>
```

### How the colour filters work

Reduce contrast, Reduce colours and Greyscale do not put a filter on `<html>`,
`<body>` or a wrapper — that would also filter the panel and make fixed
elements scroll with the page. Instead, a layer over the whole window
(`html::after` with `backdrop-filter`) filters what lies beneath it. It
ignores the mouse and lies just below the panel (`--nax-z-index` − 1).
Elements with a higher `z-index` than that (some cookie banners) are not
filtered. A website that uses `html::after` itself gets this layer instead
while one of the three settings is on.

## Template

The markup is in `contao/templates/frontend_module/neo_accessibility.html.twig`.
To change it, copy it to `templates/frontend_module/neo_accessibility.html.twig`
of the website, or create a variant such as
`templates/frontend_module/neo_accessibility/compact.html.twig` and choose it
in the module under **Custom template**.

The script finds the parts of the panel through `data-nax-*` attributes. Only
the dialog itself (`data-nax`, `data-nax-panel`) is required; every other part
is optional, and the script uses what it finds:

| Attribute | Part |
| --- | --- |
| `data-nax` | The widget (starts `hidden`, the script shows it) |
| `data-nax-voices="device"` or `"any"` | Read aloud: only voices of the device, or also online voices of the browser (module option); missing means `device` |
| `data-nax-panel` | The dialog |
| `data-nax-toggle`, `data-nax-label-active` | Floating button; label with `{count}` while settings are on |
| `data-nax-count` | Counter on the button |
| `data-neo-dock`, `data-neo-dock-corner`, `data-neo-dock-order`, `data-neo-dock-label` (widget), `data-neo-dock-launcher` (floating button), `template[data-neo-dock-icon]` | The buttons at the edge of the screen of NEO Basic ([below](#buttons-at-the-edge-of-the-screen-neo-basic)); only with a floating button |
| `data-nax-font-zoom` | On the widget: the module option **Enlarge the whole page** |
| `data-nax-backdrop` | Dimmed layer of the modal dialog |
| `data-nax-close`, `data-nax-reset`, `data-nax-cookies` | Close, Reset all, Cookie settings |
| `data-nax-font="-1"`, `data-nax-font="1"`, `data-nax-fontval`, `data-nax-format` | Text size |
| `data-nax-flag="<name>"` | A switch (`role="switch"`, names as in the table above) |
| `data-nax-profile="<key>"`, `data-nax-set` | A quick option and the settings it switches (JSON) |
| `data-nax-structure`, `data-nax-structure-list`, `data-nax-texts` | Page structure: button, list, texts (JSON) |
| `data-nax-speak-controls`, `data-nax-speak="page|pause|stop"` | Read aloud: controls |
| `data-nax-msg-again` (on `data-nax-speak-controls`) | Read aloud: the hint after a click on a control was read. Without it, controls act at once and are not read on a click |
| `data-nax-speech-reason`, `data-nax-msg-device`, `data-nax-msg-browser`, `data-nax-msg-unsupported`, `data-nax-format`, `data-nax-label` | Where the script writes why read aloud cannot be used (next to the switch and below the quick options), with the texts for the three cases (`{language}`) and, for a quick option, the format `{label}: {reason}` |
| `data-nax-status`, `data-nax-msg-*` | Announcements for screen readers and their texts |

Variables of the template: `uid`, `position`, `layout`, `modal`, `font_zoom`, `font`,
`switches`, `groups` (list of `{key, features}`), `structure`, `profiles`
(list of `{key, settings}`), `online_voices`, `statement_url`,
`statement_current`, `locale`.

The widget carries one class for the position (`.nax--bottom-left`,
`.nax--bottom-right`, `.nax--top-left`, `.nax--top-right`, `.nax--no-button`),
one for the layout (`.nax--popover`, `.nax--drawer-left`, `.nax--drawer-right`,
`.nax--center`) and `.nax--modal` with the overlay. With other buttons in its
corner, the buttons at the edge of the screen add `data-neo-dock-place`
(`menu` or `stack`) and move it with `--neo-dock-x` and `--neo-dock-y`.

## Buttons at the edge of the screen (NEO Basic)

The floating button takes part in the buttons at the edge of the screen of
NEO Basic, which arrange it with the buttons of other extensions in the same
corner (the button to reopen of NEO Cookie Consent, the chat of NEO
SuperAgent) as set in the page layout. The widget carries
`data-neo-dock="accessibility"`, `data-neo-dock-corner` (the position of the
button), `data-neo-dock-order="20"` and `data-neo-dock-label` (the title of
the panel); the floating button `data-neo-dock-launcher`; the template
`template[data-neo-dock-icon]` holds the icon of the menu entry. The script
writes the number of settings that are on to `data-neo-dock-badge` and
`data-neo-dock-badge-label` for that entry. In the menu the floating button
is hidden and the widget is moved onto the settings button (the panel opens
above it); in a stack it is moved beside the other buttons. Both through
`--neo-dock-x` and `--neo-dock-y`, which the position of the widget and the
height of the popover add. The entry **Cookie settings** in the panel
(`data-nax-cookies`) is shown only while `window.cookieConsent.open()` exists
and the page has no button to reopen (`[data-ncc-reopen]`). Without the
attributes (a template from before) the button keeps its own place.

## Texts

The texts for visitors come from `translations/neo_accessibility.<language>.yaml`,
chosen by the language of the page. To change single texts, put a file with the
same name into the `translations/` folder of the website with only those keys,
for example `translations/neo_accessibility.en.yaml`:

```yaml
button:
    label: "Display settings"
    label_active: "Display settings, {count} switched on"
profile:
    vision:
        label: "Seeing"
```

Keep the words in braces (`{value}`, `{count}`, `{label}`, `{alt}`,
`{level}`, `{language}`, `{reason}`): the panel replaces them. Then clear the cache.

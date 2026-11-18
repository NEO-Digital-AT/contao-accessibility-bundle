# Browser test of the panel

`panel.e2e.cjs` drives the panel of a Contao test installation in Chrome and
checks, with numbers instead of looking: every setting (attribute on `<html>`
and its effect on the page), every quick option (what it switches, pressed
state, off again), settings that exclude each other, text size steps, keyboard
use (open, Tab trap, Esc, focus return), reading guide and mask with pointer
and keyboard, page structure (lists and jump), read aloud (speech output
replaced by a recorder: text, language, voice, highlight, page reading, pause,
stop, Esc), stored settings after a reload and from the first version,
counter, reset, the phone (44 px targets) and 320 px, a browser without
speech output, the English page, axe (WCAG 2.1 A/AA) in light, dark and every
colour mode, and the contrast of every text in the panel. It writes a
screenshot per state. This folder is not part of the package
(`.gitattributes`).

## What the test installation needs

- Contao 5.3 or later with this extension, served at `NAX_BASE` (default
  `http://127.0.0.1:8099`).
- A German and an English root page (English with the URL prefix `en`), each
  with a page layout that holds the panel module with all settings and quick
  options (a new module has them).
- The test pages: `php tests/e2e/seed-test-pages.php <project-dir>`, then
  `php vendor/bin/contao-console contao:symlinks` in the project, so
  `files/a11y-test/` is served. The video needs `ffmpeg` (or put a short
  `loop.webm` there yourself).
- Node.js with Playwright and Chrome or Chromium, and `axe-core`
  (`npm install axe-core` anywhere; the path of `axe.min.js` is the first
  argument).

## Running it

```bash
NAX_CHROME=/usr/bin/google-chrome node tests/e2e/panel.e2e.cjs \
  /path/to/node_modules/axe-core/axe.min.js /tmp/nax-shots
```

`NAX_PLAYWRIGHT` names the Playwright package if it is not installed next to
the script. The exit code is 0 when every check passed; each check prints a
line with `PASS` or `FAIL`. `panel.e2e.cjs` uses the floating button: run it
on pages on which no other button shares its corner (or with the page layout
on *Stacked* or *Side by side*).

## Read aloud on controls

`read-aloud-controls.e2e.cjs` checks, with read aloud on and the speech
output replaced by a recorder, at 1440 px with the mouse and at 390 px by
touch: a click on the question of an accordion, on the label of a checkbox
and on the text of a card whose whole surface is a link reads that text, does
nothing else and shows the hint (a status message); the next click uses it. A
text field reads its label and takes typing at once. With the keyboard, what
gets the focus is read and Enter acts at once; Esc takes the hint back. With
read aloud off every click acts at once. It needs a German page (`NAX_PAGE`)
with the Contao element *Accordion* (two sections) and a form with a text
field `name` and a checkbox `agree` with one option; the card is added by the
test. `NAX_PAGE_HTML` serves a saved copy of that page in its place.

```bash
NAX_PAGE=http://127.0.0.1:8099/vorlesen-test \
node tests/e2e/read-aloud-controls.e2e.cjs /tmp/nax-shots
```

## Text size on a design in px

`text-size.e2e.cjs` makes the paragraphs of a page px-sized and checks at 1440
and 390 px: without the module option **Enlarge the whole page** the root font
size grows and the px text stays; with it the content is zoomed, the panel
keeps its size, nothing scrolls sideways, the panel opens above the zoomed
settings button of the buttons at the edge of the screen (NEO Basic), side by
side the zoomed cookie button sits next to the accessibility button, the
reading guide stays under the pointer, Reset all and A+ work. The page needs
NEO Cookie Consent with **Button to reopen** bottom left and the module bottom
left. Scenarios for `NAX_SWITCH`: `menu` (option off, page layout on *One
settings button with a menu*), `zoom` (option on), `zoom-stacked` (option on,
page layout on *Side by side*).

## Together with other buttons at the edge of the screen

The settings button with its menu, stacks and the other buttons in the same
corner are tested by `tests/e2e/dock.e2e.cjs` of NEO Basic, with the panel of
this extension, the cookie banner of NEO Cookie Consent and the chat of NEO
SuperAgent.

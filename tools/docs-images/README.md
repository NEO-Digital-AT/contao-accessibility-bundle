# Documentation images

The images in `docs/<lang>/bilder/` are screenshots of the real panel and of
the module form in a test installation, marked with the annotation layer of
the `neo-doku` plugin (frames and numbers). `capture.cjs` takes them again,
for example after a change to the panel. This folder is not part of the
package (`.gitattributes`).

## What the test installation needs

The same as the browser test: see `tests/e2e/README.md` (root pages in German
and English, the panel module in their layouts, the test pages from
`tests/e2e/seed-test-pages.php`). For the image of the module form also an
administrator whose back-end language is the language being captured.

## Taking the images

```bash
NAX_CHROME=/usr/bin/google-chrome NAX_LANG=en NAX_MODULE=<module id> \
NAX_ADMIN_USER=<user> NAX_ADMIN_PASSWORD=<password> \
node tools/docs-images/capture.cjs
```

Run it once per language (`NAX_LANG=en`, `NAX_LANG=de`), each time with an
administrator in that back-end language. Without the three back-end variables
the script leaves the image of the module form as it is. The password comes
from the environment, never from a file in the repository.

`NAX_ANNOTATE` is the path of `annotate.js` if the plugins are not checked out
next to this repository (`../NEO-Claude-Plugins/plugins/neo-doku/scripts/`).
`NAX_PAGE_EN` and `NAX_PAGE_DE` take the images on other pages than the test
pages; `NAX_IMAGES` takes only the images named (comma separated: `panel`,
`structure`, `speak`, `speak-disabled`, `mask`, `phone`, `module-settings`,
`menu`).

| Image | Shows |
| --- | --- |
| `panel.png` | The whole panel, numbered 1–5 as in the user manual |
| `structure.png` | Page structure, opened |
| `speak.png` | Read aloud: switch, controls, the text being read |
| `mask.png` | The reading mask on the page |
| `phone.png` | The panel at 390 px with the quick option "Low vision" |
| `module-settings.png` | The module form: offered settings, quick options, online voices |
| `speak-disabled.png`, `speak-disabled-profile.png` | Read aloud greyed out with the reason (no voice for the page language) |
| `menu.png` | The settings button of the buttons at the edge of the screen (NEO Basic) with its menu: the settings button (1) and the entry Accessibility (2). Only taken where a second button shares the corner of the panel button (e.g. the button to reopen of NEO Cookie Consent) and the page layout is on *One settings button with a menu*; the images in the documentation were taken on the dock test page of NEO Basic |

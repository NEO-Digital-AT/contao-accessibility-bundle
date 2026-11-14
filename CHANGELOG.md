# Changelog

All notable changes to this extension are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/); versions follow
[Semantic Versioning](https://semver.org/).

## [1.0.0] - 2026-11-13

Initial release. Runs on Contao 5.3 to 6.0 with PHP 8.2 to 8.5, as far as the Contao version supports it (Contao 5.7 needs PHP 8.3, Contao 6.0 PHP 8.4).

### Added
- Front-end module with a button that opens an accessibility panel; the choices are kept in the visitor's browser.
- **Quick options:** low vision, dyslexia, concentration, epilepsy, motor skills, read aloud — each switches a set of settings at once.
- **Text and content:** text size up to 150 %, readable font, dyslexia font (loaded only when switched on), more spacing (WCAG 1.4.12), text aligned left, highlighted headings and links, hidden images with their description, large cursor.
- **Colours:** high contrast, reduced contrast, reduced colours, greyscale. **Orientation:** stop animations, strong focus, reading guide, reading mask, page structure with jumps, read aloud with a voice of the visitor's device.
- Link to the accessibility statement and the cookie settings, in the language of the page; **Reset all**.
- Dialog for screen readers and keyboard, touch targets of 44 px, works from 320 px width; every setting and quick option can be switched off per module; button in any corner or as a link.
- Neutral design through CSS variables, overridable template, texts in eleven languages, no external requests; documentation in English and German; unit tests.
- Text size: the documentation and the back end say that it needs font sizes in `rem`, `em` or `%` in the design. New module option **Enlarge the whole page** (off by default) for designs with sizes in `px`: the text size then zooms the content of the page, the panel keeps its size and stays next to the zoomed buttons of its corner.
- Read aloud also reads controls: a click on a link, a button, the question of an accordion, the label of a checkbox or a card whose surface is a link reads the text under the pointer and does nothing else; a hint next to it, announced to screen readers too, says that the next click uses it. With the keyboard, whatever gets the focus is read and Enter or Space act at once; fields for typing read their label.
- The floating button takes part in the **buttons at the edge of the screen** of NEO Basic (`data-neo-dock` attributes): with buttons of other extensions in the same corner (cookie settings, AI chat) it becomes the entry **Accessibility** of one settings button with a menu (with the number of settings that are on), or is stacked with them, as set in the page layout; the panel opens next to the settings button or its own button. The entry **Cookie settings** in the panel only appears while the page has no button to reopen the cookie settings.

After installing: update the database (new field `nax_font_zoom`), then create the front-end module **Accessibility panel** and add it to the page layout. Websites that override the template `frontend_module/neo_accessibility.html.twig` take over the `data-neo-dock*` attributes of the widget and the floating button and `template[data-neo-dock-icon]`; without them the button keeps its own place.

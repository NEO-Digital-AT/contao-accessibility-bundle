---
title: Permissions
summary: The Contao permissions an editor needs to create and change the accessibility panel, with an example user group.
audience: operations
language: en
updated: 2026-11-14
---

# Permissions

The panel has no switches of its own in users or user groups. Visitors need no
account. Administrators can do everything; editors need these Contao
permissions to set the panel up.

## Permissions per task

| Task | Contao permission (user group) |
| --- | --- |
| See and edit the module | **Back end modules:** Themes · **Theme modules:** Front end modules |
| Change which settings and quick options are offered | **Allowed fields › Front end modules:** Offered settings (`nax_features`), Enlarge the whole page (`nax_font_zoom`), Quick options (`nax_profiles`), Allow the browser's online voices (`nax_online_voices`) |
| Change button, layout, overlay | **Allowed fields › Front end modules:** Position of the button (`nax_position`), Where the panel opens (`nax_layout`), Darken the background (`nax_overlay`) |
| Link the accessibility statement | **Allowed fields › Front end modules:** Accessibility statement (`nax_statement`); for choosing the page also **Pagemounts** that include it |
| Place the module in a page layout | **Theme modules:** Page layouts, and the allowed fields of the page layout |
| Place the link `{{accessibility::link}}` in content | The rights to edit that content (articles, content elements) |

The licence box at the top of the module form is shown to everyone who may
edit the module; it needs no field permission.

## Example user group "Web design"

For a person who looks after the look of the website, but not the content:

1. **Back end modules:** Themes.
2. **Theme modules:** Front end modules, Page layouts.
3. **Allowed fields › Front end modules:** Offered settings, Enlarge the whole page, Quick options,
   Allow the browser's online voices, Position of the button, Where the panel opens, Darken the background,
   Accessibility statement (besides the fields Contao needs for every module,
   such as Title and Module type).
4. **Pagemounts:** the website, so the accessibility statement can be chosen.

## Several websites in one installation

Each website (root page) usually has its own page layout and theme. Create a
panel module per theme or reuse one module in several layouts; the texts for
visitors follow the language of each page.

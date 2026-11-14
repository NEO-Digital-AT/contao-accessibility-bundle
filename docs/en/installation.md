---
title: Installation
summary: Requirements, installing with the Contao Manager or Composer, the database update, updating to a new version and removing the extension.
audience: operations
language: en
updated: 2026-11-12
---

# Installation

## Requirements

- **Contao 5.3 LTS to 5.7 and Contao 6.0** (`contao/core-bundle ^5.3 || ^6.0`)
  and **PHP 8.2+** (Contao 6: PHP 8.4+).
- The Basic bundle `neo-digital/contao-basic-bundle` (installed as a
  dependency). It provides the group **NEO Digital** among the front-end
  modules; this extension needs no licence key and no access key from it.
- Visitors need a current browser. In very old browsers the button does not
  appear or some effects do not apply; the website itself is not affected.
  Read aloud needs the browser's speech output; where a browser has none, the
  panel leaves that option out.

## Contao Manager

1. Search for `neo-digital/contao-accessibility-bundle`, add it and apply the
   changes.
2. Update the database when the Contao Manager offers it (seven columns in
   `tl_module`, see below).

## Composer

```bash
composer require neo-digital/contao-accessibility-bundle
php vendor/bin/contao-console contao:migrate
```

The bundle registers itself through its Contao Manager plugin; no
configuration file is needed.

## What the installation adds

- **Database:** seven columns in `tl_module` for the module settings
  (`nax_features`, `nax_profiles`, `nax_position`, `nax_layout`,
  `nax_online_voices`, `nax_overlay`, `nax_statement`). No table of its own,
  nothing about visitors.
- **Public files** under `bundles/neoaccessibility/`: `accessibility.css`,
  `accessibility.js` and the dyslexia font OpenDyslexic
  (`fonts/OpenDyslexic-Regular.woff2`, `fonts/OpenDyslexic-Bold.woff2`, with
  its licence `fonts/OpenDyslexic-OFL.txt`). Composer publishes them; if they
  are missing, run `php vendor/bin/contao-console assets:install public`.

Then set the module up: [Setup](setup.md).

## Updating to a new version

1. Update the package (Contao Manager or `composer update
   neo-digital/contao-accessibility-bundle`).
2. Run the database update (`contao:migrate`). It adds new columns and runs
   the migrations of the extension. Nothing is removed.
3. Clear the cache if the Contao Manager does not do it.

What a migration does is listed in the `CHANGELOG.md` under the version. The
visitors' stored choices stay valid across updates: setting names never
change.

**From an earlier test version to 1.0.0 with the new settings:** a panel
module that offered all eight settings of the first version gets the new
settings ticked once, read aloud included (it uses only voices of the
visitor's device unless online voices are allowed, see [Privacy](privacy.md)).
A module with a narrower choice keeps it; tick the new settings there if you
want them. A module that never stored a choice
offers everything.

## Removing

1. Remove the panel module from the page layouts and delete the module.
2. Remove the credit from the legal notice only if you also stop using the
   extension.
3. Remove the package (Contao Manager or `composer remove
   neo-digital/contao-accessibility-bundle`) and run the database update; the
   Contao Manager offers to drop the seven columns.

The entry `nax-settings` stays in the browsers of visitors who used the panel
until they clear their browser data; without the extension it has no effect.

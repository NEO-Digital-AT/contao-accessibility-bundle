---
title: Privacy
summary: What the panel stores and where, what read aloud sends to whom, the dyslexia font, and what belongs in the website's privacy policy.
audience: operations
language: en
updated: 2026-11-12
---

# Privacy

## What is stored

- The visitor's choices are stored in the browser's `localStorage` under the
  key `nax-settings`, for example `{"contrast":true,"font":130}`. Nothing
  else is stored, and nothing about the visitor.
- The entry is written only when the visitor changes a setting, and removed
  again when all settings are off (**Reset all**). It stays until then or
  until the visitor clears the browser data.
- No cookies, no tracking, no requests to the website's server or to third
  parties for storing or applying the choices.
- Nothing is stored in the website's database; the extension has no table for
  visitors.

## Dyslexia font

The font OpenDyslexic is part of the extension and is loaded from the
website's own server (`bundles/neoaccessibility/fonts/`), and only when a
visitor switches **Dyslexia font** on. No font service is involved.

## Read aloud

**Read aloud** uses the speech output of the visitor's browser (Web Speech
API). The extension itself sends nothing anywhere: it hands the text to the
browser, the browser reads it out. Which voice reads decides whether the text
leaves the device:

- Voices installed on the device (for example the system voices of Windows,
  macOS and iOS, which Firefox and Safari use) read the text on the device.
- Online voices (for example the "Google" voices of Chrome on a computer and
  the "Online" voices of Microsoft Edge) send the text being read, and with
  it the visitor's request, to a service of the browser's maker, which returns
  the speech. That happens under the terms of the browser's maker; the
  website does not receive or control it.

### By default: only voices of the device

Out of the box the panel reads **only with voices installed on the visitor's
device** (the browser marks them as such). It never leaves the choice of voice
to the browser:

- It waits until the browser has reported its voices (some browsers report
  them a moment after the page loads; the panel waits up to 2.5 seconds).
  Nothing is spoken before, and never without a voice chosen by the panel —
  the browser's default voice can be an online voice.
- If the device has no voice for the language of the page, **Read aloud** and
  the quick option **Read aloud** are disabled, and the reason stands next to
  them in the visitor's language, for example "No voice for German is
  installed on this device." A text in another language without a device
  voice is skipped.

With this default no text leaves the device, and read aloud needs no mention
beyond the stored choices. Visitors see the note "Uses a voice installed on
your device. The text stays on your device."

### Allowing online voices

The module option **Allow the browser's online voices** (off by default,
[Setup](setup.md)) lets read aloud use online voices too, where the device has
no voice for the language; device voices still come first. Then the browser
sends the text being read to its maker, and **the privacy policy must say
so**. The module form shows a note at the option as long as it is on. Visitors
then see: "Uses the voices of your browser. Where your device has none for the
language, some browsers send the text to an online service of their maker to
read it out." The text sent is the text of the page the visitor asked to hear,
not data the visitor entered.

## What belongs in the privacy policy

A suggestion to adapt (not legal advice):

> **Accessibility panel.** This website offers a panel with display aids. Your
> choices are stored only in your browser (local storage, entry
> "nax-settings") and are not sent to us. The function "Read aloud" uses a
> voice installed on your device; the text stays on your device.

Only with **Allow the browser's online voices** on, add:

> Where your device has no voice for the language, "Read aloud" uses an online
> voice of your browser; your browser then sends the text being read to a
> service of its maker (for example Google or Microsoft) to produce the
> speech. We do not receive this text and have no influence on that
> processing.

## Consent

Storing the choices serves a function the visitor explicitly asks for; such
storage generally needs no consent (Article 5(3) of the ePrivacy Directive).
Read aloud is started by the visitor in the browser's own function; by default
the text stays on the device. Ask your data protection officer whether your
website needs more, in particular before allowing online voices; this is not
legal advice. If you do not want to offer read aloud, untick it in the module
([Setup](setup.md)).

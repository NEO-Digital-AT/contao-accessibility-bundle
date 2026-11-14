# NEO Accessibility for Contao 5 — accessibility panel

A front-end module for Contao 5: a button opens a panel in which visitors adjust how the website is displayed for them — with six quick options or with single settings for text, colours and orientation, from a readable font and a reading mask to read aloud. The choices are kept in the visitor's browser and applied on every page before it is painted. Nothing is sent to the website's server.

**What the panel is not.** It offers display aids for visitors. It does **not** make a website accessible and does not make it conform to WCAG, EN 301 549, the European Accessibility Act or national accessibility laws. Accessibility comes from the website itself: its structure, texts, colours, forms and keyboard operation. The panel does not detect or repair anything.

Free to use with a credit in the legal notice — see [License](#license).

*Deutsche Fassung weiter unten: [Deutsch](#neo-accessibility-für-contao-5--barrierefreiheits-panel-deutsch).*

## Features

- **Quick options:** low vision, dyslexia, concentration, epilepsy, motor skills, read aloud — each switches a set of settings at once.
- **Text and content:** text size up to 150 % (needs font sizes in `rem`, `em` or `%` in the design; for designs in `px` the module can enlarge the whole page instead), readable font, dyslexia font (OpenDyslexic, loaded only when switched on), more spacing (WCAG 1.4.12), text aligned left, highlighted headings and links, hidden images with their description shown, large cursor.
- **Colours:** high contrast, reduced contrast, reduced colours, greyscale — the panel and fixed elements stay as they are.
- **Orientation:** stop animations (also videos that play by themselves), strong focus, reading guide, reading mask, page structure with jumps to areas and headings, read aloud with a voice of the visitor's device (online voices of the browser only if the website allows them).
- A counter on the button, **Reset all**, a link to the accessibility statement and to the cookie settings.
- Shares its corner with the buttons of other extensions (cookie settings, AI chat): one settings button with a menu, or stacked one above the other or side by side, as set in the page layout (buttons at the edge of the screen of NEO Basic).
- Dialog for screen readers and keyboard (switches, Esc, focus return), touch targets of 44 px, works from 320 px width.
- Every setting and quick option can be switched off per module; button in any corner or a link (`{{accessibility::link}}`), panel next to the button, as a drawer or centred, optionally modal.
- Neutral design through CSS variables (`--nax-*`), overridable template, texts in eleven languages, no dependencies, no external requests.

## Requirements

- **Contao 5.3 LTS to 5.7 and Contao 6.0** (`contao/core-bundle ^5.3 || ^6.0`) and **PHP 8.2+** (Contao 6: PHP 8.4+).
- The Basic bundle (`neo-digital/contao-basic-bundle`, installed as a dependency); no licence key or access key is needed from it.
- The text size needs font sizes in `rem`, `em` or `%` in the design of the website; with sizes in `px` tick **Enlarge the whole page** in the module ([Setup](docs/en/setup.md)).
- Visitors need a current browser; read aloud needs the browser's speech output and a voice for the page language on the device (otherwise the option is greyed out with the reason).

## Installation

### Contao Manager

1. Search for `neo-digital/contao-accessibility-bundle`, add it and apply the changes.
2. Update the database when the Contao Manager offers it.

### Composer

```bash
composer require neo-digital/contao-accessibility-bundle
php vendor/bin/contao-console contao:migrate
```

Then create the module and add it to the page layout: [Setup](docs/en/setup.md).

## Together with other buttons at the edge of the screen

The floating button takes part in the buttons at the edge of the screen of NEO Basic (installed with this extension). When buttons of other extensions sit in the same corner, e.g. the button to reopen of NEO Cookie Consent or the chat of NEO SuperAgent, they are arranged together as set in the page layout (**Buttons at the edge of the screen**): one settings button with a menu that holds **Accessibility** (the default), stacked one above the other, or side by side. Alone in its corner the button stays as it is. The extensions do not depend on each other, and the order in which their scripts load does not matter. Details: [Setup](docs/en/setup.md#together-with-other-buttons-in-the-same-corner), [Customising](docs/en/customising.md#buttons-at-the-edge-of-the-screen-neo-basic).

## Documentation

The documentation comes with the extension: [docs/en/README.md](docs/en/README.md).

- [Installation](docs/en/installation.md) — requirements, updating, removing
- [Setup](docs/en/setup.md) — the module form, page layout, insert tag, credit
- [Permissions](docs/en/permissions.md) — what editors need
- [User manual](docs/en/usage.md) — every quick option and setting as visitors see them
- [Customising](docs/en/customising.md) — CSS variables, dark design, effects, template, texts
- [Privacy](docs/en/privacy.md) — what is stored, read aloud and the voices
- [Troubleshooting](docs/en/troubleshooting.md)

## Development

The unit tests (PHPUnit 11, on PHP 8.2 with Contao 5.3 PHPUnit 10.5; folder `tests/`) need neither a database server nor a Contao installation:

```
composer install
composer test
```

The workflow `.github/workflows/tests.yml` runs them on PHP 8.2 to 8.5 and checks `composer.json` with `composer validate`.

## License

Free of charge on any number of websites. Condition: the legal notice of every website on which the extension runs publicly names "NEO Accessibility" and "NEO Digital" with a link to https://neo-digital.at, for example: Accessibility panel: NEO Accessibility by [NEO Digital](https://neo-digital.at). The back end shows this condition at the top of the module form (section "Licence"), with the credit as text with link and as HTML to copy; the link carries `rel="nofollow"`.

The version available at the time of the order (for free use: of the download) for the then current Contao long-term support version works with it until its end of life; there is no entitlement to versions for later long-term support versions. Support by separate agreement. All rights reserved; redistribution, resale and offering it as your own product or service are not permitted. Full terms: [LICENSE](LICENSE).

The dyslexia font OpenDyslexic (`public/fonts/`) is by its own authors under the SIL Open Font License 1.1 ([public/fonts/OpenDyslexic-OFL.txt](public/fonts/OpenDyslexic-OFL.txt)); it is not covered by the licence of the extension.

---

# NEO Accessibility für Contao 5 — Barrierefreiheits-Panel (Deutsch)

Ein Frontend-Modul für Contao 5: Eine Schaltfläche öffnet ein Panel, in dem Besucher die Darstellung der Website für sich anpassen — mit sechs Schnellauswahlen oder einzelnen Einstellungen für Text, Farben und Orientierung, von einer lesbaren Schrift und einer Lesemaske bis zum Vorlesen. Die Auswahl bleibt im Browser des Besuchers und wird auf jeder Seite angewendet, bevor sie gezeichnet wird. Nichts wird an den Server der Website geschickt.

**Was das Panel nicht ist.** Es bietet Besuchern Hilfen zur Darstellung. Es macht eine Website **nicht** barrierefrei und nicht konform zu WCAG, EN 301 549, dem European Accessibility Act (in Österreich BaFG) oder anderen Gesetzen zur Barrierefreiheit. Barrierefreiheit entsteht in der Website selbst: in Aufbau, Texten, Farben, Formularen und Tastaturbedienung. Das Panel erkennt und repariert nichts.

Kostenlos mit Nennung im Impressum, siehe [Lizenz](#lizenz).

## Funktionen

- **Schnellauswahl:** Sehschwäche, Legasthenie, Konzentration, Epilepsie, Motorik, Vorlesen — jede schaltet mehrere Einstellungen auf einmal.
- **Text und Inhalt:** Schriftgröße bis 150 % (setzt Schriftgrößen in `rem`, `em` oder `%` in der Gestaltung voraus; für Gestaltungen in `px` kann das Modul stattdessen die ganze Seite vergrößern), lesbare Schrift, Legasthenie-Schrift (OpenDyslexic, wird erst beim Einschalten geladen), mehr Abstand (WCAG 1.4.12), Text linksbündig, hervorgehobene Überschriften und Links, ausgeblendete Bilder mit ihrer Beschreibung, großer Mauszeiger.
- **Farben:** hoher Kontrast, verringerter Kontrast, abgeschwächte Farben, Graustufen — Panel und fest stehende Elemente bleiben, wie sie sind.
- **Orientierung:** Animationen anhalten (auch Videos, die von selbst laufen), Fokus verstärken, Leselinie, Lesemaske, Seitenstruktur mit Sprüngen zu Bereichen und Überschriften, Vorlesen mit einer Stimme des Geräts (Online-Stimmen des Browsers nur, wenn die Website sie erlaubt).
- Ein Zähler auf der Schaltfläche, **Alles zurücksetzen**, ein Link zur Erklärung zur Barrierefreiheit und zu den Cookie-Einstellungen.
- Teilt sich seine Ecke mit den Schaltflächen anderer Erweiterungen (Cookie-Einstellungen, KI-Chat): eine Einstellungs-Schaltfläche mit Menü oder übereinander oder nebeneinander gestapelt, wie im Seitenlayout eingestellt (Schaltflächen am Bildschirmrand von NEO Basic).
- Dialog für Vorlesegeräte und Tastatur (Schalter, Esc, Fokus-Rückgabe), Bedienflächen von 44 px, ab 320 px Breite nutzbar.
- Jede Einstellung und Schnellauswahl lässt sich je Modul abschalten; Schaltfläche in jeder Ecke oder ein Link (`{{accessibility::link}}`), Panel neben der Schaltfläche, als Leiste oder mittig, auf Wunsch modal.
- Neutrales Design über CSS-Variablen (`--nax-*`), überschreibbares Template, Texte in elf Sprachen, keine Abhängigkeiten, keine externen Anfragen.

## Voraussetzungen

- **Contao 5.3 LTS bis 5.7 und Contao 6.0** (`contao/core-bundle ^5.3 || ^6.0`) und **PHP 8.2+** (Contao 6: PHP 8.4+).
- Das Basic-Bundle (`neo-digital/contao-basic-bundle`, wird als Abhängigkeit mitinstalliert); es braucht von dort weder Lizenz- noch Zugangsschlüssel.
- Die Schriftgröße setzt Schriftgrößen in `rem`, `em` oder `%` in der Gestaltung der Website voraus; bei Größen in `px` im Modul **Ganze Seite vergrößern** anhaken ([Einrichtung](docs/de/setup.md)).
- Besucher brauchen einen aktuellen Browser; Vorlesen braucht die Sprachausgabe des Browsers und eine Stimme für die Sprache der Seite auf dem Gerät (sonst ist die Einstellung mit dem Grund ausgegraut).

## Installation

### Contao Manager

1. Nach `neo-digital/contao-accessibility-bundle` suchen, hinzufügen und die Änderungen anwenden.
2. Die Datenbank aktualisieren, wenn der Contao Manager es anbietet.

### Composer

```bash
composer require neo-digital/contao-accessibility-bundle
php vendor/bin/contao-console contao:migrate
```

Danach das Modul anlegen und ins Seitenlayout einbinden: [Einrichtung](docs/de/setup.md).

## Zusammen mit anderen Schaltflächen am Bildschirmrand

Die schwebende Schaltfläche gehört zu den Schaltflächen am Bildschirmrand von NEO Basic (wird mit dieser Erweiterung installiert). Sitzen Schaltflächen anderer Erweiterungen in derselben Ecke, etwa der Knopf zum Wiederöffnen von NEO Cookie Consent oder der Chat von NEO SuperAgent, werden sie gemeinsam angeordnet, wie es im Seitenlayout eingestellt ist (**Schaltflächen am Bildschirmrand**): eine Einstellungs-Schaltfläche mit Menü, das **Barrierefreiheit** enthält (die Vorgabe), übereinander oder nebeneinander gestapelt. Allein in ihrer Ecke bleibt die Schaltfläche, wie sie ist. Die Erweiterungen hängen nicht voneinander ab, und in welcher Reihenfolge ihre Skripte laden, spielt keine Rolle. Einzelheiten: [Einrichtung](docs/de/setup.md#zusammen-mit-anderen-schaltflächen-in-derselben-ecke), [Anpassen](docs/de/customising.md#schaltflächen-am-bildschirmrand-neo-basic).

## Dokumentation

Die Dokumentation liegt der Erweiterung bei: [docs/de/README.md](docs/de/README.md).

- [Installation](docs/de/installation.md) — Voraussetzungen, Aktualisieren, Entfernen
- [Einrichtung](docs/de/setup.md) — das Modulformular, Seitenlayout, Insert-Tag, Hinweis im Impressum
- [Rechte](docs/de/permissions.md) — was Redakteure brauchen
- [Bedienungsanleitung](docs/de/usage.md) — jede Schnellauswahl und Einstellung, wie Besucher sie sehen
- [Anpassen](docs/de/customising.md) — CSS-Variablen, dunkles Design, Effekte, Template, Texte
- [Datenschutz](docs/de/privacy.md) — was gespeichert wird, Vorlesen und die Stimmen
- [Fehlersuche](docs/de/troubleshooting.md)

## Entwicklung

Die Unit-Tests (PHPUnit 11, auf PHP 8.2 mit Contao 5.3 PHPUnit 10.5; Ordner `tests/`) brauchen weder einen Datenbankserver noch eine Contao-Installation:

```
composer install
composer test
```

Der Workflow `.github/workflows/tests.yml` führt sie auf PHP 8.2 bis 8.5 aus und prüft `composer.json` mit `composer validate`.

## Lizenz

Kostenlos auf beliebig vielen Websites. Bedingung: Im Impressum jeder Website, auf der die Erweiterung öffentlich läuft, werden „NEO Accessibility“ und „NEO Digital“ mit einem Link auf https://neo-digital.at genannt, zum Beispiel: Barrierefreiheits-Panel: NEO Accessibility von [NEO Digital](https://neo-digital.at). Das Backend zeigt diese Bedingung oben im Formular des Moduls (Abschnitt „Lizenz“), mit dem Hinweis als Text mit Link und als HTML zum Kopieren; der Link trägt `rel="nofollow"`.

Die bei der Bestellung (bei unentgeltlicher Nutzung: beim Herunterladen) für die dann aktuelle Long-Term-Support-Version von Contao bereitstehende Fassung funktioniert mit dieser bis zu deren End of Life; ein Anspruch auf Fassungen für spätere Long-Term-Support-Versionen besteht nicht. Support nach gesonderter Vereinbarung. Alle Rechte vorbehalten; Weitergabe, Weiterverkauf und das Anbieten als eigenes Produkt oder als Dienst sind nicht erlaubt. Vollständige Bedingungen: [LICENSE](LICENSE).

Die Legasthenie-Schrift OpenDyslexic (`public/fonts/`) stammt von ihren eigenen Urhebern und steht unter der SIL Open Font License 1.1 ([public/fonts/OpenDyslexic-OFL.txt](public/fonts/OpenDyslexic-OFL.txt)); die Lizenz der Erweiterung gilt für sie nicht.

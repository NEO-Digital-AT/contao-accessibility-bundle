---
titel: Installation
kurzfassung: Voraussetzungen, Installation mit dem Contao Manager oder Composer, die Datenbankaktualisierung, Aktualisieren auf eine neue Fassung und Entfernen der Erweiterung.
zielgruppe: betrieb
sprache: de
stand: 2026-11-12
---

# Installation

## Voraussetzungen

- **Contao 5.3 LTS bis 5.7 und Contao 6.0** (`contao/core-bundle ^5.3 || ^6.0`)
  und **PHP 8.2+** (Contao 6: PHP 8.4+).
- Das Basic-Bundle `neo-digital/contao-basic-bundle` (wird als Abhängigkeit
  mitinstalliert). Es stellt die Gruppe **NEO Digital** bei den
  Frontend-Modulen bereit; diese Erweiterung braucht von dort weder Lizenz-
  noch Zugangsschlüssel.
- Besucher brauchen einen aktuellen Browser. In sehr alten Browsern erscheint
  die Schaltfläche nicht oder einzelne Effekte greifen nicht; die Website
  selbst bleibt unberührt. Vorlesen braucht die Sprachausgabe des Browsers;
  wo ein Browser keine hat, lässt das Panel diese Einstellung weg.

## Contao Manager

1. Nach `neo-digital/contao-accessibility-bundle` suchen, hinzufügen und die
   Änderungen anwenden.
2. Die Datenbank aktualisieren, wenn der Contao Manager es anbietet (sieben
   Spalten in `tl_module`, siehe unten).

## Composer

```bash
composer require neo-digital/contao-accessibility-bundle
php vendor/bin/contao-console contao:migrate
```

Das Bundle registriert sich über sein Contao-Manager-Plugin selbst; eine
Konfigurationsdatei ist nicht nötig.

## Was die Installation ergänzt

- **Datenbank:** sieben Spalten in `tl_module` für die Moduleinstellungen
  (`nax_features`, `nax_profiles`, `nax_position`, `nax_layout`,
  `nax_online_voices`, `nax_overlay`, `nax_statement`). Keine eigene Tabelle,
  nichts über Besucher.
- **Öffentliche Dateien** unter `bundles/neoaccessibility/`:
  `accessibility.css`, `accessibility.js` und die Legasthenie-Schrift
  OpenDyslexic (`fonts/OpenDyslexic-Regular.woff2`,
  `fonts/OpenDyslexic-Bold.woff2`, mit ihrer Lizenz
  `fonts/OpenDyslexic-OFL.txt`). Composer veröffentlicht sie; fehlen sie,
  `php vendor/bin/contao-console assets:install public` ausführen.

Danach das Modul einrichten: [Einrichtung](setup.md).

## Auf eine neue Fassung aktualisieren

1. Das Paket aktualisieren (Contao Manager oder `composer update
   neo-digital/contao-accessibility-bundle`).
2. Die Datenbankaktualisierung ausführen (`contao:migrate`). Sie ergänzt neue
   Spalten und führt die Migrationen der Erweiterung aus. Nichts wird
   entfernt.
3. Den Cache leeren, falls der Contao Manager es nicht tut.

Was eine Migration tut, steht im `CHANGELOG.md` bei der Fassung. Die
gespeicherte Auswahl der Besucher bleibt über Aktualisierungen gültig: Die
Namen der Einstellungen ändern sich nie.

**Von einer früheren Testfassung auf 1.0.0 mit den neuen Einstellungen:** Ein
Panel-Modul, das alle acht Einstellungen der ersten Fassung angeboten hat,
bekommt die neuen Einstellungen einmalig angehakt, Vorlesen eingeschlossen (es
nutzt nur Stimmen des Geräts, solange Online-Stimmen nicht erlaubt sind, siehe
[Datenschutz](privacy.md)). Ein Modul mit engerer Auswahl behält sie; dort die
neuen Einstellungen anhaken, wenn sie gewünscht sind. Ein Modul, das nie eine Auswahl gespeichert hat, bietet alles
an.

## Entfernen

1. Das Panel-Modul aus den Seitenlayouts nehmen und das Modul löschen.
2. Den Hinweis im Impressum erst entfernen, wenn die Erweiterung nicht mehr
   läuft.
3. Das Paket entfernen (Contao Manager oder `composer remove
   neo-digital/contao-accessibility-bundle`) und die Datenbank aktualisieren;
   der Contao Manager bietet an, die sieben Spalten zu löschen.

Der Eintrag `nax-settings` bleibt im Browser von Besuchern, die das Panel
genutzt haben, bis sie ihre Browserdaten löschen; ohne die Erweiterung wirkt
er nicht.

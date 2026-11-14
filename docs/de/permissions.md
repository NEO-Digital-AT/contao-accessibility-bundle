---
titel: Rechte
kurzfassung: Die Contao-Rechte, die ein Redakteur zum Anlegen und Ändern des Barrierefreiheits-Panels braucht, mit einer Beispielgruppe.
zielgruppe: betrieb
sprache: de
stand: 2026-11-14
---

# Rechte

Das Panel hat keine eigenen Schalter bei Benutzern oder Benutzergruppen.
Besucher brauchen kein Konto. Administratoren dürfen alles; Redakteure
brauchen diese Contao-Rechte, um das Panel einzurichten.

## Rechte je Aufgabe

| Aufgabe | Contao-Recht (Benutzergruppe) |
| --- | --- |
| Das Modul sehen und bearbeiten | **Backend-Module:** Themes · **Theme-Module:** Frontend-Module |
| Ändern, welche Einstellungen und Schnellauswahlen angeboten werden | **Erlaubte Felder › Frontend-Module:** Angebotene Einstellungen (`nax_features`), Ganze Seite vergrößern (`nax_font_zoom`), Schnellauswahl (`nax_profiles`), Online-Stimmen des Browsers erlauben (`nax_online_voices`) |
| Schaltfläche, Variante, abgedunkelten Hintergrund ändern | **Erlaubte Felder › Frontend-Module:** Position der Schaltfläche (`nax_position`), Wo sich das Panel öffnet (`nax_layout`), Hintergrund abdunkeln (`nax_overlay`) |
| Die Erklärung zur Barrierefreiheit verlinken | **Erlaubte Felder › Frontend-Module:** Erklärung zur Barrierefreiheit (`nax_statement`); zum Auswählen der Seite auch **Pagemounts**, die sie enthalten |
| Das Modul in ein Seitenlayout einbinden | **Theme-Module:** Seitenlayouts, dazu die erlaubten Felder des Seitenlayouts |
| Den Link `{{accessibility::link}}` in Inhalte setzen | Die Rechte zum Bearbeiten dieser Inhalte (Artikel, Inhaltselemente) |

Der Lizenzkasten oben im Modulformular erscheint für jeden, der das Modul
bearbeiten darf; er braucht kein Feldrecht.

## Beispielgruppe „Webdesign“

Für jemanden, der sich um das Aussehen der Website kümmert, nicht um Inhalte:

1. **Backend-Module:** Themes.
2. **Theme-Module:** Frontend-Module, Seitenlayouts.
3. **Erlaubte Felder › Frontend-Module:** Angebotene Einstellungen, Ganze Seite vergrößern,
   Schnellauswahl, Online-Stimmen des Browsers erlauben, Position der
   Schaltfläche, Wo sich das Panel öffnet,
   Hintergrund abdunkeln, Erklärung zur Barrierefreiheit (neben den Feldern,
   die Contao für jedes Modul braucht, etwa Titel und Modultyp).
4. **Pagemounts:** die Website, damit die Erklärung zur Barrierefreiheit
   wählbar ist.

## Mehrere Websites in einer Installation

Jede Website (Startpunkt) hat meist ein eigenes Seitenlayout und Theme. Je
Theme ein Panel-Modul anlegen oder ein Modul in mehreren Layouts nutzen; die
Texte für Besucher folgen der Sprache der jeweiligen Seite.

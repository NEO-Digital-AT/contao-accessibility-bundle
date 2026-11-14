---
titel: Anpassen
kurzfassung: Farben und Größen des Panels über CSS-Variablen, ein dunkles Design, die Effekte auf der Seite anpassen, Template und Texte überschreiben.
zielgruppe: betrieb
sprache: de
stand: 2026-11-14
---

# Anpassen

Das Panel kommt im neutralen Grunddesign. Angepasst wird alles in der
Website: CSS-Variablen, Regeln unter den Attributen von `<html>`, ein eigenes
Template und eigene Texte. In der Erweiterung muss nichts geändert werden.

## CSS-Variablen

Alle Standardwerte sind ohne Spezifität gesetzt (`:where(:root)`).
Überschreiben im CSS der Website auf `:root` oder auf `.nax`:

```css
:root {
    --nax-toggle-bg: #123456;
    --nax-accent: #123456;
    --nax-offset-y: 24px;
}
```

### Panel und Schaltfläche

| Variable | Standard | Wofür |
| --- | --- | --- |
| `--nax-font-family` | nicht gesetzt (Schrift der Seite) | Schrift des Panels |
| `--nax-font-size` | `1rem` | Grundschriftgröße des Panels |
| `--nax-bg` | `#ffffff` | Hintergrund des Panels und der Bedienelemente |
| `--nax-text` | `#1a1a1a` | Text und Symbole im Panel |
| `--nax-muted` | `#4d4d4d` | Beschreibungen, Gruppenüberschriften, Hinweise |
| `--nax-border` | `#767676` | Ränder von Panel und Bedienelementen, Knopf eines ausgeschalteten Schalters |
| `--nax-divider` | `#d9d9d9` | Linien zwischen Kopf, Gruppen und Fuß |
| `--nax-hover` | `#f0f0f0` | Hintergrund einer Zeile oder Schaltfläche unter der Maus |
| `--nax-accent` | `#1a1a1a` | Eingeschalteter Schalter, gedrückte Schnellauswahl |
| `--nax-on-accent` | `#ffffff` | Knopf eines eingeschalteten Schalters, Text einer gedrückten Schnellauswahl |
| `--nax-focus` | `#1a1a1a` | Fokusrahmen im Panel |
| `--nax-radius` | `12px` | Ecken des Panels |
| `--nax-control-radius` | `8px` | Ecken von Zeilen und Schaltflächen |
| `--nax-shadow` | `0 8px 28px rgba(0, 0, 0, 0.18)` | Schatten von Panel und Schaltfläche |
| `--nax-panel-width` | `23rem` | Breite neben der Schaltfläche (nie breiter als der Bildschirm) |
| `--nax-panel-gap` | `12px` | Abstand zwischen Schaltfläche und Panel |
| `--nax-drawer-width` | `24rem` | Breite der Leiste |
| `--nax-center-width` | `32rem` | Breite in der Bildschirmmitte |
| `--nax-overlay` | `rgba(0, 0, 0, 0.45)` | Abgedunkelte Fläche bei „Hintergrund abdunkeln“ |
| `--nax-toggle-size` | `56px` | Größe der runden Schaltfläche |
| `--nax-toggle-bg` | `#1a1a1a` | Hintergrund der Schaltfläche, Rand des Zählers |
| `--nax-toggle-hover` | `#404040` | Hintergrund der Schaltfläche unter der Maus |
| `--nax-toggle-color` | `#ffffff` | Symbol der Schaltfläche |
| `--nax-toggle-ring` | `#ffffff` | Ring um die Schaltfläche (hält sie auf dunklen Seiten sichtbar) |
| `--nax-badge-bg` | nicht gesetzt (`--nax-toggle-color`) | Hintergrund des Zählers auf der Schaltfläche |
| `--nax-badge-text` | nicht gesetzt (`--nax-toggle-bg`) | Zahl des Zählers |
| `--nax-offset-x` | `16px` | Abstand vom linken bzw. rechten Rand |
| `--nax-offset-y` | `16px` | Abstand vom unteren bzw. oberen Rand |
| `--nax-z-index` | `9000` | Stapelreihenfolge; die Farbfilter liegen eins darunter |

Das Standarddesign erreicht diese Kontraste (WCAG 2.1 AA): Text im Panel
17,4:1, Beschreibungen und Gruppenüberschriften 8,45:1 (7,42:1 auf der
Hover-Fläche), gedrückte Schnellauswahl 17,4:1, Ränder der Bedienelemente
4,54:1, Zähler 17,4:1, Fokusrahmen 17,4:1. Wer die Farben ändert, prüft die
neuen Paare erneut, auch auf der Hover-Fläche.

### Ein dunkles Design

Das Panel hat keinen eigenen Dunkelmodus; eine Website mit dunklem Design
setzt die Variablen, etwa so:

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

Gemessen mit diesen Werten: Text 14,89:1, Beschreibungen 9,96:1 (8,00:1 auf
der Hover-Fläche), Ränder 6,38:1. Den Block in
`@media (prefers-color-scheme: dark) { … }` setzen, wenn die Website der
Systemeinstellung des Besuchers folgt.

### Die Effekte auf der Seite

| Variable | Standard | Wofür |
| --- | --- | --- |
| `--nax-font-base` | `100%` | Grundschriftgröße der Website, siehe unten |
| `--nax-readable-font` | `Verdana, Tahoma, "Segoe UI", "DejaVu Sans", "Noto Sans", Arial, sans-serif` | Lesbare Schrift |
| `--nax-dyslexia-font` | `"OpenDyslexic", Verdana, …` | Legasthenie-Schrift |
| `--nax-lowcontrast-filter` | `brightness(0.92) contrast(0.8)` | Kontrast verringern |
| `--nax-lowsaturation-filter` | `saturate(0.4)` | Farben abschwächen |
| `--nax-focus-color` | `#000000` | Rahmen von „Fokus verstärken“ und Fokus der Schaltfläche |
| `--nax-focus-halo` | `#ffffff` | Heller Saum um diesen Rahmen |
| `--nax-mark-color` | `#c45500` | Umrandung von Schaltflächen (Links hervorheben), Rahmen der Überschriften, Text unter der Maus beim Vorlesen |
| `--nax-guide-color` | `#1a1a1a` | Leselinie |
| `--nax-guide-halo` | `#ffffff` | Rand der Leselinie |
| `--nax-guide-size` | `6px` | Stärke der Leselinie |
| `--nax-mask-color` | `rgba(0, 0, 0, 0.6)` | Abgedunkelter Teil der Lesemaske |
| `--nax-mask-band` | `7rem` | Höhe des hellen Streifens der Lesemaske |
| `--nax-reading-bg` | `#ffe066` | Hintergrund des Textes, der vorgelesen wird |
| `--nax-reading-text` | `#000000` | Text, der vorgelesen wird (16,1:1) |
| `--nax-reading-outline` | `#1a1a1a` | Rahmen des Textes, der vorgelesen wird |
| `--nax-contrast-text` | `#000000` | Text bei „Hoher Kontrast“ |
| `--nax-contrast-muted` | `#4d4d4d` | Platzhaltertext in Formularfeldern bei „Hoher Kontrast“ |
| `--nax-contrast-bg` | `#ffffff` | Hintergrund bei „Hoher Kontrast“ |
| `--nax-contrast-link` | `#0000cc` | Links bei „Hoher Kontrast“ |
| `--nax-contrast-border` | `#000000` | Rahmen und Ränder bei „Hoher Kontrast“ |

**Schriftgröße auf Websites mit eigener Grundschriftgröße.** Das Panel setzt
`font-size: calc(var(--nax-font-base) * Faktor)` auf `<html>`. Setzt die
Website die Grundschriftgröße selbst, etwa `html { font-size: 62.5%; }`,
denselben Wert als Basis eintragen:

```css
:root {
    --nax-font-base: 62.5%;
}
```

**Schriftgröße auf Websites mit Schriftgrößen in `px`.** Eine größere
Grundschrift erreicht nur Größen in `rem`, `em` oder `%`. Mit der
Moduloption **Ganze Seite vergrößern** zoomt die Schriftgröße stattdessen den
Inhalt: `<html>` bekommt `data-a11y-font-zoom` und `--nax-font-zoom` (`1.15`,
`1.3` oder `1.5`), und jedes Kind von `<body>` außer dem Panel
(`data-nax-host`, vom Skript gesetzt) bekommt `zoom: var(--nax-font-zoom)`.
Fest stehende Elemente im gezoomten Inhalt (der Knopf von NEO Cookie Consent,
ein Chat) wachsen mit; die Schaltflächen am Bildschirmrand von NEO Basic
messen sie so, wie sie gezeichnet sind. Media Queries ändern sich mit diesem
Zoom nicht, anders als beim Zoom des Browsers.

## Die Attribute auf `<html>`

Jede Einstellung setzt ein Attribut auf `<html>`; Websites können dagegen
gestalten. Die Namen ändern sich zwischen Fassungen nicht.

| Einstellung | Attribut |
| --- | --- |
| Schriftgröße | `data-a11y-font="115"` (`115`, `130` oder `150`) und `font-size` auf `<html>`; mit **Ganze Seite vergrößern** stattdessen `data-a11y-font-zoom` und `--nax-font-zoom` |
| Lesbare Schrift | `data-a11y-typeface` |
| Legasthenie-Schrift | `data-a11y-dyslexia` |
| Mehr Abstand | `data-a11y-readable` |
| Text linksbündig | `data-a11y-align` |
| Überschriften hervorheben | `data-a11y-headings` |
| Links hervorheben | `data-a11y-links` |
| Bilder ausblenden | `data-a11y-noimages` |
| Großer Mauszeiger | `data-a11y-cursor` |
| Hoher Kontrast | `data-a11y-contrast` |
| Kontrast verringern | `data-a11y-lowcontrast` |
| Farben abschwächen | `data-a11y-lowsaturation` |
| Graustufen | `data-a11y-grayscale` |
| Animationen anhalten | `data-a11y-motion` |
| Fokus verstärken | `data-a11y-focus` |
| Leselinie | `data-a11y-guide` |
| Lesemaske | `data-a11y-mask` |
| Vorlesen | `data-a11y-speak` |

Die Seitenstruktur ist eine Aktion und setzt kein Attribut.

### Einen Effekt anpassen

Alle Regeln der Effekte haben keine Spezifität (sie stehen in `:where()`) und
nutzen `!important`. Um einen Effekt für einen Teil der Website zu ändern,
eine Regel unter demselben Attribut mit `!important` schreiben:

```css
/* Hoher Kontrast: dunkler Kopfbereich mit weißer Schrift bleibt */
html[data-a11y-contrast] .site-header,
html[data-a11y-contrast] .site-header * {
    background-color: #000000 !important;
    color: #ffffff !important;
}

/* Mehr Abstand: Hauptnavigation unverändert lassen */
html[data-a11y-readable] .main-nav li {
    letter-spacing: normal !important;
    word-spacing: normal !important;
}
```

### Symbolschriften und die lesbaren Schriften

**Lesbare Schrift** und **Legasthenie-Schrift** ändern die Schrift allen
Textes. Elemente, die meist eine Symbolschrift tragen, behalten ihre: `<i>`,
Elemente mit einer Klasse, die „icon“ oder „symbol“ enthält, Font Awesome
(`fa…`), Bootstrap Icons (`bi`), Elemente mit `aria-hidden="true"` und Code.
Wird ein Symbol der Website trotzdem zu Buchstaben, das Element (oder einen
Container) mit `data-nax-keep-font` kennzeichnen:

```html
<span class="brand-glyph" data-nax-keep-font>logo</span>
```

### Wie die Farbfilter wirken

Kontrast verringern, Farben abschwächen und Graustufen setzen keinen Filter
auf `<html>`, `<body>` oder einen Wrapper — das würde auch das Panel filtern
und fest stehende Elemente mit der Seite scrollen lassen. Stattdessen filtert
eine Fläche über dem ganzen Fenster (`html::after` mit `backdrop-filter`), was
darunter liegt. Sie lässt die Maus durch und liegt knapp unter dem Panel
(`--nax-z-index` − 1). Elemente mit höherem `z-index` (manche Cookie-Banner)
werden nicht gefiltert. Eine Website, die `html::after` selbst nutzt, bekommt
diese Fläche stattdessen, solange eine der drei Einstellungen an ist.

## Template

Das Markup steht in `contao/templates/frontend_module/neo_accessibility.html.twig`.
Zum Ändern nach `templates/frontend_module/neo_accessibility.html.twig` der
Website kopieren, oder eine Variante wie
`templates/frontend_module/neo_accessibility/kompakt.html.twig` anlegen und im
Modul unter **Individuelles Template** wählen.

Das Skript findet die Teile des Panels über `data-nax-*`-Attribute. Nötig ist
nur der Dialog selbst (`data-nax`, `data-nax-panel`); jeder andere Teil ist
optional, das Skript nutzt, was es findet:

| Attribut | Teil |
| --- | --- |
| `data-nax` | Das Panel als Ganzes (beginnt `hidden`, das Skript zeigt es) |
| `data-nax-voices="device"` oder `"any"` | Vorlesen: nur Stimmen des Geräts oder auch Online-Stimmen des Browsers (Moduloption); fehlt es, gilt `device` |
| `data-nax-panel` | Der Dialog |
| `data-nax-toggle`, `data-nax-label-active` | Schwebende Schaltfläche; Name mit `{count}`, solange Einstellungen an sind |
| `data-nax-count` | Zähler auf der Schaltfläche |
| `data-neo-dock`, `data-neo-dock-corner`, `data-neo-dock-order`, `data-neo-dock-label` (Panel), `data-neo-dock-launcher` (schwebende Schaltfläche), `template[data-neo-dock-icon]` | Die Schaltflächen am Bildschirmrand von NEO Basic ([unten](#schaltflächen-am-bildschirmrand-neo-basic)); nur mit schwebender Schaltfläche |
| `data-nax-font-zoom` | Am Panel: die Moduloption **Ganze Seite vergrößern** |
| `data-nax-backdrop` | Abgedunkelte Fläche des modalen Dialogs |
| `data-nax-close`, `data-nax-reset`, `data-nax-cookies` | Schließen, Alles zurücksetzen, Cookie-Einstellungen |
| `data-nax-font="-1"`, `data-nax-font="1"`, `data-nax-fontval`, `data-nax-format` | Schriftgröße |
| `data-nax-flag="<name>"` | Ein Schalter (`role="switch"`, Namen wie in der Tabelle oben) |
| `data-nax-profile="<key>"`, `data-nax-set` | Eine Schnellauswahl und die Einstellungen, die sie schaltet (JSON) |
| `data-nax-structure`, `data-nax-structure-list`, `data-nax-texts` | Seitenstruktur: Schaltfläche, Liste, Texte (JSON) |
| `data-nax-speak-controls`, `data-nax-speak="page|pause|stop"` | Vorlesen: Bedienknöpfe |
| `data-nax-msg-again` (an `data-nax-speak-controls`) | Vorlesen: der Hinweis, nachdem ein Bedienelement auf einen Klick vorgelesen wurde. Ohne ihn wirken Bedienelemente sofort und werden beim Klick nicht vorgelesen |
| `data-nax-speech-reason`, `data-nax-msg-device`, `data-nax-msg-browser`, `data-nax-msg-unsupported`, `data-nax-format`, `data-nax-label` | Wohin das Skript schreibt, warum Vorlesen nicht geht (neben dem Schalter und unter der Schnellauswahl), mit den Texten der drei Fälle (`{language}`) und für eine Schnellauswahl dem Format `{label}: {reason}` |
| `data-nax-status`, `data-nax-msg-*` | Ansagen für Vorlesegeräte und ihre Texte |

Variablen des Templates: `uid`, `position`, `layout`, `modal`, `font_zoom`, `font`,
`switches`, `groups` (Liste aus `{key, features}`), `structure`, `profiles`
(Liste aus `{key, settings}`), `online_voices`, `statement_url`,
`statement_current`, `locale`.

Das Panel trägt eine Klasse für die Position (`.nax--bottom-left`,
`.nax--bottom-right`, `.nax--top-left`, `.nax--top-right`, `.nax--no-button`),
eine für die Variante (`.nax--popover`, `.nax--drawer-left`,
`.nax--drawer-right`, `.nax--center`) und `.nax--modal` mit abgedunkeltem
Hintergrund. Mit anderen Schaltflächen in seiner Ecke ergänzen die
Schaltflächen am Bildschirmrand `data-neo-dock-place` (`menu` oder `stack`)
und verschieben es mit `--neo-dock-x` und `--neo-dock-y`.

## Schaltflächen am Bildschirmrand (NEO Basic)

Die schwebende Schaltfläche gehört zu den Schaltflächen am Bildschirmrand von
NEO Basic, die sie mit den Schaltflächen anderer Erweiterungen in derselben
Ecke anordnen (dem Knopf zum Wiederöffnen von NEO Cookie Consent, dem Chat von
NEO SuperAgent), wie im Seitenlayout eingestellt. Das Panel trägt
`data-neo-dock="accessibility"`, `data-neo-dock-corner` (die Position der
Schaltfläche), `data-neo-dock-order="20"` und `data-neo-dock-label` (den Titel
des Panels); die schwebende Schaltfläche `data-neo-dock-launcher`; das
Template `template[data-neo-dock-icon]` enthält das Symbol des Menüeintrags.
Das Skript schreibt die Zahl der eingeschalteten Einstellungen für diesen
Eintrag in `data-neo-dock-badge` und `data-neo-dock-badge-label`. Im Menü ist
die schwebende Schaltfläche ausgeblendet und das Panel auf die
Einstellungs-Schaltfläche gerückt (es öffnet sich darüber); im Stapel rückt
es neben die anderen Schaltflächen. Beides über `--neo-dock-x` und
`--neo-dock-y`, die die Position des Panels und die Höhe des Popovers
addieren. Der Eintrag **Cookie-Einstellungen** im Panel (`data-nax-cookies`)
erscheint nur, solange es `window.cookieConsent.open()` gibt und die Seite
keinen Knopf zum Wiederöffnen hat (`[data-ncc-reopen]`). Ohne die Attribute
(ein Template von früher) behält die Schaltfläche ihren eigenen Platz.

## Texte

Die Texte für Besucher kommen aus `translations/neo_accessibility.<sprache>.yaml`,
gewählt nach der Sprache der Seite. Einzelne Texte ändern: eine Datei gleichen
Namens mit nur diesen Schlüsseln in den Ordner `translations/` der Website
legen, etwa `translations/neo_accessibility.de.yaml`:

```yaml
button:
    label: "Darstellung anpassen"
    label_active: "Darstellung anpassen, {count} eingeschaltet"
profile:
    vision:
        label: "Sehen"
```

Die Wörter in geschweiften Klammern (`{value}`, `{count}`, `{label}`, `{alt}`,
`{level}`, `{language}`, `{reason}`) stehen lassen: Das Panel ersetzt sie. Danach den Cache leeren.

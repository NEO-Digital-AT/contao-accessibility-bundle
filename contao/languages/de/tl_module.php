<?php

declare(strict_types=1);

// Front-end module "neo_accessibility" (accessibility panel).

// Legends
$GLOBALS['TL_LANG']['tl_module']['nax_features_legend'] = 'Einstellungen für Besucher';
$GLOBALS['TL_LANG']['tl_module']['nax_layout_legend'] = 'Schaltfläche und Panel';

// Fields
$GLOBALS['TL_LANG']['tl_module']['nax_features'] = ['Angebotene Einstellungen', 'Besucher sehen im Panel nur die angehakten Einstellungen. „Vorlesen“ nutzt nur Stimmen, die auf dem Gerät des Besuchers installiert sind, außer Online-Stimmen sind unten erlaubt. „Schriftgröße“ setzt Schriftgrößen in rem, em oder % in der Gestaltung der Website voraus; mit Größen in px wächst nur das Panel (siehe „Ganze Seite vergrößern“).'];
$GLOBALS['TL_LANG']['tl_module']['nax_features_options'] = [
    // Groups (headings of the panel)
    'text' => 'Text und Inhalt',
    'colour' => 'Farben',
    'orientation' => 'Orientierung',
    // Settings
    'font' => 'Schriftgröße',
    'typeface' => 'Lesbare Schrift',
    'dyslexia' => 'Legasthenie-Schrift',
    'readable' => 'Mehr Abstand',
    'align' => 'Text linksbündig',
    'headings' => 'Überschriften hervorheben',
    'links' => 'Links hervorheben',
    'noimages' => 'Bilder ausblenden',
    'cursor' => 'Großer Mauszeiger',
    'contrast' => 'Hoher Kontrast',
    'lowcontrast' => 'Kontrast verringern',
    'lowsaturation' => 'Farben abschwächen',
    'grayscale' => 'Graustufen',
    'motion' => 'Animationen anhalten',
    'focus' => 'Fokus verstärken',
    'guide' => 'Leselinie',
    'mask' => 'Lesemaske',
    'structure' => 'Seitenstruktur',
    'speak' => 'Vorlesen',
];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles'] = ['Schnellauswahl', 'Schaltflächen oben im Panel, die mehrere Einstellungen auf einmal einschalten. Jede schaltet nur oben angehakte Einstellungen; eine ohne solche erscheint nicht.'];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_options'] = [
    'vision' => 'Sehschwäche',
    'dyslexia' => 'Legasthenie',
    'concentration' => 'Konzentration',
    'epilepsy' => 'Epilepsie',
    'motor' => 'Motorik',
    'reader' => 'Vorlesen',
];
// Quick options: label of an option (name: what it switches) and the note on those that cannot work
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_option'] = '%1$s: %2$s';
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_unavailable'] = 'Nicht im Panel: %s. Keine der Einstellungen, die sie schalten, ist unter „Angebotene Einstellungen“ angehakt; eine davon anhaken, damit die Schnellauswahl erscheint.';
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom'] = ['Ganze Seite vergrößern', 'Für Websites, deren Gestaltung Schriftgrößen in Pixeln (px) angibt: „Schriftgröße“ vergrößert dann die ganze Seite wie der Zoom des Browsers statt nur der Grundschrift; das Panel behält seine Größe. Aus: „Schriftgröße“ vergrößert die Grundschrift, was wirkt, wo die Gestaltung rem, em oder % verwendet.'];
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom_unavailable'] = '„Ganze Seite vergrößern“ wirkt nur, wenn „Schriftgröße“ unter „Angebotene Einstellungen“ angehakt ist.';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices'] = ['Online-Stimmen des Browsers erlauben', 'Vorlesen nutzt dann auch Online-Stimmen des Browsers (etwa in Chrome oder Microsoft Edge), wo das Gerät des Besuchers keine Stimme für die Sprache hat. Der Browser schickt den vorgelesenen Text dafür an seinen Hersteller; das muss in der Datenschutzerklärung stehen. Aus: nur Stimmen, die auf dem Gerät installiert sind, der Text bleibt dort.'];
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_unavailable'] = '„Online-Stimmen des Browsers erlauben“ wirkt nur, wenn „Vorlesen“ unter „Angebotene Einstellungen“ angehakt ist.';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_privacy'] = 'Online-Stimmen sind erlaubt: Hat das Gerät eines Besuchers keine Stimme für die Sprache, schickt der Browser den vorgelesenen Text an seinen Hersteller. Das in der Datenschutzerklärung nennen (siehe Dokumentation).';
$GLOBALS['TL_LANG']['tl_module']['nax_position'] = ['Position der Schaltfläche', 'Wo die schwebende Schaltfläche erscheint. Ohne schwebende Schaltfläche öffnet der Insert-Tag {{accessibility::link}} das Panel. Sitzen Schaltflächen anderer Erweiterungen (Cookie-Einstellungen, Chat) in derselben Ecke, werden sie gemeinsam so angeordnet, wie es im Seitenlayout eingestellt ist: eine Einstellungs-Schaltfläche mit Menü, übereinander oder nebeneinander.'];
$GLOBALS['TL_LANG']['tl_module']['nax_position_options'] = [
    'bottom-left' => 'Unten links',
    'bottom-right' => 'Unten rechts',
    'top-left' => 'Oben links',
    'top-right' => 'Oben rechts',
    'none' => 'Keine schwebende Schaltfläche',
];
$GLOBALS['TL_LANG']['tl_module']['nax_layout'] = ['Wo sich das Panel öffnet', 'Neben der Schaltfläche, als Leiste am linken oder rechten Bildschirmrand oder in der Mitte des Bildschirms.'];
$GLOBALS['TL_LANG']['tl_module']['nax_layout_options'] = [
    'popover' => 'Neben der Schaltfläche',
    'drawer-left' => 'Leiste links',
    'drawer-right' => 'Leiste rechts',
    'center' => 'Bildschirmmitte',
];
$GLOBALS['TL_LANG']['tl_module']['nax_overlay'] = ['Hintergrund abdunkeln', 'Legt eine dunkle Fläche hinter das offene Panel. Der Rest der Seite ist erst wieder bedienbar, wenn das Panel geschlossen ist; ein Klick auf die dunkle Fläche schließt es.'];
$GLOBALS['TL_LANG']['tl_module']['nax_statement'] = ['Erklärung zur Barrierefreiheit', 'Seite mit der Erklärung zur Barrierefreiheit dieser Website. Das Panel verlinkt darauf. Leer lassen, wenn es keine gibt.'];

// Licence note at the top of the form (the only place in the back end that names the maker)
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example_label'] = 'Text zum Kopieren (mit Link):';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example'] = 'Barrierefreiheits-Panel: NEO Accessibility von NEO Digital';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_text'] = 'Diese Erweiterung ist kostenlos. Bedingung: Im Impressum jeder Website, auf der sie öffentlich läuft, wird sie mit einem Link auf https://neo-digital.at genannt.';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_html_label'] = 'Oder als HTML für den Quelltext des Impressums:';
$GLOBALS['TL_LANG']['tl_module']['licence_legend'] = 'Lizenz';

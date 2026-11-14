<?php

declare(strict_types=1);

// Front-end module "neo_accessibility" (accessibility panel).

// Legends
$GLOBALS['TL_LANG']['tl_module']['nax_features_legend'] = 'Nastavení pro návštěvníky';
$GLOBALS['TL_LANG']['tl_module']['nax_layout_legend'] = 'Tlačítko a panel';

// Fields
$GLOBALS['TL_LANG']['tl_module']['nax_features'] = ['Nabízená nastavení', 'Návštěvníci uvidí v panelu jen zaškrtnutá nastavení. „Předčítání“ používá jen hlasy nainstalované v zařízení návštěvníka, pokud níže nejsou povoleny online hlasy. „Velikost písma“ předpokládá velikosti písma v rem, em nebo % v designu webu; s velikostmi v px se zvětší jen panel (viz „Zvětšit celou stránku“).'];
$GLOBALS['TL_LANG']['tl_module']['nax_features_options'] = [
    // Groups (headings of the panel)
    'text' => 'Text a obsah',
    'colour' => 'Barvy',
    'orientation' => 'Orientace',
    // Settings
    'font' => 'Velikost písma',
    'typeface' => 'Čitelné písmo',
    'dyslexia' => 'Písmo pro dyslektiky',
    'readable' => 'Větší mezery',
    'align' => 'Text doleva',
    'headings' => 'Zvýraznit nadpisy',
    'links' => 'Zvýraznit odkazy',
    'noimages' => 'Skrýt obrázky',
    'cursor' => 'Velký kurzor',
    'contrast' => 'Vysoký kontrast',
    'lowcontrast' => 'Snížit kontrast',
    'lowsaturation' => 'Ztlumit barvy',
    'grayscale' => 'Odstíny šedi',
    'motion' => 'Zastavit animace',
    'focus' => 'Výrazný fokus',
    'guide' => 'Čtecí linka',
    'mask' => 'Čtecí maska',
    'structure' => 'Struktura stránky',
    'speak' => 'Předčítání',
];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles'] = ['Rychlá volba', 'Tlačítka v horní části panelu, která zapnou několik nastavení najednou. Každé zapíná jen nastavení zaškrtnutá výše; tlačítko bez žádného z nich se nezobrazí.'];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_options'] = [
    'vision' => 'Slabozrakost',
    'dyslexia' => 'Dyslexie',
    'concentration' => 'Soustředění',
    'epilepsy' => 'Epilepsie',
    'motor' => 'Motorika',
    'reader' => 'Předčítání',
];
// Quick options: label of an option (name: what it switches) and the note on those that cannot work
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_option'] = '%1$s: %2$s';
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_unavailable'] = 'Není v panelu: %s. Žádné z nastavení, která zapínají, není zaškrtnuto v „Nabízená nastavení“; zaškrtněte jedno z nich, aby se rychlá volba zobrazila.';
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom'] = ['Zvětšit celou stránku', 'Pro weby, jejichž design udává velikosti písma v pixelech (px): „Velikost písma“ pak zvětší celou stránku jako zoom prohlížeče, ne jen základní písmo; panel si ponechá svou velikost. Vypnuto: „Velikost písma“ zvětší základní písmo, což funguje tam, kde design používá rem, em nebo %.'];
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom_unavailable'] = '„Zvětšit celou stránku“ má účinek, jen když je v „Nabízená nastavení“ zaškrtnuto „Velikost písma“.';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices'] = ['Povolit online hlasy prohlížeče', 'Předčítání pak používá i online hlasy prohlížeče (například v Chrome nebo Microsoft Edge), pokud zařízení návštěvníka nemá hlas pro daný jazyk. Prohlížeč k tomu posílá předčítaný text svému výrobci; musí to být uvedeno v zásadách ochrany osobních údajů. Vypnuto: jen hlasy nainstalované v zařízení, text zůstává tam.'];
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_unavailable'] = '„Povolit online hlasy prohlížeče“ má účinek, jen když je v „Nabízená nastavení“ zaškrtnuto „Předčítání“.';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_privacy'] = 'Online hlasy jsou povoleny: pokud zařízení návštěvníka nemá hlas pro daný jazyk, prohlížeč posílá předčítaný text svému výrobci. Uveďte to v zásadách ochrany osobních údajů (viz dokumentace).';
$GLOBALS['TL_LANG']['tl_module']['nax_position'] = ['Umístění tlačítka', 'Kde se plovoucí tlačítko zobrazí. Bez plovoucího tlačítka otevře panel vkládací značka {{accessibility::link}}. Pokud jsou ve stejném rohu tlačítka jiných rozšíření (nastavení cookies, chat), uspořádají se společně podle nastavení v rozvržení stránky: jedno tlačítko nastavení s nabídkou, nad sebou nebo vedle sebe.'];
$GLOBALS['TL_LANG']['tl_module']['nax_position_options'] = [
    'bottom-left' => 'Vlevo dole',
    'bottom-right' => 'Vpravo dole',
    'top-left' => 'Vlevo nahoře',
    'top-right' => 'Vpravo nahoře',
    'none' => 'Bez plovoucího tlačítka',
];
$GLOBALS['TL_LANG']['tl_module']['nax_layout'] = ['Kde se panel otevře', 'Vedle tlačítka, jako vysouvací panel u levého nebo pravého okraje obrazovky, nebo uprostřed obrazovky.'];
$GLOBALS['TL_LANG']['tl_module']['nax_layout_options'] = [
    'popover' => 'Vedle tlačítka',
    'drawer-left' => 'Vysouvací panel vlevo',
    'drawer-right' => 'Vysouvací panel vpravo',
    'center' => 'Střed obrazovky',
];
$GLOBALS['TL_LANG']['tl_module']['nax_overlay'] = ['Ztmavit pozadí', 'Za otevřeným panelem zobrazí tmavou vrstvu. Zbytek stránky lze použít až po zavření panelu; kliknutí na tmavou vrstvu panel zavře.'];
$GLOBALS['TL_LANG']['tl_module']['nax_statement'] = ['Prohlášení o přístupnosti', 'Stránka s prohlášením o přístupnosti tohoto webu. Panel na ni odkazuje. Pokud žádné není, nechte pole prázdné.'];

// Licence note at the top of the form (the only place in the back end that names the maker)
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example_label'] = 'Text ke zkopírování (s odkazem):';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example'] = 'Panel přístupnosti: NEO Accessibility od NEO Digital';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_text'] = 'Toto rozšíření je zdarma. Podmínka: právní upozornění každého webu, na kterém veřejně běží, jej uvádí s odkazem na https://neo-digital.at.';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_html_label'] = 'Nebo jako HTML pro zdrojový kód právního upozornění:';
$GLOBALS['TL_LANG']['tl_module']['licence_legend'] = 'Licence';

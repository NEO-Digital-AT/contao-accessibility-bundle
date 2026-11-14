<?php

declare(strict_types=1);

// Front-end module "neo_accessibility" (accessibility panel).

// Legends
$GLOBALS['TL_LANG']['tl_module']['nax_features_legend'] = 'Beállítások a látogatóknak';
$GLOBALS['TL_LANG']['tl_module']['nax_layout_legend'] = 'Gomb és panel';

// Fields
$GLOBALS['TL_LANG']['tl_module']['nax_features'] = ['Felkínált beállítások', 'A látogatók a panelen csak a bejelölt beállításokat látják. A „Felolvasás” csak a látogató eszközére telepített hangokat használja, hacsak lent nincsenek engedélyezve az online hangok. A „Betűméret” rem, em vagy % egységű betűméreteket feltételez a weboldal kinézetében; px-ben megadott méreteknél csak a panel nő (lásd „Az egész oldal nagyítása”).'];
$GLOBALS['TL_LANG']['tl_module']['nax_features_options'] = [
    // Groups (headings of the panel)
    'text' => 'Szöveg és tartalom',
    'colour' => 'Színek',
    'orientation' => 'Tájékozódás',
    // Settings
    'font' => 'Betűméret',
    'typeface' => 'Olvasható betűtípus',
    'dyslexia' => 'Diszlexiás betűtípus',
    'readable' => 'Nagyobb térköz',
    'align' => 'Balra igazított szöveg',
    'headings' => 'Címsorok kiemelése',
    'links' => 'Linkek kiemelése',
    'noimages' => 'Képek elrejtése',
    'cursor' => 'Nagy egérmutató',
    'contrast' => 'Nagy kontraszt',
    'lowcontrast' => 'Kisebb kontraszt',
    'lowsaturation' => 'Halványabb színek',
    'grayscale' => 'Szürkeárnyalat',
    'motion' => 'Animációk leállítása',
    'focus' => 'Erős fókusz',
    'guide' => 'Olvasóvonal',
    'mask' => 'Olvasómaszk',
    'structure' => 'Oldalszerkezet',
    'speak' => 'Felolvasás',
];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles'] = ['Gyorsválasztás', 'Gombok a panel tetején, amelyek egyszerre több beállítást kapcsolnak be. Mindegyik csak a fent bejelölt beállításokat kapcsolja; az a gomb, amelyhez egy sincs, nem jelenik meg.'];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_options'] = [
    'vision' => 'Gyengénlátás',
    'dyslexia' => 'Diszlexia',
    'concentration' => 'Koncentráció',
    'epilepsy' => 'Epilepszia',
    'motor' => 'Motorika',
    'reader' => 'Felolvasás',
];
// Quick options: label of an option (name: what it switches) and the note on those that cannot work
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_option'] = '%1$s: %2$s';
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_unavailable'] = 'Nem jelenik meg a panelen: %s. Az általuk bekapcsolt beállítások közül egyik sincs bejelölve a „Felkínált beállítások” alatt; jelöljön be egyet, hogy a gyorsválasztás megjelenjen.';
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom'] = ['Az egész oldal nagyítása', 'Olyan weboldalakhoz, amelyek kinézete pixelben (px) adja meg a betűméreteket: a „Betűméret” ekkor az egész oldalt nagyítja, mint a böngésző nagyítása, nem csak az alapbetűméretet; a panel megtartja a méretét. Kikapcsolva: a „Betűméret” az alapbetűméretet növeli, ami ott működik, ahol a kinézet rem, em vagy % egységet használ.'];
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom_unavailable'] = 'Az egész oldal nagyítása csak akkor hat, ha a „Felkínált beállítások” alatt be van jelölve a „Betűméret”.';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices'] = ['A böngésző online hangjainak engedélyezése', 'A felolvasás ekkor a böngésző online hangjait is használja (például Chrome-ban vagy Microsoft Edge-ben), ha a látogató eszközén nincs hang az adott nyelvhez. Ehhez a böngésző elküldi a felolvasott szöveget a gyártójának; ezt az adatvédelmi tájékoztatóban fel kell tüntetni. Kikapcsolva: csak az eszközre telepített hangok, a szöveg ott marad.'];
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_unavailable'] = 'Az online hangok engedélyezése csak akkor hat, ha a „Felkínált beállítások” alatt be van jelölve a „Felolvasás”.';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_privacy'] = 'Az online hangok engedélyezve vannak: ha a látogató eszközén nincs hang az adott nyelvhez, a böngésző elküldi a felolvasott szöveget a gyártójának. Ezt tüntesse fel az adatvédelmi tájékoztatóban (lásd a dokumentációt).';
$GLOBALS['TL_LANG']['tl_module']['nax_position'] = ['A gomb helye', 'Hol jelenik meg a lebegő gomb. Lebegő gomb nélkül a {{accessibility::link}} beszúró címke nyitja meg a panelt. Ha ugyanabban a sarokban más bővítmények gombjai is vannak (sütibeállítások, chat), az oldalelrendezés beállítása szerint együtt jelennek meg: egy beállítások gomb menüvel, egymás fölött vagy egymás mellett.'];
$GLOBALS['TL_LANG']['tl_module']['nax_position_options'] = [
    'bottom-left' => 'Bal alsó sarok',
    'bottom-right' => 'Jobb alsó sarok',
    'top-left' => 'Bal felső sarok',
    'top-right' => 'Jobb felső sarok',
    'none' => 'Nincs lebegő gomb',
];
$GLOBALS['TL_LANG']['tl_module']['nax_layout'] = ['Hol nyílik meg a panel', 'A gomb mellett, oldalsávként a képernyő bal vagy jobb szélén, vagy a képernyő közepén.'];
$GLOBALS['TL_LANG']['tl_module']['nax_layout_options'] = [
    'popover' => 'A gomb mellett',
    'drawer-left' => 'Oldalsáv balra',
    'drawer-right' => 'Oldalsáv jobbra',
    'center' => 'A képernyő közepén',
];
$GLOBALS['TL_LANG']['tl_module']['nax_overlay'] = ['Háttér elsötétítése', 'Sötét réteget jelenít meg a nyitott panel mögött. Az oldal többi része csak a panel bezárása után használható; a sötét rétegre kattintva a panel bezárul.'];
$GLOBALS['TL_LANG']['tl_module']['nax_statement'] = ['Akadálymentességi nyilatkozat', 'A weboldal akadálymentességi nyilatkozatát tartalmazó oldal. A panel erre hivatkozik. Hagyja üresen, ha nincs ilyen.'];

// Licence note at the top of the form (the only place in the back end that names the maker)
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example_label'] = 'Másolható szöveg (hivatkozással):';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example'] = 'Akadálymentesítési panel: NEO Accessibility, készítette: NEO Digital';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_text'] = 'Ez a bővítmény ingyenes. Feltétel: minden olyan weboldal impresszuma, amelyen nyilvánosan fut, megnevezi egy https://neo-digital.at címre mutató hivatkozással.';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_html_label'] = 'Vagy HTML-ként az impresszum forráskódjához:';
$GLOBALS['TL_LANG']['tl_module']['licence_legend'] = 'Licenc';

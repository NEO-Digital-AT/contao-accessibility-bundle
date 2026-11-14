<?php

declare(strict_types=1);

// Front-end module "neo_accessibility" (accessibility panel).

// Legends
$GLOBALS['TL_LANG']['tl_module']['nax_features_legend'] = 'Nastavitve za obiskovalce';
$GLOBALS['TL_LANG']['tl_module']['nax_layout_legend'] = 'Gumb in plošča';

// Fields
$GLOBALS['TL_LANG']['tl_module']['nax_features'] = ['Ponujene nastavitve', 'Obiskovalci v plošči vidijo samo označene nastavitve. »Branje na glas« uporablja samo glasove, nameščene v napravi obiskovalca, razen če so spodaj dovoljeni spletni glasovi. »Velikost besedila« predpostavlja velikosti pisave v rem, em ali % v oblikovanju spletnega mesta; pri velikostih v px se poveča samo plošča (glejte »Povečaj celo stran«).'];
$GLOBALS['TL_LANG']['tl_module']['nax_features_options'] = [
    // Groups (headings of the panel)
    'text' => 'Besedilo in vsebina',
    'colour' => 'Barve',
    'orientation' => 'Orientacija',
    // Settings
    'font' => 'Velikost besedila',
    'typeface' => 'Berljiva pisava',
    'dyslexia' => 'Pisava za disleksijo',
    'readable' => 'Več razmika',
    'align' => 'Besedilo levo',
    'headings' => 'Poudari naslove',
    'links' => 'Poudari povezave',
    'noimages' => 'Skrij slike',
    'cursor' => 'Velik kazalec',
    'contrast' => 'Visok kontrast',
    'lowcontrast' => 'Manjši kontrast',
    'lowsaturation' => 'Bledejše barve',
    'grayscale' => 'Sivine',
    'motion' => 'Ustavi animacije',
    'focus' => 'Izrazit fokus',
    'guide' => 'Bralna črta',
    'mask' => 'Bralna maska',
    'structure' => 'Struktura strani',
    'speak' => 'Branje na glas',
];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles'] = ['Hitra izbira', 'Gumbi na vrhu plošče, ki vklopijo več nastavitev hkrati. Vsak vklopi samo zgoraj označene nastavitve; gumb brez njih se ne prikaže.'];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_options'] = [
    'vision' => 'Slabovidnost',
    'dyslexia' => 'Disleksija',
    'concentration' => 'Zbranost',
    'epilepsy' => 'Epilepsija',
    'motor' => 'Motorika',
    'reader' => 'Branje na glas',
];
// Quick options: label of an option (name: what it switches) and the note on those that cannot work
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_option'] = '%1$s: %2$s';
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_unavailable'] = 'Ni v plošči: %s. Nobena od nastavitev, ki jih vklopijo, ni označena pod »Ponujene nastavitve«; označite eno, da se hitra izbira prikaže.';
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom'] = ['Povečaj celo stran', 'Za spletna mesta, katerih oblikovanje podaja velikosti pisave v slikovnih pikah (px): »Velikost besedila« potem poveča celo stran kot povečava brskalnika, ne le osnovne pisave; plošča obdrži svojo velikost. Izklopljeno: »Velikost besedila« poveča osnovno pisavo, kar deluje tam, kjer oblikovanje uporablja rem, em ali %.'];
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom_unavailable'] = '»Povečaj celo stran« deluje samo, če je pod »Ponujene nastavitve« označeno »Velikost besedila«.';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices'] = ['Dovoli spletne glasove brskalnika', 'Branje na glas tedaj uporablja tudi spletne glasove brskalnika (na primer v Chromu ali Microsoft Edgeu), kadar naprava obiskovalca nima glasu za jezik. Brskalnik pri tem pošlje prebrano besedilo svojemu proizvajalcu; to mora biti navedeno v izjavi o varstvu podatkov. Izklopljeno: samo glasovi, nameščeni v napravi, besedilo ostane tam.'];
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_unavailable'] = '»Dovoli spletne glasove brskalnika« deluje samo, če je pod »Ponujene nastavitve« označeno »Branje na glas«.';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_privacy'] = 'Spletni glasovi so dovoljeni: če naprava obiskovalca nima glasu za jezik, brskalnik pošlje prebrano besedilo svojemu proizvajalcu. To navedite v izjavi o varstvu podatkov (glejte dokumentacijo).';
$GLOBALS['TL_LANG']['tl_module']['nax_position'] = ['Položaj gumba', 'Kje se prikaže plavajoči gumb. Brez plavajočega gumba ploščo odpre vstavljena oznaka {{accessibility::link}}. Če so v istem kotu gumbi drugih razširitev (nastavitve piškotkov, klepet), so razporejeni skupaj, kot je nastavljeno v postavitvi strani: en gumb za nastavitve z menijem, drug nad drugim ali drug ob drugem.'];
$GLOBALS['TL_LANG']['tl_module']['nax_position_options'] = [
    'bottom-left' => 'Spodaj levo',
    'bottom-right' => 'Spodaj desno',
    'top-left' => 'Zgoraj levo',
    'top-right' => 'Zgoraj desno',
    'none' => 'Brez plavajočega gumba',
];
$GLOBALS['TL_LANG']['tl_module']['nax_layout'] = ['Kje se plošča odpre', 'Ob gumbu, kot predal ob levem ali desnem robu zaslona ali na sredini zaslona.'];
$GLOBALS['TL_LANG']['tl_module']['nax_layout_options'] = [
    'popover' => 'Ob gumbu',
    'drawer-left' => 'Predal levo',
    'drawer-right' => 'Predal desno',
    'center' => 'Sredina zaslona',
];
$GLOBALS['TL_LANG']['tl_module']['nax_overlay'] = ['Zatemni ozadje', 'Za odprto ploščo prikaže temno plast. Preostanek strani je spet uporaben šele, ko se plošča zapre; klik na temno plast jo zapre.'];
$GLOBALS['TL_LANG']['tl_module']['nax_statement'] = ['Izjava o dostopnosti', 'Stran z izjavo o dostopnosti tega spletnega mesta. Plošča se poveže nanjo. Pustite prazno, če je ni.'];

// Licence note at the top of the form (the only place in the back end that names the maker)
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example_label'] = 'Besedilo za kopiranje (s povezavo):';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example'] = 'Plošča za dostopnost: NEO Accessibility podjetja NEO Digital';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_text'] = 'Ta razširitev je brezplačna. Pogoj: pravno obvestilo vsakega spletnega mesta, na katerem javno deluje, jo navaja s povezavo na https://neo-digital.at.';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_html_label'] = 'Ali kot HTML za izvorno kodo pravnega obvestila:';
$GLOBALS['TL_LANG']['tl_module']['licence_legend'] = 'Licenca';

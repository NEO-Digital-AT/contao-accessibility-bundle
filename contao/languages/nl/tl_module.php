<?php

declare(strict_types=1);

// Front-end module "neo_accessibility" (accessibility panel).

// Legends
$GLOBALS['TL_LANG']['tl_module']['nax_features_legend'] = 'Instellingen voor bezoekers';
$GLOBALS['TL_LANG']['tl_module']['nax_layout_legend'] = 'Knop en paneel';

// Fields
$GLOBALS['TL_LANG']['tl_module']['nax_features'] = ['Aangeboden instellingen', 'Bezoekers zien in het paneel alleen de aangevinkte instellingen. "Voorlezen" gebruikt alleen stemmen die op het apparaat van de bezoeker zijn geïnstalleerd, tenzij onlinestemmen hieronder zijn toegestaan. "Tekstgrootte" vereist lettergroottes in rem, em of % in het ontwerp van de website; met groottes in px wordt alleen het paneel groter (zie "Hele pagina vergroten").'];
$GLOBALS['TL_LANG']['tl_module']['nax_features_options'] = [
    // Groups (headings of the panel)
    'text' => 'Tekst en inhoud',
    'colour' => 'Kleuren',
    'orientation' => 'Oriëntatie',
    // Settings
    'font' => 'Tekstgrootte',
    'typeface' => 'Leesbaar lettertype',
    'dyslexia' => 'Dyslexielettertype',
    'readable' => 'Meer ruimte',
    'align' => 'Tekst links uitlijnen',
    'headings' => 'Koppen markeren',
    'links' => 'Links markeren',
    'noimages' => 'Afbeeldingen verbergen',
    'cursor' => 'Grote cursor',
    'contrast' => 'Hoog contrast',
    'lowcontrast' => 'Contrast verlagen',
    'lowsaturation' => 'Kleuren dempen',
    'grayscale' => 'Grijstinten',
    'motion' => 'Animaties stoppen',
    'focus' => 'Sterke focus',
    'guide' => 'Leeslijn',
    'mask' => 'Leesmasker',
    'structure' => 'Paginastructuur',
    'speak' => 'Voorlezen',
];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles'] = ['Snelkeuze', 'Knoppen boven in het paneel die meerdere instellingen tegelijk aanzetten. Elke knop zet alleen hierboven aangevinkte instellingen aan; een knop zonder zulke instellingen verschijnt niet.'];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_options'] = [
    'vision' => 'Slechtziend',
    'dyslexia' => 'Dyslexie',
    'concentration' => 'Concentratie',
    'epilepsy' => 'Epilepsie',
    'motor' => 'Motoriek',
    'reader' => 'Voorlezen',
];
// Quick options: label of an option (name: what it switches) and the note on those that cannot work
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_option'] = '%1$s: %2$s';
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_unavailable'] = 'Niet in het paneel: %s. Geen van de instellingen die ze aanzetten is aangevinkt bij "Aangeboden instellingen"; vink er een aan om de snelkeuze te tonen.';
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom'] = ['Hele pagina vergroten', 'Voor websites waarvan het ontwerp lettergroottes in pixels (px) opgeeft: "Tekstgrootte" vergroot dan de hele pagina, zoals de zoom van de browser, in plaats van alleen de basisgrootte; het paneel behoudt zijn grootte. Uit: "Tekstgrootte" vergroot de basisgrootte, wat werkt waar het ontwerp rem, em of % gebruikt.'];
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom_unavailable'] = '"Hele pagina vergroten" werkt alleen als "Tekstgrootte" is aangevinkt bij "Aangeboden instellingen".';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices'] = ['Onlinestemmen van de browser toestaan', 'Voorlezen gebruikt dan ook onlinestemmen van de browser (bijvoorbeeld in Chrome of Microsoft Edge) als het apparaat van de bezoeker geen stem voor de taal heeft. De browser stuurt de voorgelezen tekst daarvoor naar zijn maker; dat moet in de privacyverklaring staan. Uit: alleen stemmen die op het apparaat zijn geïnstalleerd, de tekst blijft daar.'];
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_unavailable'] = '"Onlinestemmen van de browser toestaan" werkt alleen als "Voorlezen" is aangevinkt bij "Aangeboden instellingen".';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_privacy'] = 'Onlinestemmen zijn toegestaan: heeft het apparaat van een bezoeker geen stem voor de taal, dan stuurt de browser de voorgelezen tekst naar zijn maker. Vermeld dit in de privacyverklaring (zie de documentatie).';
$GLOBALS['TL_LANG']['tl_module']['nax_position'] = ['Positie van de knop', 'Waar de zwevende knop verschijnt. Zonder zwevende knop opent de insert-tag {{accessibility::link}} het paneel. Staan er knoppen van andere extensies (cookie-instellingen, chat) in dezelfde hoek, dan worden ze samen geordend zoals ingesteld in de pagina-layout: één instellingenknop met een menu, boven elkaar of naast elkaar.'];
$GLOBALS['TL_LANG']['tl_module']['nax_position_options'] = [
    'bottom-left' => 'Linksonder',
    'bottom-right' => 'Rechtsonder',
    'top-left' => 'Linksboven',
    'top-right' => 'Rechtsboven',
    'none' => 'Geen zwevende knop',
];
$GLOBALS['TL_LANG']['tl_module']['nax_layout'] = ['Waar het paneel opent', 'Naast de knop, als lade aan de linker- of rechterrand van het scherm, of in het midden van het scherm.'];
$GLOBALS['TL_LANG']['tl_module']['nax_layout_options'] = [
    'popover' => 'Naast de knop',
    'drawer-left' => 'Lade links',
    'drawer-right' => 'Lade rechts',
    'center' => 'Midden van het scherm',
];
$GLOBALS['TL_LANG']['tl_module']['nax_overlay'] = ['Achtergrond verduisteren', 'Toont een donkere laag achter het geopende paneel. De rest van de pagina is pas weer te gebruiken als het paneel gesloten is; een klik op de donkere laag sluit het.'];
$GLOBALS['TL_LANG']['tl_module']['nax_statement'] = ['Toegankelijkheidsverklaring', 'Pagina met de toegankelijkheidsverklaring van deze website. Het paneel linkt ernaar. Leeg laten als er geen is.'];

// Licence note at the top of the form (the only place in the back end that names the maker)
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example_label'] = 'Tekst om te kopiëren (met link):';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example'] = 'Toegankelijkheidspaneel: NEO Accessibility van NEO Digital';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_text'] = 'Deze extensie is gratis. Voorwaarde: de juridische informatie van elke website waarop zij openbaar draait, noemt haar met een link naar https://neo-digital.at.';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_html_label'] = 'Of als HTML voor de broncode van de juridische informatie:';
$GLOBALS['TL_LANG']['tl_module']['licence_legend'] = 'Licentie';

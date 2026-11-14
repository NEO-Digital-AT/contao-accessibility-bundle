<?php

declare(strict_types=1);

// Front-end module "neo_accessibility" (accessibility panel).

// Legends
$GLOBALS['TL_LANG']['tl_module']['nax_features_legend'] = 'Postavke za posjetitelje';
$GLOBALS['TL_LANG']['tl_module']['nax_layout_legend'] = 'Gumb i ploča';

// Fields
$GLOBALS['TL_LANG']['tl_module']['nax_features'] = ['Ponuđene postavke', 'Posjetitelji na ploči vide samo označene postavke. „Čitanje naglas” koristi samo glasove instalirane na uređaju posjetitelja, osim ako su u nastavku dopušteni mrežni glasovi. „Veličina teksta” pretpostavlja veličine fonta u rem, em ili % u dizajnu web-stranice; s veličinama u px povećava se samo ploča (vidi „Povećaj cijelu stranicu”).'];
$GLOBALS['TL_LANG']['tl_module']['nax_features_options'] = [
    // Groups (headings of the panel)
    'text' => 'Tekst i sadržaj',
    'colour' => 'Boje',
    'orientation' => 'Orijentacija',
    // Settings
    'font' => 'Veličina teksta',
    'typeface' => 'Čitljiv font',
    'dyslexia' => 'Font za disleksiju',
    'readable' => 'Više razmaka',
    'align' => 'Tekst ulijevo',
    'headings' => 'Istakni naslove',
    'links' => 'Istakni poveznice',
    'noimages' => 'Sakrij slike',
    'cursor' => 'Veliki pokazivač',
    'contrast' => 'Visoki kontrast',
    'lowcontrast' => 'Smanji kontrast',
    'lowsaturation' => 'Prigušene boje',
    'grayscale' => 'Nijanse sive',
    'motion' => 'Zaustavi animacije',
    'focus' => 'Jači fokus',
    'guide' => 'Linija za čitanje',
    'mask' => 'Maska za čitanje',
    'structure' => 'Struktura stranice',
    'speak' => 'Čitanje naglas',
];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles'] = ['Brzi odabir', 'Gumbi na vrhu ploče koji uključuju nekoliko postavki odjednom. Svaki uključuje samo gore označene postavke; gumb bez njih se ne prikazuje.'];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_options'] = [
    'vision' => 'Slabovidnost',
    'dyslexia' => 'Disleksija',
    'concentration' => 'Koncentracija',
    'epilepsy' => 'Epilepsija',
    'motor' => 'Motorika',
    'reader' => 'Čitanje naglas',
];
// Quick options: label of an option (name: what it switches) and the note on those that cannot work
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_option'] = '%1$s: %2$s';
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_unavailable'] = 'Nije na ploči: %s. Nijedna od postavki koje uključuju nije označena pod „Ponuđene postavke”; označite jednu da bi se brzi odabir prikazao.';
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom'] = ['Povećaj cijelu stranicu', 'Za web-stranice čiji dizajn navodi veličine fonta u pikselima (px): „Veličina teksta” tada povećava cijelu stranicu kao zumiranje preglednika, a ne samo osnovni font; ploča zadržava svoju veličinu. Isključeno: „Veličina teksta” povećava osnovni font, što djeluje ondje gdje dizajn koristi rem, em ili %.'];
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom_unavailable'] = '„Povećaj cijelu stranicu” djeluje samo ako je pod „Ponuđene postavke” označeno „Veličina teksta”.';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices'] = ['Dopusti mrežne glasove preglednika', 'Čitanje naglas tada koristi i mrežne glasove preglednika (na primjer u Chromeu ili Microsoft Edgeu) kada uređaj posjetitelja nema glas za jezik. Preglednik pritom šalje pročitani tekst svojem proizvođaču; to mora stajati u izjavi o zaštiti podataka. Isključeno: samo glasovi instalirani na uređaju, tekst ostaje ondje.'];
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_unavailable'] = '„Dopusti mrežne glasove preglednika” djeluje samo ako je pod „Ponuđene postavke” označeno „Čitanje naglas”.';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_privacy'] = 'Mrežni glasovi su dopušteni: ako uređaj posjetitelja nema glas za jezik, preglednik šalje pročitani tekst svojem proizvođaču. Navedite to u izjavi o zaštiti podataka (vidi dokumentaciju).';
$GLOBALS['TL_LANG']['tl_module']['nax_position'] = ['Položaj gumba', 'Gdje se pojavljuje plutajući gumb. Bez plutajućeg gumba ploču otvara oznaka za umetanje {{accessibility::link}}. Ako su u istom kutu gumbi drugih proširenja (postavke kolačića, chat), raspoređuju se zajedno kako je postavljeno u izgledu stranice: jedan gumb postavki s izbornikom, jedan iznad drugoga ili jedan pokraj drugoga.'];
$GLOBALS['TL_LANG']['tl_module']['nax_position_options'] = [
    'bottom-left' => 'Dolje lijevo',
    'bottom-right' => 'Dolje desno',
    'top-left' => 'Gore lijevo',
    'top-right' => 'Gore desno',
    'none' => 'Bez plutajućeg gumba',
];
$GLOBALS['TL_LANG']['tl_module']['nax_layout'] = ['Gdje se ploča otvara', 'Pokraj gumba, kao ladica uz lijevi ili desni rub zaslona ili u sredini zaslona.'];
$GLOBALS['TL_LANG']['tl_module']['nax_layout_options'] = [
    'popover' => 'Pokraj gumba',
    'drawer-left' => 'Ladica lijevo',
    'drawer-right' => 'Ladica desno',
    'center' => 'Sredina zaslona',
];
$GLOBALS['TL_LANG']['tl_module']['nax_overlay'] = ['Zatamni pozadinu', 'Iza otvorene ploče prikazuje tamni sloj. Ostatak stranice može se koristiti tek kad se ploča zatvori; klik na tamni sloj zatvara je.'];
$GLOBALS['TL_LANG']['tl_module']['nax_statement'] = ['Izjava o pristupačnosti', 'Stranica s izjavom o pristupačnosti ove web-stranice. Ploča vodi na nju. Ostavite prazno ako je nema.'];

// Licence note at the top of the form (the only place in the back end that names the maker)
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example_label'] = 'Tekst za kopiranje (s poveznicom):';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example'] = 'Ploča pristupačnosti: NEO Accessibility tvrtke NEO Digital';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_text'] = 'Ovo proširenje je besplatno. Uvjet: impresum svakog web-mjesta na kojem javno radi navodi ga s poveznicom na https://neo-digital.at.';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_html_label'] = 'Ili kao HTML za izvorni kod impresuma:';
$GLOBALS['TL_LANG']['tl_module']['licence_legend'] = 'Licenca';

<?php

declare(strict_types=1);

// Front-end module "neo_accessibility" (accessibility panel).

// Legends
$GLOBALS['TL_LANG']['tl_module']['nax_features_legend'] = 'Impostazioni per i visitatori';
$GLOBALS['TL_LANG']['tl_module']['nax_layout_legend'] = 'Pulsante e pannello';

// Fields
$GLOBALS['TL_LANG']['tl_module']['nax_features'] = ['Impostazioni offerte', 'I visitatori vedono nel pannello solo le impostazioni spuntate. «Lettura ad alta voce» usa solo voci installate sul dispositivo del visitatore, a meno che le voci online siano consentite qui sotto. «Dimensione del testo» richiede dimensioni dei caratteri in rem, em o % nel design del sito; con dimensioni in px si ingrandisce solo il pannello (vedi «Ingrandire tutta la pagina»).'];
$GLOBALS['TL_LANG']['tl_module']['nax_features_options'] = [
    // Groups (headings of the panel)
    'text' => 'Testo e contenuti',
    'colour' => 'Colori',
    'orientation' => 'Orientamento',
    // Settings
    'font' => 'Dimensione del testo',
    'typeface' => 'Carattere leggibile',
    'dyslexia' => 'Carattere per la dislessia',
    'readable' => 'Più spazio',
    'align' => 'Testo a sinistra',
    'headings' => 'Evidenziare i titoli',
    'links' => 'Evidenziare i link',
    'noimages' => 'Nascondere le immagini',
    'cursor' => 'Cursore grande',
    'contrast' => 'Contrasto elevato',
    'lowcontrast' => 'Ridurre il contrasto',
    'lowsaturation' => 'Attenuare i colori',
    'grayscale' => 'Scala di grigi',
    'motion' => 'Fermare le animazioni',
    'focus' => 'Focus evidente',
    'guide' => 'Guida di lettura',
    'mask' => 'Maschera di lettura',
    'structure' => 'Struttura della pagina',
    'speak' => 'Lettura ad alta voce',
];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles'] = ['Opzioni rapide', 'Pulsanti in cima al pannello che attivano più impostazioni insieme. Ognuno attiva solo impostazioni spuntate sopra; un pulsante senza nessuna di esse non compare.'];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_options'] = [
    'vision' => 'Ipovisione',
    'dyslexia' => 'Dislessia',
    'concentration' => 'Concentrazione',
    'epilepsy' => 'Epilessia',
    'motor' => 'Motricità',
    'reader' => 'Lettura ad alta voce',
];
// Quick options: label of an option (name: what it switches) and the note on those that cannot work
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_option'] = '%1$s: %2$s';
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_unavailable'] = 'Non nel pannello: %s. Nessuna delle impostazioni che attivano è spuntata in «Impostazioni offerte»; ne spunti una per mostrare l’opzione rapida.';
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom'] = ['Ingrandire tutta la pagina', 'Per siti il cui design indica le dimensioni dei caratteri in pixel (px): «Dimensione del testo» ingrandisce allora tutta la pagina, come lo zoom del browser, invece della sola dimensione di base; il pannello mantiene la sua dimensione. Disattivato: «Dimensione del testo» ingrandisce la dimensione di base, cosa che funziona dove il design usa rem, em o %.'];
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom_unavailable'] = '«Ingrandire tutta la pagina» ha effetto solo se «Dimensione del testo» è spuntata in «Impostazioni offerte».';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices'] = ['Consentire le voci online del browser', 'La lettura ad alta voce usa allora anche le voci online del browser (per esempio in Chrome o Microsoft Edge) quando il dispositivo del visitatore non ha una voce per la lingua. Per farlo il browser invia il testo letto al suo produttore; l’informativa sulla privacy deve indicarlo. Disattivato: solo voci installate sul dispositivo, il testo resta lì.'];
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_unavailable'] = '«Consentire le voci online del browser» ha effetto solo se «Lettura ad alta voce» è spuntata in «Impostazioni offerte».';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_privacy'] = 'Le voci online sono consentite: se il dispositivo di un visitatore non ha una voce per la lingua, il browser invia il testo letto al suo produttore. Lo indichi nell’informativa sulla privacy (vedi la documentazione).';
$GLOBALS['TL_LANG']['tl_module']['nax_position'] = ['Posizione del pulsante', 'Dove compare il pulsante mobile. Senza pulsante mobile, l’insert tag {{accessibility::link}} apre il pannello. Se nello stesso angolo ci sono pulsanti di altre estensioni (impostazioni dei cookie, chat), vengono disposti insieme come impostato nel layout di pagina: un pulsante impostazioni con menu, impilati o uno accanto all’altro.'];
$GLOBALS['TL_LANG']['tl_module']['nax_position_options'] = [
    'bottom-left' => 'In basso a sinistra',
    'bottom-right' => 'In basso a destra',
    'top-left' => 'In alto a sinistra',
    'top-right' => 'In alto a destra',
    'none' => 'Nessun pulsante mobile',
];
$GLOBALS['TL_LANG']['tl_module']['nax_layout'] = ['Dove si apre il pannello', 'Accanto al pulsante, come pannello laterale sul bordo sinistro o destro dello schermo, oppure al centro dello schermo.'];
$GLOBALS['TL_LANG']['tl_module']['nax_layout_options'] = [
    'popover' => 'Accanto al pulsante',
    'drawer-left' => 'Pannello laterale a sinistra',
    'drawer-right' => 'Pannello laterale a destra',
    'center' => 'Centro dello schermo',
];
$GLOBALS['TL_LANG']['tl_module']['nax_overlay'] = ['Scurire lo sfondo', 'Mostra uno strato scuro dietro il pannello aperto. Il resto della pagina torna utilizzabile solo dopo la chiusura del pannello; un clic sullo strato scuro lo chiude.'];
$GLOBALS['TL_LANG']['tl_module']['nax_statement'] = ['Dichiarazione di accessibilità', 'Pagina con la dichiarazione di accessibilità di questo sito web. Il pannello vi rimanda. Lasci vuoto se non esiste.'];

// Licence note at the top of the form (the only place in the back end that names the maker)
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example_label'] = 'Testo da copiare (con link):';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example'] = 'Pannello di accessibilità: NEO Accessibility di NEO Digital';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_text'] = 'Questa estensione è gratuita. Condizione: le note legali di ogni sito su cui è in uso pubblicamente la citano con un link a https://neo-digital.at.';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_html_label'] = 'Oppure in HTML per il codice sorgente delle note legali:';
$GLOBALS['TL_LANG']['tl_module']['licence_legend'] = 'Licenza';

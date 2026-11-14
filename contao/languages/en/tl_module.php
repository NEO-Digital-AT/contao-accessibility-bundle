<?php

declare(strict_types=1);

// Front-end module "neo_accessibility" (accessibility panel).

// Legends
$GLOBALS['TL_LANG']['tl_module']['nax_features_legend'] = 'Settings for visitors';
$GLOBALS['TL_LANG']['tl_module']['nax_layout_legend'] = 'Button and panel';

// Fields
$GLOBALS['TL_LANG']['tl_module']['nax_features'] = ['Offered settings', 'Visitors see only the ticked settings in the panel. "Read aloud" uses only voices installed on the visitor\'s device, unless online voices are allowed below. "Text size" needs font sizes in rem, em or % in the design of the website; with sizes in px only the panel grows (see "Enlarge the whole page").'];
$GLOBALS['TL_LANG']['tl_module']['nax_features_options'] = [
    // Groups (headings of the panel)
    'text' => 'Text and content',
    'colour' => 'Colours',
    'orientation' => 'Orientation',
    // Settings
    'font' => 'Text size',
    'typeface' => 'Readable font',
    'dyslexia' => 'Dyslexia font',
    'readable' => 'More spacing',
    'align' => 'Align text left',
    'headings' => 'Highlight headings',
    'links' => 'Highlight links',
    'noimages' => 'Hide images',
    'cursor' => 'Large cursor',
    'contrast' => 'High contrast',
    'lowcontrast' => 'Reduce contrast',
    'lowsaturation' => 'Reduce colours',
    'grayscale' => 'Greyscale',
    'motion' => 'Stop animations',
    'focus' => 'Strong focus',
    'guide' => 'Reading guide',
    'mask' => 'Reading mask',
    'structure' => 'Page structure',
    'speak' => 'Read aloud',
];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles'] = ['Quick options', 'Buttons at the top of the panel that switch on several settings at once. Each one only switches settings ticked above; one without any of them is not shown.'];
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_options'] = [
    'vision' => 'Low vision',
    'dyslexia' => 'Dyslexia',
    'concentration' => 'Concentration',
    'epilepsy' => 'Epilepsy',
    'motor' => 'Motor skills',
    'reader' => 'Read aloud',
];
// Quick options: label of an option (name: what it switches) and the note on those that cannot work
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_option'] = '%1$s: %2$s';
$GLOBALS['TL_LANG']['tl_module']['nax_profiles_unavailable'] = 'Not in the panel: %s. None of the settings they switch is ticked under "Offered settings"; tick one of them to show the quick option.';
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom'] = ['Enlarge the whole page', 'For websites whose design sets font sizes in pixels (px): "Text size" then enlarges the whole page, like the zoom of the browser, instead of only the base font size; the panel keeps its size. Off: "Text size" scales the base font size, which works where the design uses rem, em or %.'];
$GLOBALS['TL_LANG']['tl_module']['nax_font_zoom_unavailable'] = '"Enlarge the whole page" only has an effect with "Text size" ticked under "Offered settings".';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices'] = ['Allow the browser\'s online voices', 'Read aloud then also uses online voices of the browser (for example in Chrome or Microsoft Edge) where the visitor\'s device has no voice for the language. The browser sends the text being read to its maker for this; the privacy policy must say so. Off: only voices installed on the device, the text stays there.'];
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_unavailable'] = '"Allow the browser\'s online voices" only has an effect with "Read aloud" ticked under "Offered settings".';
$GLOBALS['TL_LANG']['tl_module']['nax_online_voices_privacy'] = 'Online voices are allowed: where a visitor\'s device has no voice for the language, the browser sends the text being read to its maker. Mention this in the privacy policy (see the documentation).';
$GLOBALS['TL_LANG']['tl_module']['nax_position'] = ['Position of the button', 'Where the floating button appears. Without a floating button, the insert tag {{accessibility::link}} opens the panel. With buttons of other extensions in the same corner (cookie settings, chat), they are arranged together as set in the page layout: one settings button with a menu, stacked or side by side.'];
$GLOBALS['TL_LANG']['tl_module']['nax_position_options'] = [
    'bottom-left' => 'Bottom left',
    'bottom-right' => 'Bottom right',
    'top-left' => 'Top left',
    'top-right' => 'Top right',
    'none' => 'No floating button',
];
$GLOBALS['TL_LANG']['tl_module']['nax_layout'] = ['Where the panel opens', 'Next to the button, as a drawer at the left or right edge of the screen, or in the centre of the screen.'];
$GLOBALS['TL_LANG']['tl_module']['nax_layout_options'] = [
    'popover' => 'Next to the button',
    'drawer-left' => 'Drawer on the left',
    'drawer-right' => 'Drawer on the right',
    'center' => 'Centre of the screen',
];
$GLOBALS['TL_LANG']['tl_module']['nax_overlay'] = ['Darken the background', 'Shows a dark layer behind the open panel. The rest of the page cannot be used until the panel is closed; a click on the dark layer closes it.'];
$GLOBALS['TL_LANG']['tl_module']['nax_statement'] = ['Accessibility statement', 'Page with the accessibility statement of this website. The panel links to it. Leave empty if there is none.'];

// Licence note at the top of the form (the only place in the back end that names the maker)
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example_label'] = 'Text to copy (with link):';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_example'] = 'Accessibility panel: NEO Accessibility by NEO Digital';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_text'] = 'This extension is free. Condition: the legal notice of every website on which it runs publicly names it with a link to https://neo-digital.at.';
$GLOBALS['TL_LANG']['tl_module']['nax_licence_html_label'] = 'Or as HTML for the source code of the legal notice:';
$GLOBALS['TL_LANG']['tl_module']['licence_legend'] = 'Licence';

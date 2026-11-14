<?php

declare(strict_types=1);

use Neo\AccessibilityBundle\Backend\ModuleCallbacks;
use Neo\AccessibilityBundle\Panel\Features;
use Neo\AccessibilityBundle\Panel\Placement;

/*
 * Back-end form of the front-end module "neo_accessibility"
 * (Neo\AccessibilityBundle\Controller\FrontendModule\AccessibilityPanelController).
 *
 *  - nax_info: licence note, first block of the form under "Licence" (no database column);
 *  - nax_features: which settings visitors are offered (all by default), in
 *    the groups of the panel;
 *  - nax_profiles: which quick options appear at the top of the panel (all by
 *    default); each one only switches settings that are offered, and the
 *    field only lists the quick options that switch at least one of them
 *    (with what they switch); nax_profiles_info names the others and what
 *    they need. nax_features reloads the form on change for this;
 *  - nax_font_zoom: "Text size" enlarges the whole page (CSS zoom of the
 *    content, the panel excluded) instead of the root font size, for designs
 *    with font sizes in px. Off by default. Only shown while "Text size" is
 *    offered; otherwise nax_font_zoom_info says why it is missing;
 *  - nax_online_voices: read aloud may also use online voices of the browser
 *    (off: only voices installed on the visitor's device). Only shown while
 *    read aloud is offered; otherwise nax_online_voices_info says why it is
 *    missing (ModuleCallbacks::adjustPalette());
 *  - nax_position: corner of the floating button, or no button (then the
 *    insert tag {{accessibility::link}} opens the panel). With buttons of
 *    other extensions in the same corner (cookie settings, chat), the
 *    buttons at the edge of the screen of NEO Basic arrange them as set in
 *    the page layout;
 *  - nax_layout: panel next to the button, as a drawer at a side, or centred;
 *  - nax_overlay: dimmed backdrop, the panel becomes a modal dialog;
 *  - nax_statement: page with the accessibility statement, linked in the panel.
 *
 * Visitor texts are not fields: they come from the translations in the page
 * language and can be changed in the website (README, "Customising").
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['neo_accessibility'] =
    '{licence_legend},nax_info;{title_legend},name,type;'
    .'{nax_features_legend},nax_features,nax_font_zoom,nax_font_zoom_info,nax_profiles,nax_profiles_info,nax_online_voices,nax_online_voices_info;'
    .'{nax_layout_legend},nax_position,nax_layout,nax_overlay,nax_statement;'
    .'{template_legend:hide},customTpl;'
    .'{protected_legend:hide},protected;'
    .'{expert_legend:hide},guests,cssID';

$GLOBALS['TL_DCA']['tl_module']['fields']['nax_info'] = [
    // Shown to everyone who may edit the module, without an extra field permission.
    'exclude' => false,
    'input_field_callback' => [ModuleCallbacks::class, 'licenceInfo'],
    'eval' => ['doNotShow' => true],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['nax_features'] = [
    'inputType' => 'checkbox',
    'exclude' => true,
    // Grouped as in the panel; the group keys are translated through the same reference.
    'options' => Features::GROUPS,
    'reference' => &$GLOBALS['TL_LANG']['tl_module']['nax_features_options'],
    'default' => Features::ALL,
    // Reloads the form: the quick options below depend on this choice.
    'eval' => ['multiple' => true, 'mandatory' => true, 'collapseUncheckedGroups' => true, 'submitOnChange' => true, 'tl_class' => 'clr'],
    'load_callback' => [[ModuleCallbacks::class, 'loadFeatures']],
    'sql' => 'blob NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['nax_profiles'] = [
    'inputType' => 'checkbox',
    'exclude' => true,
    // Only the quick options that work with the offered settings, labelled with what they switch.
    'options_callback' => [ModuleCallbacks::class, 'profileOptions'],
    'default' => array_keys(Features::PROFILES),
    'eval' => ['multiple' => true, 'tl_class' => 'clr'],
    'load_callback' => [[ModuleCallbacks::class, 'loadProfiles']],
    'save_callback' => [[ModuleCallbacks::class, 'saveProfiles']],
    // NULL: not saved since the field exists, all quick options; an empty
    // serialized list: none (Features::profilesFromModuleValue()).
    'sql' => 'blob NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['nax_profiles_info'] = [
    // Note below the quick options; empty while every quick option works. No database column.
    'exclude' => false,
    'input_field_callback' => [ModuleCallbacks::class, 'profilesInfo'],
    'eval' => ['doNotShow' => true],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['nax_font_zoom'] = [
    'inputType' => 'checkbox',
    'exclude' => true,
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => 'boolean', 'default' => false],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['nax_font_zoom_info'] = [
    // Note in place of nax_font_zoom while "Text size" is not offered. No database column.
    'exclude' => false,
    'input_field_callback' => [ModuleCallbacks::class, 'fontZoomInfo'],
    'eval' => ['doNotShow' => true],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['nax_online_voices'] = [
    'inputType' => 'checkbox',
    'exclude' => true,
    // Reloads the form: the privacy note below follows the choice at once.
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => 'boolean', 'default' => false],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['nax_online_voices_info'] = [
    // Note at nax_online_voices: why it is missing (read aloud not offered) or, while
    // online voices are allowed, that the privacy policy must name them. No database column.
    'exclude' => false,
    'input_field_callback' => [ModuleCallbacks::class, 'onlineVoicesInfo'],
    'eval' => ['doNotShow' => true],
];

// Removes nax_online_voices from the form while read aloud is not offered,
// nax_font_zoom while text size is not offered.
$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = [ModuleCallbacks::class, 'adjustPalette'];

$GLOBALS['TL_DCA']['tl_module']['fields']['nax_position'] = [
    'inputType' => 'select',
    'exclude' => true,
    'options' => Placement::POSITIONS,
    'reference' => &$GLOBALS['TL_LANG']['tl_module']['nax_position_options'],
    'default' => Placement::DEFAULT_POSITION,
    'eval' => ['tl_class' => 'w50'],
    'load_callback' => [[ModuleCallbacks::class, 'loadPosition']],
    'sql' => "varchar(16) NOT NULL default 'bottom-left'",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['nax_layout'] = [
    'inputType' => 'select',
    'exclude' => true,
    'options' => Placement::LAYOUTS,
    'reference' => &$GLOBALS['TL_LANG']['tl_module']['nax_layout_options'],
    'default' => Placement::DEFAULT_LAYOUT,
    'eval' => ['tl_class' => 'w50'],
    'sql' => "varchar(16) NOT NULL default 'popover'",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['nax_overlay'] = [
    'inputType' => 'checkbox',
    'exclude' => true,
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => ['type' => 'boolean', 'default' => false],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['nax_statement'] = [
    'inputType' => 'pageTree',
    'exclude' => true,
    'foreignKey' => 'tl_page.title',
    'eval' => ['fieldType' => 'radio', 'tl_class' => 'clr'],
    'sql' => 'int(10) unsigned NOT NULL default 0',
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];

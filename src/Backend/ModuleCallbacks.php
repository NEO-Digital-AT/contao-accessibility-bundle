<?php

declare(strict_types=1);

namespace Neo\AccessibilityBundle\Backend;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\DataContainer;
use Contao\StringUtil;
use Neo\AccessibilityBundle\Panel\Features;
use Neo\AccessibilityBundle\Panel\Placement;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * DCA callbacks of the "neo_accessibility" front-end module (tl_module).
 * Texts: TL_LANG.tl_module of this bundle.
 *
 * A setting of the form never looks as if it worked when it does not: a quick
 * option whose settings are all unticked under "Offered settings" is not
 * offered in its field, and a note directly below the field names it and the
 * settings it needs. The other quick options name what they switch with the
 * current choice. "Offered settings" reloads the form when it changes, so both
 * always match the choice above them.
 */
final class ModuleCallbacks
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * input_field_callback of "nax_info": the licence note at the top of the
     * edit form, with an example credit for the legal notice to copy.
     */
    public function licenceInfo(): string
    {
        return CreditNote::forTable('tl_module', 'nax_licence_');
    }

    /**
     * load_callback of "nax_features": a module without a choice (created before
     * the field existed) offers everything, so the form shows every box ticked.
     */
    public function loadFeatures(mixed $value): mixed
    {
        return [] === StringUtil::deserialize($value, true) ? serialize(Features::ALL) : $value;
    }

    /**
     * options_callback of "nax_profiles": the quick options that switch at least
     * one offered setting, each labelled with the settings it switches.
     *
     * @return array<string, string>
     */
    public function profileOptions(DataContainer|null $dc = null): array
    {
        $options = [];

        foreach (Features::profiles($this->offered($dc), array_keys(Features::PROFILES)) as $profile) {
            $options[$profile['key']] = \sprintf(
                $this->text('nax_profiles_option', '%1$s: %2$s'),
                $this->profileName($profile['key']),
                $this->settingsList($profile['settings']),
            );
        }

        return $options;
    }

    /**
     * input_field_callback of "nax_profiles_info": below the quick options, the
     * ones that cannot work with the current choice and what they need. Empty
     * when every quick option works.
     */
    public function profilesInfo(DataContainer|null $dc = null): string
    {
        $missing = array_diff(array_keys(Features::PROFILES), array_keys($this->profileOptions($dc)));

        if ([] === $missing) {
            return '';
        }

        $items = array_map(function (string $key): string {
            $needed = array_keys(Features::PROFILES[$key]);

            foreach ($needed as $feature) {
                if (isset(Features::SUBSTITUTES[$feature])) {
                    $needed[] = Features::SUBSTITUTES[$feature];
                }
            }

            return $this->profileName($key).' ('.implode(', ', array_map(fn (string $feature): string => $this->featureName($feature), array_unique($needed))).')';
        }, array_values($missing));

        return \sprintf(
            '<div class="widget clr"><p class="tl_info" style="margin:0">%s</p></div>',
            StringUtil::specialchars(\sprintf($this->text('nax_profiles_unavailable', '%s'), implode('; ', $items))),
        );
    }

    /**
     * onload_callback of tl_module: fields that cannot work with the current
     * record leave the form; their stored values stay.
     *  - "Allow the browser's online voices" only matters while read aloud is
     *    offered; nax_online_voices_info says why it is missing.
     *  - "Enlarge the whole page" only matters while text size is offered;
     *    nax_font_zoom_info says why it is missing.
     */
    public function adjustPalette(DataContainer|null $dc = null): void
    {
        $record = $dc?->getCurrentRecord();

        if (null === $record || 'neo_accessibility' !== ($record['type'] ?? null)) {
            return;
        }

        $remove = [];

        if (!$this->speakOffered($dc)) {
            $remove[] = 'nax_online_voices';
        }

        if (!$this->fontOffered($dc)) {
            $remove[] = 'nax_font_zoom';
        }

        if ([] === $remove) {
            return;
        }

        $manipulator = PaletteManipulator::create();

        foreach ($remove as $field) {
            $manipulator->removeField($field);
        }

        $manipulator->applyToPalette('neo_accessibility', 'tl_module');
    }

    /**
     * input_field_callback of "nax_online_voices_info": the reason in place of
     * the field while read aloud is not offered; while online voices are
     * allowed, that the browser then sends the text to its maker (the field's
     * help shows only one line); empty otherwise.
     */
    public function onlineVoicesInfo(DataContainer|null $dc = null): string
    {
        if (!$this->speakOffered($dc)) {
            $text = $this->text('nax_online_voices_unavailable', '');
        } elseif ((bool) ($dc?->getCurrentRecord()['nax_online_voices'] ?? false)) {
            $text = $this->text('nax_online_voices_privacy', '');
        } else {
            return '';
        }

        return \sprintf('<div class="widget clr"><p class="tl_info" style="margin:0">%s</p></div>', StringUtil::specialchars($text));
    }

    /**
     * input_field_callback of "nax_font_zoom_info": the reason in place of
     * "Enlarge the whole page" while text size is not offered; empty otherwise.
     */
    public function fontZoomInfo(DataContainer|null $dc = null): string
    {
        if ($this->fontOffered($dc)) {
            return '';
        }

        return \sprintf('<div class="widget clr"><p class="tl_info" style="margin:0">%s</p></div>', StringUtil::specialchars($this->text('nax_font_zoom_unavailable', '')));
    }

    /**
     * load_callback of "nax_profiles": a module not saved since the field exists
     * shows all quick options ticked. Quick options that are not offered in the
     * field (none of their settings ticked) are left out of the value too:
     * Contao would show them as ticked "unknown options". saveProfiles() keeps
     * their stored state.
     */
    public function loadProfiles(mixed $value, DataContainer|null $dc = null): string
    {
        $chosen = Features::profilesFromModuleValue(null === $value ? null : StringUtil::deserialize($value, true));

        return serialize(array_values(array_intersect($chosen, array_keys($this->profileOptions($dc)))));
    }

    /**
     * save_callback of "nax_profiles": nothing ticked is stored as an empty
     * list, not as NULL, which would mean "all" (a module from before the field).
     * Quick options that were not offered in the form (none of their settings
     * ticked) keep their stored state, so they come back as they were when one
     * of their settings is ticked again.
     */
    public function saveProfiles(mixed $value, DataContainer|null $dc = null): mixed
    {
        $chosen = StringUtil::deserialize($value, true);
        $record = $dc?->getCurrentRecord();

        if (null !== $record) {
            $hidden = array_diff(array_keys(Features::PROFILES), array_keys($this->profileOptions($dc)));
            $before = Features::profilesFromModuleValue($record['nax_profiles'] ?? null);
            $chosen = array_merge($chosen, array_intersect($before, $hidden));
        }

        return serialize(array_values(array_intersect(array_keys(Features::PROFILES), $chosen)));
    }

    /**
     * load_callback of "nax_position": the early values "left" and "right" are
     * shown as the bottom corners they stand for.
     */
    public function loadPosition(mixed $value): string
    {
        return Placement::position($value);
    }

    private function fontOffered(DataContainer|null $dc): bool
    {
        return \in_array(Features::FONT, $this->offered($dc), true);
    }

    private function speakOffered(DataContainer|null $dc): bool
    {
        return \in_array(Features::SPEAK, $this->offered($dc), true);
    }

    /**
     * Offered settings of the record being edited, as stored. "Offered settings"
     * submits the form on change, so the stored choice is the one in the form.
     *
     * @return list<string>
     */
    private function offered(DataContainer|null $dc): array
    {
        $record = $dc?->getCurrentRecord();

        return Features::fromModuleValue($record['nax_features'] ?? null);
    }

    /**
     * @param array<string, bool|int> $settings
     */
    private function settingsList(array $settings): string
    {
        $names = [];

        foreach ($settings as $feature => $value) {
            $name = $this->featureName($feature);

            if (Features::FONT === $feature && \is_int($value)) {
                $name .= ' '.str_replace('{value}', (string) $value, $this->translator->trans('font.value', [], 'neo_accessibility'));
            }

            $names[] = $name;
        }

        return implode(', ', $names);
    }

    private function featureName(string $feature): string
    {
        return (string) ($GLOBALS['TL_LANG']['tl_module']['nax_features_options'][$feature] ?? $feature);
    }

    private function profileName(string $profile): string
    {
        return (string) ($GLOBALS['TL_LANG']['tl_module']['nax_profiles_options'][$profile] ?? $profile);
    }

    private function text(string $key, string $fallback): string
    {
        $text = $GLOBALS['TL_LANG']['tl_module'][$key] ?? '';

        return \is_string($text) && '' !== $text ? $text : $fallback;
    }
}

<?php

declare(strict_types=1);

namespace Neo\AccessibilityBundle\Panel;

use Contao\StringUtil;

/**
 * The display settings the panel can offer, by group and in the order they
 * appear, and the quick options (profiles) that switch several at once.
 *
 *  - "font" is the text size (steps in FONT_STEPS);
 *  - "structure" is an action (list of the page's areas and headings), not a
 *    stored setting;
 *  - every other entry is an on/off switch that sets the attribute
 *    data-a11y-<name> on <html>.
 *
 * Websites style against these attribute names and the visitor's stored
 * choices use them as keys, so they must never change. New settings are added,
 * none is renamed.
 */
final class Features
{
    public const FONT = 'font';

    public const STRUCTURE = 'structure';

    public const SPEAK = 'speak';

    /** Settings in panel order, by group (the group keys name the headings in the panel). */
    public const GROUPS = [
        'text' => [self::FONT, 'typeface', 'dyslexia', 'readable', 'align', 'headings', 'links', 'noimages', 'cursor'],
        'colour' => ['contrast', 'lowcontrast', 'lowsaturation', 'grayscale'],
        'orientation' => ['motion', 'focus', 'guide', 'mask', self::STRUCTURE, self::SPEAK],
    ];

    public const ALL = [...self::GROUPS['text'], ...self::GROUPS['colour'], ...self::GROUPS['orientation']];

    /**
     * The settings of the first version. A module that offered exactly these
     * gets the newer ones from Migration\NewSettingsMigration.
     */
    public const FIRST_VERSION = ['font', 'contrast', 'grayscale', 'readable', 'links', 'cursor', 'focus', 'motion'];

    /**
     * Key under which the visitor's choices are kept in localStorage. Must match
     * STORAGE_KEY in public/accessibility.js.
     */
    public const STORAGE_KEY = 'nax-settings';

    /**
     * Text size in percent. Must match FONT_STEPS in public/accessibility.js.
     * Stored values between the steps (the first version stored 145 and 160)
     * snap to the nearest step.
     */
    public const FONT_STEPS = [100, 115, 130, 150];

    public const FONT_MIN = 100;

    /**
     * Quick options and the settings each one switches on: true for a switch,
     * a percentage for the text size (at least this size).
     */
    public const PROFILES = [
        'vision' => [self::FONT => 130, 'contrast' => true, 'cursor' => true, 'focus' => true],
        'dyslexia' => ['dyslexia' => true, 'readable' => true, 'align' => true],
        'concentration' => ['motion' => true, 'mask' => true],
        'epilepsy' => ['motion' => true, 'lowsaturation' => true],
        'motor' => ['cursor' => true, 'focus' => true],
        'reader' => [self::SPEAK => true],
    ];

    /** Stand-in for a profile setting that the module does not offer. */
    public const SUBSTITUTES = [
        'dyslexia' => 'typeface',
        'mask' => 'guide',
        'lowsaturation' => 'grayscale',
    ];

    /**
     * Offered settings of a module, from the serialized checkbox value of
     * tl_module.nax_features. Nothing chosen (also: a module saved before the
     * field existed) means everything is offered.
     *
     * @return list<string>
     */
    public static function fromModuleValue(mixed $value): array
    {
        $chosen = StringUtil::deserialize($value, true);
        $offered = array_values(array_filter(self::ALL, static fn (string $feature): bool => \in_array($feature, $chosen, true)));

        return [] === $offered ? self::ALL : $offered;
    }

    /**
     * The on/off switches among the offered settings (everything except the
     * text size and the page structure).
     *
     * @param list<string> $offered
     *
     * @return list<string>
     */
    public static function switches(array $offered): array
    {
        return array_values(array_filter($offered, static fn (string $feature): bool => self::FONT !== $feature && self::STRUCTURE !== $feature));
    }

    /**
     * The offered settings by group, in panel order; groups without an offered
     * setting are left out.
     *
     * @param list<string> $offered
     *
     * @return list<array{key: string, features: list<string>}>
     */
    public static function groups(array $offered): array
    {
        $groups = [];

        foreach (self::GROUPS as $key => $features) {
            $features = array_values(array_intersect($features, $offered));

            if ([] !== $features) {
                $groups[] = ['key' => $key, 'features' => $features];
            }
        }

        return $groups;
    }

    /**
     * Profiles chosen in a module (tl_module.nax_profiles). NULL — the module
     * was not saved since the field exists — means all; a saved empty choice
     * means none.
     *
     * @return list<string>
     */
    public static function profilesFromModuleValue(mixed $value): array
    {
        if (null === $value) {
            return array_keys(self::PROFILES);
        }

        $chosen = StringUtil::deserialize($value, true);

        return array_values(array_filter(array_keys(self::PROFILES), static fn (string $profile): bool => \in_array($profile, $chosen, true)));
    }

    /**
     * The quick options to show, each with the settings it switches, limited to
     * the offered settings. A setting that is not offered is replaced by its
     * stand-in (SUBSTITUTES) if that is offered; a profile left without any
     * setting is not shown.
     *
     * @param list<string> $offered
     * @param list<string> $chosen  see profilesFromModuleValue()
     *
     * @return list<array{key: string, settings: array<string, bool|int>}>
     */
    public static function profiles(array $offered, array $chosen): array
    {
        $profiles = [];

        foreach (self::PROFILES as $key => $definition) {
            if (!\in_array($key, $chosen, true)) {
                continue;
            }

            $settings = [];

            foreach ($definition as $feature => $value) {
                if (!\in_array($feature, $offered, true)) {
                    $feature = self::SUBSTITUTES[$feature] ?? null;
                }

                if (null !== $feature && \in_array($feature, $offered, true)) {
                    $settings[$feature] = $value;
                }
            }

            if ([] !== $settings) {
                $profiles[] = ['key' => $key, 'settings' => $settings];
            }
        }

        return $profiles;
    }
}

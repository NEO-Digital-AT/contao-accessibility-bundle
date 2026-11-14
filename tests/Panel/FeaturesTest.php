<?php

declare(strict_types=1);

namespace Neo\AccessibilityBundle\Tests\Panel;

use Neo\AccessibilityBundle\Panel\Features;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class FeaturesTest extends TestCase
{
    public function testEverySettingIsInExactlyOneGroup(): void
    {
        $this->assertSame(array_values(array_unique(Features::ALL)), Features::ALL);
        $this->assertSame([], array_diff(Features::FIRST_VERSION, Features::ALL));
        $this->assertContains(Features::FONT, Features::ALL);
        $this->assertContains(Features::STRUCTURE, Features::ALL);
        $this->assertContains(Features::SPEAK, Features::ALL);
    }

    public function testProfilesAndStandInsUseKnownSettingsOnly(): void
    {
        foreach (Features::PROFILES as $profile => $settings) {
            $this->assertSame([], array_diff(array_keys($settings), Features::ALL), $profile);
        }

        $this->assertSame([], array_diff(array_merge(array_keys(Features::SUBSTITUTES), Features::SUBSTITUTES), Features::ALL));
    }

    #[DataProvider('moduleValues')]
    public function testOfferedSettingsOfAModule(mixed $value, array $offered): void
    {
        $this->assertSame($offered, Features::fromModuleValue($value));
    }

    /**
     * @return iterable<string, array{mixed, list<string>}>
     */
    public static function moduleValues(): iterable
    {
        yield 'module from before the field' => [null, Features::ALL];
        yield 'nothing ticked' => [serialize([]), Features::ALL];
        yield 'only unknown values' => [serialize(['removed']), Features::ALL];
        yield 'choice in panel order' => [serialize(['motion', 'font', 'removed', 'contrast']), ['font', 'contrast', 'motion']];
        yield 'already an array' => [['links'], ['links']];
    }

    public function testSwitchesLeaveOutTextSizeAndStructure(): void
    {
        $this->assertSame(['contrast', 'speak'], Features::switches(['font', 'contrast', 'structure', 'speak']));
        $this->assertNotContains(Features::FONT, Features::switches(Features::ALL));
        $this->assertNotContains(Features::STRUCTURE, Features::switches(Features::ALL));
    }

    public function testGroupsInPanelOrderWithoutEmptyGroups(): void
    {
        $this->assertSame(
            [['key' => 'text', 'features' => ['font', 'links']], ['key' => 'orientation', 'features' => ['focus']]],
            Features::groups(['focus', 'links', 'font']),
        );
    }

    #[DataProvider('profileValues')]
    public function testChosenProfiles(mixed $value, array $profiles): void
    {
        $this->assertSame($profiles, Features::profilesFromModuleValue($value));
    }

    /**
     * @return iterable<string, array{mixed, list<string>}>
     */
    public static function profileValues(): iterable
    {
        yield 'never saved means all' => [null, array_keys(Features::PROFILES)];
        yield 'saved empty means none' => [serialize([]), []];
        yield 'empty string means none' => ['', []];
        yield 'in definition order, unknown dropped' => [serialize(['reader', 'vision', 'removed']), ['vision', 'reader']];
    }

    public function testProfilesUseStandInsAndDropProfilesWithoutSettings(): void
    {
        $offered = ['font', 'typeface', 'contrast', 'motion', 'grayscale'];

        $this->assertSame(
            [
                ['key' => 'vision', 'settings' => ['font' => 130, 'contrast' => true]],
                ['key' => 'dyslexia', 'settings' => ['typeface' => true]],
                ['key' => 'concentration', 'settings' => ['motion' => true]],
                ['key' => 'epilepsy', 'settings' => ['motion' => true, 'grayscale' => true]],
            ],
            Features::profiles($offered, array_keys(Features::PROFILES)),
        );
    }

    public function testProfilesNotChosenAreNotShown(): void
    {
        $this->assertSame([['key' => 'reader', 'settings' => ['speak' => true]]], Features::profiles(Features::ALL, ['reader']));
        $this->assertSame([], Features::profiles(Features::ALL, []));
    }

    public function testTheOriginalSettingWinsOverItsStandIn(): void
    {
        $profiles = Features::profiles(['dyslexia', 'typeface'], ['dyslexia']);

        $this->assertSame([['key' => 'dyslexia', 'settings' => ['dyslexia' => true]]], $profiles);
    }

    public function testStorageKeyAndTextSizesMatchThePanelScript(): void
    {
        $script = (string) file_get_contents(\dirname(__DIR__, 2).'/public/accessibility.js');

        $this->assertMatchesRegularExpression('/var STORAGE_KEY = \''.preg_quote(Features::STORAGE_KEY, '/').'\';/', $script);
        $this->assertMatchesRegularExpression('/var FONT_STEPS = \['.preg_quote(implode(', ', Features::FONT_STEPS), '/').'\];/', $script);
        $this->assertSame(Features::FONT_MIN, Features::FONT_STEPS[0]);
    }

    /**
     * Visitors see a label for every setting, quick option and group, in every language.
     */
    public function testEverySettingHasAVisitorTextInEveryLanguage(): void
    {
        $files = glob(\dirname(__DIR__, 2).'/translations/neo_accessibility.*.yaml') ?: [];
        $this->assertCount(11, $files);

        foreach ($files as $file) {
            $texts = Yaml::parseFile($file);

            foreach (Features::ALL as $feature) {
                $this->assertNotEmpty($texts[$feature]['label'] ?? null, basename($file).': '.$feature);
            }

            foreach (array_keys(Features::PROFILES) as $profile) {
                $this->assertNotEmpty($texts['profile'][$profile]['label'] ?? null, basename($file).': profile '.$profile);
            }

            foreach (array_keys(Features::GROUPS) as $group) {
                $this->assertNotEmpty($texts['group'][$group] ?? null, basename($file).': group '.$group);
            }
        }
    }

    /**
     * The back end names every setting and quick option, in every language.
     */
    public function testEverySettingHasABackEndLabelInEveryLanguage(): void
    {
        $files = glob(\dirname(__DIR__, 2).'/contao/languages/*/tl_module.php') ?: [];
        $this->assertCount(11, $files);

        foreach ($files as $file) {
            $GLOBALS['TL_LANG'] = [];
            include $file;
            $lang = $GLOBALS['TL_LANG']['tl_module'];

            $this->assertSame([], array_diff(Features::ALL, array_keys($lang['nax_features_options'])), $file);
            $this->assertSame([], array_diff(array_keys(Features::PROFILES), array_keys($lang['nax_profiles_options'])), $file);
        }

        unset($GLOBALS['TL_LANG']);
    }
}

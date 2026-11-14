<?php

declare(strict_types=1);

namespace Neo\AccessibilityBundle\Tests\Backend;

use Contao\DataContainer;
use Neo\AccessibilityBundle\Backend\ModuleCallbacks;
use Neo\AccessibilityBundle\Panel\Features;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The module form never offers a setting that cannot work, and says why.
 */
final class ModuleCallbacksTest extends TestCase
{
    protected function setUp(): void
    {
        include \dirname(__DIR__, 2).'/contao/languages/en/tl_module.php';
        $GLOBALS['TL_DCA']['tl_module']['palettes']['neo_accessibility'] = '{title_legend},name,type;{nax_features_legend},nax_features,nax_font_zoom,nax_font_zoom_info,nax_profiles,nax_profiles_info,nax_online_voices,nax_online_voices_info;{nax_layout_legend},nax_position,nax_layout';
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TL_LANG'], $GLOBALS['TL_DCA']);
    }

    public function testAModuleWithoutChoiceShowsEverySettingTicked(): void
    {
        $callbacks = $this->callbacks();

        $this->assertSame(serialize(Features::ALL), $callbacks->loadFeatures(null));
        $this->assertSame(serialize(Features::ALL), $callbacks->loadFeatures(serialize([])));
        $this->assertSame(serialize(['font']), $callbacks->loadFeatures(serialize(['font'])));
    }

    public function testQuickOptionsNameWhatTheySwitchWithTheCurrentChoice(): void
    {
        $options = $this->callbacks()->profileOptions($this->record(['font', 'contrast', 'motion']));

        $this->assertSame(['vision', 'concentration', 'epilepsy'], array_keys($options));
        $this->assertSame('Low vision: Text size 130%, High contrast', $options['vision']);
    }

    public function testQuickOptionsThatCannotWorkAreNamedWithWhatTheyNeed(): void
    {
        $info = $this->callbacks()->profilesInfo($this->record(['font', 'contrast', 'motion']));

        $this->assertStringContainsString('Not in the panel:', $info);
        $this->assertStringContainsString('Dyslexia (', $info);
        $this->assertStringContainsString('Read aloud (Read aloud)', $info);
        $this->assertStringNotContainsString('Low vision', $info);
        $this->assertSame('', $this->callbacks()->profilesInfo($this->record(Features::ALL)));
    }

    public function testHiddenQuickOptionsKeepTheirStoredState(): void
    {
        $callbacks = $this->callbacks();
        $dc = $this->record(['font', 'contrast'], serialize(['vision', 'reader']));

        // "Read aloud" is not in the form (speak not offered) …
        $this->assertSame(serialize(['vision']), $callbacks->loadProfiles(serialize(['vision', 'reader']), $dc));

        // … and comes back as it was when read aloud is offered again.
        $this->assertSame(serialize(['reader']), $callbacks->saveProfiles(serialize([]), $dc));
    }

    public function testNothingTickedIsStoredAsAnEmptyListNotAsAll(): void
    {
        $saved = $this->callbacks()->saveProfiles('', $this->record(Features::ALL, null));

        $this->assertSame(serialize([]), $saved);
        $this->assertSame([], Features::profilesFromModuleValue($saved));
    }

    public function testANeverSavedModuleShowsAllPossibleQuickOptions(): void
    {
        $this->assertSame(serialize(['vision', 'motor']), $this->callbacks()->loadProfiles(null, $this->record(['font', 'cursor'])));
    }

    public function testOnlineVoicesLeaveTheFormWhileReadAloudIsNotOffered(): void
    {
        $this->callbacks()->adjustPalette($this->record(['font', 'contrast']));

        $palette = $GLOBALS['TL_DCA']['tl_module']['palettes']['neo_accessibility'];
        $this->assertStringNotContainsString('nax_online_voices,', $palette);
        $this->assertStringContainsString('nax_online_voices_info', $palette);
        $this->assertStringContainsString('only has an effect with', $this->callbacks()->onlineVoicesInfo($this->record(['font'])));
    }

    public function testOnlineVoicesStayWhileReadAloudIsOffered(): void
    {
        $before = $GLOBALS['TL_DCA']['tl_module']['palettes']['neo_accessibility'];

        $this->callbacks()->adjustPalette($this->record(['font', 'speak']));
        $this->callbacks()->adjustPalette($this->record(['font'], null, 'navigation'));

        $this->assertSame($before, $GLOBALS['TL_DCA']['tl_module']['palettes']['neo_accessibility']);
        $this->assertSame('', $this->callbacks()->onlineVoicesInfo($this->record(['speak'])));
        $this->assertStringContainsString('Online voices are allowed', $this->callbacks()->onlineVoicesInfo($this->record(['speak'], null, 'neo_accessibility', true)));
    }

    public function testEnlargingThePageOnlyWithTextSize(): void
    {
        $callbacks = $this->callbacks();

        $callbacks->adjustPalette($this->record(['font', 'speak']));
        $this->assertStringContainsString('nax_font_zoom,', $GLOBALS['TL_DCA']['tl_module']['palettes']['neo_accessibility']);
        $this->assertSame('', $callbacks->fontZoomInfo($this->record(['font', 'speak'])));

        $callbacks->adjustPalette($this->record(['contrast', 'speak']));
        $this->assertStringNotContainsString('nax_font_zoom,', $GLOBALS['TL_DCA']['tl_module']['palettes']['neo_accessibility']);
        $this->assertStringContainsString('nax_font_zoom_info', $GLOBALS['TL_DCA']['tl_module']['palettes']['neo_accessibility']);
        $this->assertStringContainsString('only has an effect with &quot;Text size&quot;', $callbacks->fontZoomInfo($this->record(['contrast'])));
    }

    public function testEarlyPositionsAreShownAsTheirCorners(): void
    {
        $this->assertSame('bottom-right', $this->callbacks()->loadPosition('right'));
    }

    /**
     * @param list<string> $features
     */
    private function record(array $features, string|null $profiles = null, string $type = 'neo_accessibility', bool $onlineVoices = false): DataContainer
    {
        $dc = $this->createStub(DataContainer::class);
        $dc->method('getCurrentRecord')->willReturn([
            'type' => $type,
            'nax_features' => serialize($features),
            'nax_profiles' => $profiles,
            'nax_online_voices' => $onlineVoices ? '1' : '',
        ]);

        return $dc;
    }

    private function callbacks(): ModuleCallbacks
    {
        $translator = $this->createStub(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(static fn (string $id): string => 'font.value' === $id ? '{value}%' : $id);

        return new ModuleCallbacks($translator);
    }
}

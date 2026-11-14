<?php

declare(strict_types=1);

namespace Neo\AccessibilityBundle\Tests\Panel;

use Neo\AccessibilityBundle\Panel\Features;
use Neo\AccessibilityBundle\Panel\HeadScript;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\ExecutableFinder;

final class HeadScriptTest extends TestCase
{
    public function testOnlyOfferedSwitchesAreApplied(): void
    {
        $script = HeadScript::build(['contrast', 'links', 'structure']);

        $this->assertStringContainsString('f=["contrast","links"]', $script);
        $this->assertStringContainsString('localStorage.getItem("nax-settings")', $script);
        $this->assertStringNotContainsString('fontSize', $script, 'text size not offered');
        $this->assertStringStartsWith('(function(){try{', $script);
        $this->assertStringEndsWith('}catch(x){}})();', $script);
    }

    public function testTextSizeOnlyWhenOffered(): void
    {
        $script = HeadScript::build(['font']);

        $this->assertStringContainsString('f=[]', $script);
        $this->assertStringContainsString('f=[100,115,130,150]', $script);
        $this->assertStringContainsString('data-a11y-font', $script);
    }

    /**
     * The script in a minimal browser environment (Node.js): what it sets on
     * <html> for stored choices.
     *
     * @param array<string, mixed> $stored
     * @param list<string>         $offered
     * @param array<string, string> $attributes
     */
    #[DataProvider('storedChoices')]
    public function testWhatTheScriptAppliesBeforeTheFirstPaint(array $stored, array $offered, bool $speech, array $attributes, string $fontSize): void
    {
        $node = (new ExecutableFinder())->find('node');

        if (null === $node) {
            $this->markTestSkipped('Node.js is not installed.');
        }

        $harness = 'var attributes={},style={};'
            .'var document={documentElement:{setAttribute:function(n,v){attributes[n]=v},style:style}};'
            .'var localStorage={getItem:function(k){return k==="nax-settings"?'.json_encode(json_encode($stored)).':null}};'
            .'var window='.($speech ? '{speechSynthesis:{}}' : '{}').';'
            .HeadScript::build($offered)
            .'process.stdout.write(JSON.stringify({attributes:attributes,fontSize:style.fontSize||""}));';

        $file = tempnam(sys_get_temp_dir(), 'nax');
        file_put_contents($file, $harness);
        $output = (string) shell_exec(escapeshellarg($node).' '.escapeshellarg($file));
        unlink($file);

        $this->assertSame(['attributes' => $attributes, 'fontSize' => $fontSize], json_decode($output, true));
    }

    /**
     * @return iterable<string, array{array<string, mixed>, list<string>, bool, array<string, string>, string}>
     */
    public static function storedChoices(): iterable
    {
        yield 'nothing stored' => [[], Features::ALL, true, [], ''];
        yield 'switch on' => [['contrast' => true, 'links' => false], Features::ALL, true, ['data-a11y-contrast' => ''], ''];
        yield 'switch no longer offered' => [['contrast' => true, 'links' => true], ['links'], true, ['data-a11y-links' => ''], ''];
        yield 'only true counts' => [['contrast' => 'yes'], Features::ALL, true, [], ''];
        yield 'read aloud without speech output' => [['speak' => true, 'focus' => true], Features::ALL, false, ['data-a11y-focus' => ''], ''];
        yield 'read aloud with speech output' => [['speak' => true], Features::ALL, true, ['data-a11y-speak' => ''], ''];
        yield 'text size of the first version snaps to the nearest step' => [['font' => 145], Features::ALL, true, ['data-a11y-font' => '150'], 'calc(var(--nax-font-base, 100%) * 1.5)'];
        yield 'text size between steps' => [['font' => 120], Features::ALL, true, ['data-a11y-font' => '115'], 'calc(var(--nax-font-base, 100%) * 1.15)'];
        yield 'normal text size' => [['font' => 100], Features::ALL, true, [], ''];
        yield 'text size not offered' => [['font' => 150], ['contrast'], true, [], ''];
    }

    /**
     * Option "Enlarge the whole page": the stored text size zooms the content
     * (data-a11y-font-zoom, --nax-font-zoom) and leaves the root font size alone.
     */
    public function testTextSizeAsZoomOfThePage(): void
    {
        $node = (new ExecutableFinder())->find('node');

        if (null === $node) {
            $this->markTestSkipped('Node.js is not installed.');
        }

        $run = static function (bool $zoom, int $font) use ($node): array {
            $harness = 'var attributes={},style={},props={};style.setProperty=function(n,v){props[n]=v};'
                .'var document={documentElement:{setAttribute:function(n,v){attributes[n]=v},style:style}};'
                .'var localStorage={getItem:function(){return '.json_encode(json_encode(['font' => $font])).'}};var window={};'
                .HeadScript::build(Features::ALL, $zoom)
                .'process.stdout.write(JSON.stringify({attributes:attributes,fontSize:style.fontSize||"",props:props}));';
            $file = tempnam(sys_get_temp_dir(), 'nax');
            file_put_contents($file, $harness);
            $output = (string) shell_exec(escapeshellarg($node).' '.escapeshellarg($file));
            unlink($file);

            return json_decode($output, true);
        };

        $this->assertSame(['attributes' => ['data-a11y-font' => '130', 'data-a11y-font-zoom' => ''], 'fontSize' => '', 'props' => ['--nax-font-zoom' => '1.3']], $run(true, 130));
        $this->assertSame(['attributes' => ['data-a11y-font' => '130'], 'fontSize' => 'calc(var(--nax-font-base, 100%) * 1.3)', 'props' => []], $run(false, 130));
        $this->assertSame(['attributes' => [], 'fontSize' => '', 'props' => []], $run(true, 100), 'normal size: no zoom');
    }

    public function testBrokenStoredDataDoesNotStopThePage(): void
    {
        $node = (new ExecutableFinder())->find('node');

        if (null === $node) {
            $this->markTestSkipped('Node.js is not installed.');
        }

        $harness = 'var document={documentElement:{setAttribute:function(){},style:{}}};'
            .'var localStorage={getItem:function(){return "{broken"}};var window={};'
            .HeadScript::build(Features::ALL)
            .'process.stdout.write("still running");';

        $file = tempnam(sys_get_temp_dir(), 'nax');
        file_put_contents($file, $harness);
        $output = (string) shell_exec(escapeshellarg($node).' '.escapeshellarg($file).' 2>&1');
        unlink($file);

        $this->assertSame('still running', $output);
    }
}

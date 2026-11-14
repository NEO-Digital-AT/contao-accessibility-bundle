<?php

declare(strict_types=1);

namespace Neo\AccessibilityBundle\Tests\Panel;

use Neo\AccessibilityBundle\Panel\Placement;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PlacementTest extends TestCase
{
    #[DataProvider('positions')]
    public function testPosition(mixed $stored, string $position): void
    {
        $this->assertSame($position, Placement::position($stored));
    }

    /**
     * @return iterable<string, array{mixed, string}>
     */
    public static function positions(): iterable
    {
        yield 'early value left' => ['left', 'bottom-left'];
        yield 'early value right' => ['right', 'bottom-right'];
        yield 'corner' => ['top-right', 'top-right'];
        yield 'no floating button' => ['none', 'none'];
        yield 'unknown' => ['middle', 'bottom-left'];
        yield 'empty' => ['', 'bottom-left'];
        yield 'null' => [null, 'bottom-left'];
    }

    public function testLayout(): void
    {
        $this->assertSame('drawer-right', Placement::layout('drawer-right'));
        $this->assertSame('popover', Placement::layout('sidebar'));
        $this->assertSame('popover', Placement::layout(null));
    }
}

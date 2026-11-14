<?php

declare(strict_types=1);

namespace Neo\AccessibilityBundle\Panel;

/**
 * Where the floating button sits and where the panel opens. The values are
 * stored in tl_module (nax_position, nax_layout) and become CSS classes on the
 * widget (.nax--<position>, .nax--<layout>).
 */
final class Placement
{
    /** "none": no floating button; the panel is opened by {{accessibility::link}}. */
    public const POSITIONS = ['bottom-left', 'bottom-right', 'top-left', 'top-right', 'none'];

    public const DEFAULT_POSITION = 'bottom-left';

    /** "popover": next to the button; "drawer-*": full height at a side; "center": middle of the screen. */
    public const LAYOUTS = ['popover', 'drawer-left', 'drawer-right', 'center'];

    public const DEFAULT_LAYOUT = 'popover';

    /**
     * Position of a module; the early values "left" and "right" mean the bottom corners.
     */
    public static function position(mixed $value): string
    {
        $value = match ($value) {
            'left' => 'bottom-left',
            'right' => 'bottom-right',
            default => $value,
        };

        return \in_array($value, self::POSITIONS, true) ? $value : self::DEFAULT_POSITION;
    }

    public static function layout(mixed $value): string
    {
        return \in_array($value, self::LAYOUTS, true) ? $value : self::DEFAULT_LAYOUT;
    }
}

<?php

declare(strict_types=1);

namespace Neo\AccessibilityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * NEO Accessibility — a front-end module that gives visitors a panel with
 * display aids (text size, high contrast, greyscale, readable text, highlighted
 * links, large cursor, strong focus, reduced motion). The choices are stored in
 * the visitor's browser (localStorage) and applied as data-a11y-* attributes on
 * <html>; the effects ship in public/accessibility.css.
 *
 * The bundle root holds the Contao resources (contao/, config/, public/,
 * translations/), which is why getPath() points one level above src/.
 */
final class NeoAccessibilityBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}

<?php

declare(strict_types=1);

namespace Neo\AccessibilityBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Neo\AccessibilityBundle\NeoAccessibilityBundle;
use Neo\BasicBundle\NeoBasicBundle;

/**
 * Contao Manager plugin — registers the bundle. Installed via Composer, the
 * plugin is discovered through "extra.contao-manager-plugin"; no host-side
 * configuration is required.
 *
 * The bundle loads after the Basic bundle, which names the group "neo" of the
 * front-end modules that the accessibility panel belongs to.
 */
class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(NeoAccessibilityBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class, NeoBasicBundle::class]),
        ];
    }
}

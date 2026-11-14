<?php

declare(strict_types=1);

namespace Neo\AccessibilityBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Neo\AccessibilityBundle\Panel\Features;

/**
 * Offers the settings added after the first version in panel modules that
 * offered every setting of the first version.
 *
 * Such a module was left as it came ("all ticked"), so the new settings belong
 * in it too; none of them changes anything for visitors until they switch it
 * on. Read aloud included: it uses only voices installed on the visitor's
 * device unless the module allows online voices (off by default), so no text
 * leaves the device. A module with a narrower choice keeps it: someone chose
 * what visitors see there.
 *
 * Runs once per module: it only touches modules whose choice is exactly the
 * first version's and whose quick options (nax_profiles, new in the same
 * version) were never saved. Afterwards the choice is no longer the first
 * version's; and a module saved in the back end has a value in nax_profiles.
 */
final class NewSettingsMigration extends AbstractMigration
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function getName(): string
    {
        return 'NEO Accessibility: offer the new settings in panels that offered all settings';
    }

    public function shouldRun(): bool
    {
        return [] !== $this->affectedModules();
    }

    public function run(): MigrationResult
    {
        $modules = $this->affectedModules();
        $value = serialize(self::extendedChoice());

        foreach ($modules as $id) {
            $this->connection->update('tl_module', ['nax_features' => $value], ['id' => $id]);
        }

        return $this->createResult(true, \sprintf('%s: %d module(s)', $this->getName(), \count($modules)));
    }

    /**
     * The first version's settings plus every newer one, in panel order.
     *
     * @return list<string>
     */
    public static function extendedChoice(): array
    {
        return Features::ALL;
    }

    /**
     * IDs of the panel modules to update. Empty while tl_module or one of the
     * two columns does not exist yet (the schema update adds nax_profiles; the
     * migration then runs in the next round of contao:migrate).
     *
     * @return list<int>
     */
    private function affectedModules(): array
    {
        $columns = (int) $this->connection->fetchOne(
            "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tl_module' AND COLUMN_NAME IN ('type', 'nax_features', 'nax_profiles')",
        );

        if (3 !== $columns) {
            return [];
        }

        $rows = $this->connection->fetchAllKeyValue(
            "SELECT id, nax_features FROM tl_module WHERE type = 'neo_accessibility' AND nax_profiles IS NULL",
        );

        $first = Features::FIRST_VERSION;
        sort($first);

        $ids = [];

        foreach ($rows as $id => $value) {
            $chosen = array_values(array_unique(array_map('strval', StringUtil::deserialize($value, true))));
            sort($chosen);

            if ($chosen === $first) {
                $ids[] = (int) $id;
            }
        }

        return $ids;
    }
}

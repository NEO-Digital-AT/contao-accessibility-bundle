<?php

declare(strict_types=1);

namespace Neo\AccessibilityBundle\Tests\Migration;

use Doctrine\DBAL\Connection;
use Neo\AccessibilityBundle\Migration\NewSettingsMigration;
use Neo\AccessibilityBundle\Panel\Features;
use PHPUnit\Framework\TestCase;

final class NewSettingsMigrationTest extends TestCase
{
    /**
     * tl_module: id => [type, nax_features, nax_profiles]
     *
     * @var array<int, array{type: string, nax_features: string|null, nax_profiles: string|null}>
     */
    private array $modules = [];

    private int $columns = 3;

    public function testPanelsWithEveryFirstVersionSettingGetTheNewOnes(): void
    {
        $this->module(1, Features::FIRST_VERSION);
        // The same choice in another order.
        $this->module(2, array_reverse(Features::FIRST_VERSION));
        // A narrower choice someone made.
        $this->module(3, ['font', 'contrast']);
        // Saved since the quick options exist.
        $this->module(4, Features::FIRST_VERSION, serialize(['vision']));
        // Another module type.
        $this->module(5, Features::FIRST_VERSION, null, 'navigation');

        $migration = new NewSettingsMigration($this->connection());

        $this->assertTrue($migration->shouldRun());

        $result = $migration->run();

        $this->assertTrue($result->isSuccessful());
        $this->assertStringContainsString('2 module(s)', $result->getMessage());
        $this->assertSame(serialize(Features::ALL), $this->modules[1]['nax_features']);
        $this->assertSame(serialize(Features::ALL), $this->modules[2]['nax_features']);
        $this->assertSame(serialize(['font', 'contrast']), $this->modules[3]['nax_features']);
        $this->assertSame(serialize(Features::FIRST_VERSION), $this->modules[4]['nax_features']);
        $this->assertSame(serialize(Features::FIRST_VERSION), $this->modules[5]['nax_features']);
    }

    public function testRunsOnlyOnce(): void
    {
        $this->module(1, Features::FIRST_VERSION);
        $migration = new NewSettingsMigration($this->connection());

        $migration->run();

        $this->assertFalse($migration->shouldRun());
    }

    public function testWaitsForTheColumnsOfTheSchemaUpdate(): void
    {
        $this->module(1, Features::FIRST_VERSION);
        $this->columns = 2;

        $this->assertFalse((new NewSettingsMigration($this->connection()))->shouldRun());
    }

    public function testNothingToDoWithoutPanels(): void
    {
        $this->assertFalse((new NewSettingsMigration($this->connection()))->shouldRun());
    }

    public function testTheExtendedChoiceIsEverySettingInPanelOrder(): void
    {
        $this->assertSame(Features::ALL, NewSettingsMigration::extendedChoice());
        $this->assertSame([], array_diff(Features::FIRST_VERSION, NewSettingsMigration::extendedChoice()));
    }

    /**
     * @param list<string> $features
     */
    private function module(int $id, array $features, string|null $profiles = null, string $type = 'neo_accessibility'): void
    {
        $this->modules[$id] = ['type' => $type, 'nax_features' => serialize($features), 'nax_profiles' => $profiles];
    }

    /**
     * A connection that answers the migration's queries from $this->modules,
     * as MySQL would (the column check reads information_schema).
     */
    private function connection(): Connection
    {
        $connection = $this->createStub(Connection::class);

        $connection->method('fetchOne')->willReturnCallback(function (string $sql): int {
            $this->assertStringContainsString('information_schema.COLUMNS', $sql);

            return $this->columns;
        });

        $connection->method('fetchAllKeyValue')->willReturnCallback(function (string $sql): array {
            $this->assertStringContainsString("type = 'neo_accessibility' AND nax_profiles IS NULL", $sql);

            $rows = array_filter($this->modules, static fn (array $module): bool => 'neo_accessibility' === $module['type'] && null === $module['nax_profiles']);

            return array_map(static fn (array $module): string|null => $module['nax_features'], $rows);
        });

        $connection->method('update')->willReturnCallback(function (string $table, array $values, array $criteria): int {
            $this->assertSame('tl_module', $table);
            $this->modules[$criteria['id']] = $values + $this->modules[$criteria['id']];

            return 1;
        });

        return $connection;
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Command\MigrateCommand;
use App\Utils\Config;
use App\Utils\DatabaseFactory;
use flight\database\SimplePdo;
use PHPUnit\Framework\TestCase;

/**
 * Tests migration file selection and apply logic against a temp SQLite file
 * without bootstrapping the full Runway CLI.
 */
class MigrateCommandTest extends TestCase
{
    /** @var string */
    private $dbPath;

    /** @var string */
    private $migrationsDir;

    protected function setUp(): void
    {
        $this->dbPath = sys_get_temp_dir() . '/flight_skeleton_migrate_' . uniqid('', true) . '.sqlite';
        $this->migrationsDir = sys_get_temp_dir() . '/flight_skeleton_migrations_' . uniqid('', true);
        mkdir($this->migrationsDir, 0775, true);

        file_put_contents(
            $this->migrationsDir . '/20240101000000_test_table.sql',
            "CREATE TABLE IF NOT EXISTS demo (id INTEGER PRIMARY KEY, name TEXT);\n"
            . "INSERT INTO demo (name) VALUES ('alpha');\n"
        );
        file_put_contents(
            $this->migrationsDir . '/20240101000000_test_table.mysql.sql',
            "CREATE TABLE IF NOT EXISTS demo (id INT PRIMARY KEY, name VARCHAR(255));\n"
        );
        file_put_contents(
            $this->migrationsDir . '/20240102000000_other.sql',
            "CREATE TABLE IF NOT EXISTS other (id INTEGER PRIMARY KEY);\n"
        );
    }

    protected function tearDown(): void
    {
        if (is_file($this->dbPath)) {
            unlink($this->dbPath);
        }
        $files = glob($this->migrationsDir . '/*');
        if ($files) {
            foreach ($files as $f) {
                unlink($f);
            }
        }
        if (is_dir($this->migrationsDir)) {
            rmdir($this->migrationsDir);
        }
    }

    public function testListMigrationFilesFiltersByDriver(): void
    {
        $sqlite = MigrateCommand::listMigrationFiles($this->migrationsDir, 'sqlite');
        $sqliteNames = array_map('basename', $sqlite);
        $this->assertSame(
            ['20240101000000_test_table.sql', '20240102000000_other.sql'],
            $sqliteNames
        );

        $mysql = MigrateCommand::listMigrationFiles($this->migrationsDir, 'mysql');
        $mysqlNames = array_map('basename', $mysql);
        $this->assertSame(['20240101000000_test_table.mysql.sql'], $mysqlNames);

        $this->assertTrue(MigrateCommand::isMysqlMigrationName('foo.mysql.sql'));
        $this->assertFalse(MigrateCommand::isMysqlMigrationName('foo.sql'));
    }

    public function testApplyMigrationsToSqlite(): void
    {
        $config = new Config([
            'database' => [
                'driver' => 'sqlite',
                'file_path' => $this->dbPath,
            ],
        ]);

        $db = DatabaseFactory::create($config);
        $this->assertInstanceOf(SimplePdo::class, $db);

        $db->exec(
            'CREATE TABLE IF NOT EXISTS _migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE,
                applied_at TEXT NOT NULL
            )'
        );

        $files = MigrateCommand::listMigrationFiles($this->migrationsDir, 'sqlite');
        $this->assertNotEmpty($files);

        foreach ($files as $file) {
            $name = basename($file);
            $sql = file_get_contents($file);
            if ($sql === false) {
                $this->fail('Unable to read migration file: ' . $name);
            }
            $db->beginTransaction();
            try {
                foreach ($this->splitSql($sql) as $statement) {
                    $db->exec($statement);
                }
                $stmt = $db->prepare('INSERT INTO _migrations (name, applied_at) VALUES (?, ?)');
                $stmt->execute([$name, date('c')]);
                $db->commit();
            } catch (\Throwable $e) {
                $db->rollBack();
                throw $e;
            }
        }

        $count = $db->fetchField('SELECT COUNT(*) FROM demo');
        $this->assertSame(1, (int) $count);

        $applied = $db->fetchField('SELECT COUNT(*) FROM _migrations');
        $this->assertSame(2, (int) $applied);

        // Second run should not re-apply (simulate tracking check)
        $names = $db->fetchAll('SELECT name FROM _migrations');
        $this->assertCount(2, $names);
    }

    /**
     * @return array<int,string>
     */
    private function splitSql(string $sql): array
    {
        $lines = preg_split('/\R/', $sql);
        $cleaned = [];
        if ($lines !== false) {
            foreach ($lines as $line) {
                if (strpos(ltrim($line), '--') === 0) {
                    continue;
                }
                $cleaned[] = $line;
            }
        }
        $body = implode("\n", $cleaned);
        $parts = preg_split('/;\s*[\r\n]+/', $body);
        $out = [];
        if ($parts !== false) {
            foreach ($parts as $part) {
                $part = trim($part);
                if ($part !== '') {
                    $out[] = $part;
                }
            }
        }
        return $out;
    }
}

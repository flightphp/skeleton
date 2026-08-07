<?php

declare(strict_types=1);

namespace App\Command;

use App\Utils\Config;
use App\Utils\DatabaseFactory;
use App\Utils\Env;
use flight\commands\AbstractBaseCommand;
use flight\database\SimplePdo;
use PDOException;

/**
 * Apply pending SQL migrations from migrations/.
 *
 * Usage: php runway migrate
 *
 * Files (driver selected from config / .env DB_DRIVER):
 *   SQLite (default): migrations/{YYYYMMDDHHMMSS}_{description}.sql
 *   MySQL:            migrations/{YYYYMMDDHHMMSS}_{description}.mysql.sql
 *
 * Tracking table: _migrations (id, name, applied_at)
 *
 * Note: Lives under app/commands/ (lowercase) so Runway can discover it.
 * Namespace is App\Command — Runway requires the file by path, not PSR-4 alone.
 */
class MigrateCommand extends AbstractBaseCommand
{
    /**
     * @param array<string,mixed> $config From .runway-config.json
     */
    public function __construct(array $config)
    {
        parent::__construct('migrate', 'Apply pending SQL migrations from migrations/', $config);
    }

    public function execute(): void
    {
        // Command::io() returns Interactor; avoid Application::io() union for static analysis
        $io = $this->io();
        $projectRoot = getcwd();
        if ($projectRoot === false) {
            $io->error('Unable to determine project root (getcwd failed).', true);
            return;
        }

        try {
            $appConfig = $this->loadAppConfig($projectRoot);
            $db = DatabaseFactory::create($appConfig);
        } catch (\Throwable $e) {
            $io->error($e->getMessage(), true);
            return;
        }

        $migrationsDir = $projectRoot . DIRECTORY_SEPARATOR . 'migrations';
        if (!is_dir($migrationsDir)) {
            $io->error('migrations/ directory not found.', true);
            return;
        }

        $driver = strtolower((string) $appConfig->get('database.driver', 'sqlite'));
        $this->ensureMigrationsTable($db, $driver);

        $applied = $this->getAppliedNames($db);
        $files = $this->listMigrationFiles($migrationsDir, $driver);

        $pending = 0;
        foreach ($files as $file) {
            $name = basename($file);
            if (isset($applied[$name])) {
                continue;
            }

            $pending++;
            $sql = file_get_contents($file);
            if ($sql === false || trim($sql) === '') {
                $io->error("Skipping empty or unreadable migration: {$name}", true);
                continue;
            }

            $io->info("Applying {$name}...", true);

            try {
                $this->applyMigration($db, $name, $sql);
                $io->ok("  applied {$name}", true);
            } catch (PDOException $e) {
                $io->error("  failed {$name}: " . $e->getMessage(), true);
                return;
            }
        }

        if ($pending === 0) {
            $io->info('No pending migrations.', true);
            return;
        }

        $io->ok("Done. Applied {$pending} migration(s).", true);
    }

    /**
     * Pick migration files for the active driver.
     *
     * - sqlite (default): *.sql excluding *.mysql.sql
     * - mysql: *.mysql.sql only
     *
     * @return array<int,string> Absolute paths, sorted
     */
    public static function listMigrationFiles(string $migrationsDir, string $driver): array
    {
        $driver = strtolower($driver);
        $all = glob($migrationsDir . DIRECTORY_SEPARATOR . '*.sql');
        if ($all === false) {
            return [];
        }

        $files = [];
        foreach ($all as $file) {
            $name = basename($file);
            $isMysql = self::isMysqlMigrationName($name);

            if ($driver === 'mysql') {
                if ($isMysql) {
                    $files[] = $file;
                }
                continue;
            }

            // sqlite and any other non-mysql default: plain .sql only
            if (!$isMysql) {
                $files[] = $file;
            }
        }

        sort($files, SORT_STRING);
        return $files;
    }

    public static function isMysqlMigrationName(string $basename): bool
    {
        return (bool) preg_match('/\.mysql\.sql$/', $basename);
    }

    private function loadAppConfig(string $projectRoot): Config
    {
        Env::load($projectRoot . DIRECTORY_SEPARATOR . '.env');

        $configFile = $projectRoot . DIRECTORY_SEPARATOR . 'app'
            . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

        if (!is_file($configFile)) {
            throw new \RuntimeException(
                'app/config/config.php not found. Copy config_sample.php first.'
            );
        }

        $fileConfig = require $configFile;
        if (!is_array($fileConfig)) {
            throw new \RuntimeException('config.php must return an array.');
        }

        $merged = Config::mergeEnv($fileConfig, $_ENV);
        return new Config($merged);
    }

    private function ensureMigrationsTable(SimplePdo $db, string $driver): void
    {
        if ($driver === 'mysql') {
            $db->exec(
                'CREATE TABLE IF NOT EXISTS `_migrations` (
                    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(255) NOT NULL,
                    `applied_at` VARCHAR(64) NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `uq_migrations_name` (`name`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
            );
            return;
        }

        $db->exec(
            'CREATE TABLE IF NOT EXISTS _migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE,
                applied_at TEXT NOT NULL
            )'
        );
    }

    /**
     * @return array<string,true>
     */
    private function getAppliedNames(SimplePdo $db): array
    {
        $rows = $db->fetchAll('SELECT name FROM _migrations ORDER BY name ASC');
        $map = [];
        foreach ($rows as $row) {
            // SimplePdo::fetchAll returns Collection (ArrayAccess) or array rows
            $name = $row['name'] ?? null;
            if ($name !== null && $name !== '') {
                $map[(string) $name] = true;
            }
        }
        return $map;
    }

    private function applyMigration(SimplePdo $db, string $name, string $sql): void
    {
        $driver = $db->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $useTransaction = in_array($driver, ['sqlite', 'mysql', 'pgsql'], true);

        if ($useTransaction) {
            $db->beginTransaction();
        }

        try {
            // Split on semicolons at end of lines for multi-statement files
            $statements = $this->splitSql($sql);
            foreach ($statements as $statement) {
                $db->exec($statement);
            }

            $stmt = $db->prepare(
                'INSERT INTO _migrations (name, applied_at) VALUES (?, ?)'
            );
            $stmt->execute([$name, date('c')]);

            if ($useTransaction && $db->inTransaction()) {
                $db->commit();
            }
        } catch (\Throwable $e) {
            if ($useTransaction && $db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * @return array<int,string>
     */
    private function splitSql(string $sql): array
    {
        // Strip line comments
        $lines = preg_split('/\R/', $sql);
        $cleaned = [];
        if ($lines !== false) {
            foreach ($lines as $line) {
                $trim = ltrim($line);
                if (strpos($trim, '--') === 0) {
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

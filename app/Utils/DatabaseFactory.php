<?php

declare(strict_types=1);

namespace App\Utils;

use flight\database\SimplePdo;
use InvalidArgumentException;
use PDO;

/**
 * Builds a SimplePdo connection from Config.
 * Shared helper for bootstrap services and CLI (migrate).
 */
final class DatabaseFactory
{
    /**
     * Whether database is configured (driver non-empty).
     */
    public static function isEnabled(Config $config): bool
    {
        $driver = (string) $config->get('database.driver', '');
        return $driver !== '';
    }

    /**
     * Create a SimplePdo instance from config.
     *
     * @throws InvalidArgumentException When driver is empty or unsupported
     */
    public static function create(Config $config): SimplePdo
    {
        $driver = strtolower((string) $config->get('database.driver', ''));

        if ($driver === '') {
            throw new InvalidArgumentException(
                'Database is not configured. Set database.driver in config.php or DB_DRIVER in .env'
            );
        }

        $dsn = self::buildDsn($config, $driver);
        $user = $config->get('database.user');
        $password = $config->get('database.password');

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        return new SimplePdo(
            $dsn,
            $user !== null && $user !== '' ? (string) $user : null,
            $password !== null ? (string) $password : null,
            $options
        );
    }

    private static function buildDsn(Config $config, string $driver): string
    {
        if ($driver === 'sqlite') {
            $path = (string) $config->get('database.file_path', '');
            if ($path === '') {
                throw new InvalidArgumentException('database.file_path is required for sqlite');
            }

            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            return 'sqlite:' . $path;
        }

        if ($driver === 'mysql') {
            $host = (string) $config->get('database.host', 'localhost');
            $dbname = (string) $config->get('database.dbname', '');
            $charset = (string) $config->get('database.charset', 'utf8mb4');

            return 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=' . $charset;
        }

        throw new InvalidArgumentException('Unsupported database driver: ' . $driver);
    }
}

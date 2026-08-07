<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Immutable-ish configuration bag built once at bootstrap from
 * config.php defaults + .env overlay (see mergeEnv).
 *
 * Inject this class into controllers/middleware/models.
 * Do not read $_ENV outside bootstrap/env merge.
 */
final class Config
{
    /** @var array<string,mixed> */
    private $data;

    /**
     * Explicit map of environment variable name => dotted config path.
     * Env wins when the variable is set and non-empty.
     *
     * @var array<string,string>
     */
    public const ENV_MAP = [
        'APP_ENV' => 'app.env',
        'APP_DEBUG' => 'app.debug',
        'FLIGHT_BASE_URL' => 'app.base_url',
        'APP_TIMEZONE' => 'app.timezone',
        'DB_DRIVER' => 'database.driver',
        'DB_HOST' => 'database.host',
        'DB_DATABASE' => 'database.dbname',
        'DB_USERNAME' => 'database.user',
        'DB_PASSWORD' => 'database.password',
        'DB_SQLITE_PATH' => 'database.file_path',
    ];

    /**
     * @param array<string,mixed> $data Merged configuration array
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get a value by dotted key path (e.g. "database.host").
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $segments = explode('.', $key);
        $value = $this->data;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * @return array<string,mixed>
     */
    public function all(): array
    {
        return $this->data;
    }

    public function isDebug(): bool
    {
        $debug = $this->get('app.debug', false);

        if (is_bool($debug)) {
            return $debug;
        }

        if (is_string($debug)) {
            return in_array(strtolower($debug), ['1', 'true', 'yes', 'on'], true);
        }

        return (bool) $debug;
    }

    public function env(): string
    {
        return (string) $this->get('app.env', 'production');
    }

    /**
     * Application base URL with a trailing slash so templates can
     * safely join paths: {{ base_url }}posts → /myapp/posts
     */
    public function baseUrl(): string
    {
        $url = (string) $this->get('app.base_url', '/');
        if ($url === '') {
            return '/';
        }

        return rtrim($url, '/') . '/';
    }

    /**
     * Overlay mapped environment variables onto file config.
     * Env wins when the variable is set and the string is non-empty.
     *
     * @param array<string,mixed>  $fileConfig From config.php (literals only)
     * @param array<string,mixed>  $env        Typically $_ENV after loadEnv
     * @return array<string,mixed>
     */
    public static function mergeEnv(array $fileConfig, array $env): array
    {
        $merged = $fileConfig;

        foreach (self::ENV_MAP as $envKey => $configPath) {
            if (!array_key_exists($envKey, $env)) {
                continue;
            }

            $raw = $env[$envKey];
            if ($raw === null || $raw === '') {
                continue;
            }

            $value = self::castEnvValue($configPath, (string) $raw);
            self::setPath($merged, $configPath, $value);
        }

        return $merged;
    }

    /**
     * @param string $path
     * @param string $raw
     * @return mixed
     */
    private static function castEnvValue(string $path, string $raw)
    {
        if ($path === 'app.debug') {
            return in_array(strtolower($raw), ['1', 'true', 'yes', 'on'], true);
        }

        return $raw;
    }

    /**
     * @param array<string,mixed> $data
     * @param string              $path Dotted path
     * @param mixed               $value
     */
    private static function setPath(array &$data, string $path, $value): void
    {
        $segments = explode('.', $path);
        $ref = &$data;

        foreach ($segments as $i => $segment) {
            if ($i === count($segments) - 1) {
                $ref[$segment] = $value;
                return;
            }

            if (!isset($ref[$segment]) || !is_array($ref[$segment])) {
                $ref[$segment] = [];
            }
            $ref = &$ref[$segment];
        }
    }
}

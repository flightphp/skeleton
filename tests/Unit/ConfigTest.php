<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Utils\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testGetNestedKeys(): void
    {
        $config = new Config([
            'app' => ['env' => 'testing', 'debug' => true],
            'database' => ['host' => 'localhost', 'dbname' => 'app'],
        ]);

        $this->assertSame('testing', $config->get('app.env'));
        $this->assertSame('localhost', $config->get('database.host'));
        $this->assertSame('fallback', $config->get('missing.key', 'fallback'));
        $this->assertTrue($config->isDebug());
        $this->assertSame('testing', $config->env());
    }

    public function testMergeEnvWinsWhenSet(): void
    {
        $file = [
            'app' => [
                'env' => 'development',
                'debug' => true,
                'base_url' => '/',
            ],
            'database' => [
                'driver' => 'sqlite',
                'host' => 'localhost',
                'password' => '',
            ],
        ];

        $env = [
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'DB_HOST' => 'db.internal',
            'DB_PASSWORD' => 'secret',
            'IGNORED_VAR' => 'nope',
        ];

        $merged = Config::mergeEnv($file, $env);
        $config = new Config($merged);

        $this->assertSame('production', $config->env());
        $this->assertFalse($config->isDebug());
        $this->assertSame('db.internal', $config->get('database.host'));
        $this->assertSame('secret', $config->get('database.password'));
        $this->assertSame('sqlite', $config->get('database.driver'));
        $this->assertArrayNotHasKey('IGNORED_VAR', $config->all());
    }

    public function testMergeEnvIgnoresEmptyValues(): void
    {
        $file = [
            'app' => ['env' => 'development', 'debug' => true],
            'database' => ['host' => 'localhost'],
        ];

        $env = [
            'APP_ENV' => '',
            'DB_HOST' => '',
        ];

        $merged = Config::mergeEnv($file, $env);
        $config = new Config($merged);

        $this->assertSame('development', $config->env());
        $this->assertSame('localhost', $config->get('database.host'));
    }

    public function testDebugStringCasting(): void
    {
        $config = new Config(['app' => ['debug' => 'true', 'env' => 'dev']]);
        $this->assertTrue($config->isDebug());

        $config = new Config(['app' => ['debug' => '0', 'env' => 'dev']]);
        $this->assertFalse($config->isDebug());
    }

    public function testBaseUrlAlwaysHasTrailingSlash(): void
    {
        $this->assertSame('/', (new Config(['app' => ['base_url' => '/']]))->baseUrl());
        $this->assertSame('/', (new Config(['app' => ['base_url' => '']]))->baseUrl());
        $this->assertSame('/myapp/', (new Config(['app' => ['base_url' => '/myapp']]))->baseUrl());
        $this->assertSame('/myapp/', (new Config(['app' => ['base_url' => '/myapp/']]))->baseUrl());
        $this->assertSame('/', (new Config([]))->baseUrl());
    }
}

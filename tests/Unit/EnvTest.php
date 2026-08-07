<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Utils\Env;
use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase
{
    /** @var string */
    private $path;

    protected function setUp(): void
    {
        $this->path = sys_get_temp_dir() . '/flight_env_' . uniqid('', true) . '.env';
        file_put_contents(
            $this->path,
            "# comment\n"
            . "TEST_ENV_KEY=hello\n"
            . "TEST_QUOTED=\"world\"\n"
            . "export TEST_EXPORT=yes\n"
        );
    }

    protected function tearDown(): void
    {
        if (is_file($this->path)) {
            unlink($this->path);
        }
        putenv('TEST_ENV_KEY');
        putenv('TEST_QUOTED');
        putenv('TEST_EXPORT');
        unset($_ENV['TEST_ENV_KEY'], $_ENV['TEST_QUOTED'], $_ENV['TEST_EXPORT']);
    }

    public function testLoadParsesEnvFile(): void
    {
        Env::load($this->path);

        $this->assertSame('hello', $_ENV['TEST_ENV_KEY'] ?? null);
        $this->assertSame('world', $_ENV['TEST_QUOTED'] ?? null);
        $this->assertSame('yes', $_ENV['TEST_EXPORT'] ?? null);
    }
}

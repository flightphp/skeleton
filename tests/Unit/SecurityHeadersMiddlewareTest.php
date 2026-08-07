<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Middleware\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Response;
use PHPUnit\Framework\TestCase;

class SecurityHeadersMiddlewareTest extends TestCase
{
    public function testBeforeSetsSecurityHeaders(): void
    {
        $headers = [];

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['header'])
            ->getMock();

        $response->method('header')
            ->willReturnCallback(static function ($name, $value = null) use (&$headers, $response) {
                if ($value !== null) {
                    $headers[$name] = $value;
                }
                return $response;
            });

        // get() is real; response() is mapped via __call
        $app = $this->getMockBuilder(Engine::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->addMethods(['response'])
            ->getMock();

        $app->method('get')->with('csp_nonce')->willReturn('abc123nonce');
        $app->method('response')->willReturn($response);

        $middleware = new SecurityHeadersMiddleware($app);
        $middleware->before([]);

        $this->assertArrayHasKey('Content-Security-Policy', $headers);
        $this->assertStringContainsString('nonce-abc123nonce', $headers['Content-Security-Policy']);
        $this->assertSame('SAMEORIGIN', $headers['X-Frame-Options']);
        $this->assertSame('nosniff', $headers['X-Content-Type-Options']);
        $this->assertArrayHasKey('Strict-Transport-Security', $headers);
    }
}

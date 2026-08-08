<?php

declare(strict_types=1);

namespace App\Middleware;

use flight\Engine;
use Tracy\Debugger;

/**
 * Canonical middleware example — constructor injection + before().
 */
class SecurityHeadersMiddleware
{
    /** @var Engine<object> */
    private $app;

    /**
     * @param Engine<object> $app
     */
    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    /**
     * @param array<string,mixed> $params
     */
    public function before(array $params): void
    {
        $nonce = (string) $this->app->get('csp_nonce');

        // Tracy debug bar injects inline CSS in development
        $styleSrc = "'self' 'nonce-{$nonce}'";
        if (class_exists(Debugger::class) && Debugger::$showBar === true) {
            $styleSrc = "'self' 'unsafe-inline'";
        }

        $csp = "default-src 'self'; "
            . "script-src 'self' 'nonce-{$nonce}' 'strict-dynamic'; "
            . "style-src {$styleSrc}; "
            . "img-src 'self' data:;";

        $response = $this->app->response();
        $response->header('X-Frame-Options', 'SAMEORIGIN');
        $response->header('Content-Security-Policy', $csp);
        $response->header('X-XSS-Protection', '1; mode=block');
        $response->header('X-Content-Type-Options', 'nosniff');
        $response->header('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->header(
            'Strict-Transport-Security',
            'max-age=31536000; includeSubDomains; preload'
        );
        $response->header('Permissions-Policy', 'geolocation=()');
    }
}

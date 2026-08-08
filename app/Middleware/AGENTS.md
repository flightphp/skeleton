# AGENTS — Middleware (`App\Middleware`)

Read root [AGENTS.md](../../AGENTS.md) first. Security: [SECURITY.md](../../SECURITY.md).

## Purpose

Cross-cutting HTTP filters: headers, auth gates, logging, etc. Canonical example: `SecurityHeadersMiddleware`.

## Flight nuances (easy to get wrong)

1. **Class + methods, not only closures.** Prefer a class so Dice can inject `Engine`, `Config`, `Session`.
2. **Lifecycle:** implement `before(array $params): void` and/or `after(array $params): void` as needed. Route params from the matched route are in `$params`.
3. **Constructor injection** works the same as controllers — type-hint dependencies; do not use `Flight::`.
4. **Attach in routes**, not inside the middleware class:
   - Group: `$router->group('/admin', function (...) { ... }, [AuthMiddleware::class]);`
   - Or per-route via Flight’s middleware APIs if you use them — see docs.
5. **Order matters.** Group middleware runs for all nested routes. Outer group middleware runs around inner groups.
6. **Halt / redirect to stop the chain:** `$this->app->halt(403)`, `$this->app->redirect('/login')` — do not assume framework “return false” Laravel semantics unless you verify current Flight middleware docs.
7. **Response headers:** `$this->app->response()->header('Name', 'value')`.
8. **CSP nonce** is on the Engine: `$this->app->get('csp_nonce')` (set in bootstrap). Do not generate a second unrelated nonce for the same response unless you also update Twig globals.
9. **Do not** read `$_SERVER` / `$_SESSION` directly for auth if Session is available — inject `flight\Session`.
10. **Tracy in dev** may need CSP `style-src` relaxation for the debug bar (see existing security headers middleware). Do not disable all CSP globally for that.

## Pattern

```php
namespace App\Middleware;

use flight\Engine;

class ExampleMiddleware
{
    private $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    public function before(array $params): void
    {
        // gate or mutate request/response headers
    }
}
```

## Do not

- Put business/SQL logic that belongs in controllers/models here unless it is truly cross-cutting.
- Remove security headers middleware from the default group without an equivalent control (see SECURITY.md).

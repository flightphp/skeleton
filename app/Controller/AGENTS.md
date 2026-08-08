# AGENTS — Controllers (`App\Controller`)

Read root [AGENTS.md](../../AGENTS.md) first. Security: [SECURITY.md](../../SECURITY.md).

## Purpose

HTTP action classes. One public method per route action (or a small coherent set). Keep them thin: request → use services/models → render or JSON.

## Flight nuances (easy to get wrong)

1. **Inject `flight\Engine $app`**, not `Flight::`. Dice substitutes the **same** Engine as bootstrap. Do not `new Engine()`.
2. **Do not** call `Flight::render`, `Flight::json`, `Flight::redirect` here. Use `$this->app->render()`, `$this->app->json()`, `$this->app->redirect()`, `$this->app->halt()`.
3. **Request data:** `$this->app->request()` — e.g. `$this->app->request()->data->email`, `->query`, `->method`. Never `$_GET` / `$_POST` / `$_REQUEST`.
4. **Views:** `$this->app->render('template', $data)` — Twig is already mapped. Template name may omit `.twig`. Do not use Flight’s old PHP view engine API for new pages.
5. **JSON:** `$this->app->json($payload, $status)` (optional pretty-print args per core docs). Prefer this over `echo json_encode`.
6. **404 / early exit:** `$this->app->halt($code, $message)` or return after setting response. For APIs, JSON error body + status is fine.
7. **Constructor injection only** for dependencies (`Config`, `SimplePdo`, `Session`, etc.). Register shared services in `app/config/services.php`.
8. **Models:** inject `SimplePdo`, then `new Post($this->db)` (or inject a factory later). Do not invent a static `Model::find()` base class.
9. **No `$_ENV`.** Use injected `App\Utils\Config`.
10. **Routes stay in `app/config/routes.php`.** Controllers do not register their own routes.

## Pattern

```php
namespace App\Controller;

use flight\Engine;
use App\Utils\Config;

class ExampleController
{
    private $app;
    private $config;

    public function __construct(Engine $app, Config $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    public function index(): void
    {
        $this->app->render('example/index', [
            'title' => 'Example',
        ]);
    }
}
```

Wire: `$router->get('/example', [ExampleController::class, 'index']);`

## Do not

- Put SQL strings with unescaped input in the controller (use SimplePdo params / ActiveRecord).
- Start sessions with raw `session_start()` / `$_SESSION` (use injected Session).
- Add Laravel-style FormRequest / middleware attributes — this is Flight.

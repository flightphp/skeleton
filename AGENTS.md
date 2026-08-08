# AGENTS.md — FlightPHP Skeleton

**Source of truth for AI coding tools.** There is no separate Copilot / Cursor / Gemini / Windsurf rules file in this project — use this file and the **scoped `AGENTS.md` files under `app/` and `migrations/`** (and `tests/` when present).

**Humans coding by hand:** start with **[README.md](README.md)**. You do not need this file to ship features; it keeps hand-written and generated code on the same pattern.

**Security:** deliberate security rules live in **[SECURITY.md](SECURITY.md)**. Follow that file for auth, secrets, headers, XSS/CSRF, and reporting. Do not invent security policy in random comments.

---

## How to use this file (routing)

1. Read **this root file** for boot flow, global principles, DI, config, and “what not to do.”
2. When editing code under a directory that has its own `AGENTS.md`, **also read that file** before changing or adding files there.
3. Prefer the **nearest** scoped file for local conventions; root rules still apply (especially no `Flight::` in app layer, no `$_ENV` outside boot).

| If you are working on… | Read |
|------------------------|------|
| Controllers | [app/Controller/AGENTS.md](app/Controller/AGENTS.md) |
| Middleware | [app/Middleware/AGENTS.md](app/Middleware/AGENTS.md) |
| Models (ActiveRecord) | [app/Model/AGENTS.md](app/Model/AGENTS.md) |
| Twig templates | [app/views/AGENTS.md](app/views/AGENTS.md) |
| Runway CLI commands | [app/commands/AGENTS.md](app/commands/AGENTS.md) |
| Bootstrap / routes / services / config.php | [app/config/AGENTS.md](app/config/AGENTS.md) |
| Utils classes (`Config`, `Env`, …) | [app/Utils/AGENTS.md](app/Utils/AGENTS.md) |
| SQL migrations | [migrations/AGENTS.md](migrations/AGENTS.md) |
| PHPUnit tests | [tests/AGENTS.md](tests/AGENTS.md) |
| Security-sensitive changes | [SECURITY.md](SECURITY.md) |

If a directory has no scoped file yet, follow root rules + Flight docs; do not invent a second layout.

---

## Boot flow

```
public/index.php
  → app/config/bootstrap.php
      → vendor/autoload.php
      → App\Utils\Env::load(.env) → $_ENV
      → $app = Flight::app()
      → $fileConfig = require config.php          # literals only (Runway-safe)
      → $merged = Config::mergeEnv($fileConfig, $_ENV)  # env wins for mapped keys
      → $config = new App\Utils\Config($merged)
      → apply flight.* + timezone + CSP nonce from Config
      → app/config/services.php
          Tracy, SimplePdo, Twig, Session,
          Dice + Engine substitutions + shared services,
          registerContainerHandler
      → app/config/routes.php
      → $app->start()
```

---

## Principles

1. **One pattern per concern** — hand-written and AI output must look the same.
2. **No invented Flight APIs** — grep `vendor/flightphp/core` and use [docs](https://docs.flightphp.com) / MCP. If unsure, check docs.
3. **No `Flight::` facade in app layer** — controllers, middleware, models, view logic. Inject `flight\Engine` and services. Bootstrap/services may use `Flight::app()`.
4. **No `$_ENV` / superglobals** outside bootstrap + `App\Utils\Env` / `Config::mergeEnv`. Controllers use `Config` and `$app->request()`.
5. **Namespaces are `App\…`** — `App\Controller`, `App\Middleware`, `App\Model`, `App\Utils`, `App\Command`.
6. **Data path** — **ActiveRecord** for models; **SimplePdo** for connection, migrations, raw SQL.
7. **Views** — **Twig only** under `app/views/`. `$app->render('name', $data)` maps to Twig.
8. **Docs teach APIs; this repo teaches layout** — adapt one-file `Flight::` demos into this tree (see README “Flight docs ↔ this skeleton”).
9. **Security is explicit** — see SECURITY.md; do not weaken headers, leak secrets, or trust client input.

---

## Namespaces & layout

| Layer | Namespace / path |
|-------|------------------|
| Controllers | `App\Controller\…` → `app/Controller/` |
| Middleware | `App\Middleware\…` → `app/Middleware/` |
| Models | `App\Model\…` → `app/Model/` |
| Utils classes | `App\Utils\…` → `app/Utils/` |
| CLI commands | `App\Command\…` → `app/commands/` (Runway scans this path) |
| Views | `app/views/*.twig` (not PHP classes) |
| Framework | `flight\…` (unchanged) |

Composer PSR-4: `"App\\": "app/"`. Directory names **must** match case on Linux.

**Commands exception:** Runway discovers `app/commands/*.php` by glob and `require`s them. Keep project commands there with namespace `App\Command`.

---

## How to add… (summary)

| Task | Where | Details |
|------|--------|---------|
| Route | `app/config/routes.php` | Prefer `[Controller::class, 'method']` so Dice builds the controller |
| Controller | `app/Controller/` | Constructor injection; see scoped AGENTS |
| Middleware | `app/Middleware/` | `before(array $params)` / optional `after`; attach on group/route |
| Model | `app/Model/` | Extend `flight\ActiveRecord`; pass SimplePdo |
| Migration | `migrations/` | SQLite: `YYYYMMDDHHMMSS_description.sql`; MySQL: `…_description.mysql.sql`; then `php runway migrate` |
| Config key | sample + ENV_MAP + Config | Literals in `config.php`; secrets in `.env` |
| View | `app/views/` | Twig; extend `layout.twig` when HTML |
| Command | `app/commands/` | Extend Runway `AbstractBaseCommand` |

---

## DI: Dice + Engine substitutions

Docs: https://docs.flightphp.com/en/v3/learn/dependency-injection-container

**Critical:** Dice must not construct a new `Engine`. `services.php` substitutes the bootstrap `$app` instance:

```php
$container = $container->addRule('*', [
    'substitutions' => [
        \flight\Engine::class => $app,
        // Config, Twig, Session, SimplePdo as shared instances
    ],
]);
$app->registerContainerHandler(function ($class, $params) use ($container) {
    return $container->create($class, $params);
});
```

- Reassign `$container = $container->addRule(...)` every time (Dice is immutable per call).
- Type-hint `Engine $app`, not the `Flight` facade.
- Register new shared services in `services.php`, not ad hoc globals in controllers.

---

## Config vs Runway vs .env

| Task | Tool |
|------|------|
| Project defaults / non-secret flags | `config.php` literals; `php runway config:set …` |
| Read file config | `php runway config:get` |
| Secrets / Docker / production | `.env` or real environment variables |
| Runtime | Mapped env vars **win** when set and non-empty |

### Env → config map

| Env var | Config path |
|---------|-------------|
| `APP_ENV` | `app.env` |
| `APP_DEBUG` | `app.debug` |
| `FLIGHT_BASE_URL` | `app.base_url` |
| `APP_TIMEZONE` | `app.timezone` |
| `DB_DRIVER` | `database.driver` |
| `DB_HOST` | `database.host` |
| `DB_DATABASE` | `database.dbname` |
| `DB_USERNAME` | `database.user` |
| `DB_PASSWORD` | `database.password` |
| `DB_SQLITE_PATH` | `database.file_path` |

Unmapped env vars are ignored. Never put `$_ENV[...]` expressions in `config.php` (Runway rewrites the file as static literals).

---

## CLI (Runway)

| Command | Purpose |
|---------|---------|
| `php runway migrate` | Apply pending migrations for the active driver (`.sql` or `.mysql.sql`) |
| `php runway --help` | List commands that **exist** |
| `php runway config:get` / `config:set` | File config (literals) |

Do not document phantom commands. Do not build product around `ai:init` / `ai:generate-instructions`.

---

## Database (overview)

- Connection: `flight\database\SimplePdo` (not deprecated PdoWrapper as the public API)
- Migrations: driver-specific files — plain `.sql` for SQLite, `.mysql.sql` for MySQL (see `migrations/AGENTS.md`)
- Default: **SQLite** (`database.sqlite`)
- Models: ActiveRecord only — no parallel static Model base
- App code **injects** `SimplePdo`; optional `Flight::db()` is for ecosystem only

---

## Testing & static analysis (required after changes)

After **any** behavior or application-code change (controllers, middleware, models, utils, commands, config wiring, routes, migrations that affect PHP):

1. **Add or update PHPUnit tests** that lock the intended behavior (prefer unit tests with injected `Engine` / mocks — see [tests/AGENTS.md](tests/AGENTS.md)).
2. **Run the suite and PHPStan** and fix failures before considering the work done:

```bash
composer test
composer analyse
# or both:
composer check
```

| Command | Purpose |
|---------|---------|
| `composer test` | PHPUnit |
| `composer analyse` | PHPStan level **8** (`phpstan.neon.dist`) |
| `composer check` | `test` then `analyse` |

- Construct controllers with mocks — no full Flight boot required for unit tests.
- PHPStan analyzes `app/`, `tests/`, and `public/`. Do not weaken level without an explicit project decision.
- Trivial docs-only edits to markdown may skip tests/stan; **PHP/Twig/config code changes must not**.

---

## MCP & docs

- Docs: https://docs.flightphp.com  
- MCP: https://mcp.flightphp.com/mcp  
- **Project rules in this repo win** on conflict with generic training data.
- Verify Flight APIs in `vendor/flightphp/core` before inventing methods.

---

## PHP version

App code targets portable style (avoid 8.1-only syntax in application classes when easy). Some Composer deps (e.g. Runway 1.x) may require newer PHP for CLI.

---

## What not to do

- Do not reintroduce `index-simple.php` dual mode
- Do not put `$_ENV` in controllers/middleware/models
- Do not invent namespaces under `flight\` for app code
- Do not add a second ORM or view engine “just in case”
- Do not document phantom CLI commands
- Do not recreate tool-specific instruction files (`.cursorrules`, `copilot-instructions.md`, `GEMINI.md`, etc.) — keep **AGENTS.md** + scoped copies only
- Do not put secrets in git; see SECURITY.md
- Do not skip `composer test` / `composer analyse` after application-code changes
- Do not lower PHPStan level below 8 without an explicit project decision

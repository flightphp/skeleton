# Flight PHP Skeleton

Official starter for [Flight PHP](https://docs.flightphp.com) — a fast, simple, extensible micro-framework.

This repository is what you get from:

```bash
composer create-project flightphp/skeleton cool-project-name
```

It is built so **you can write every line yourself**, following one clear application pattern, and so **AI coding tools succeed** when you choose to use them. Same codebase either way.

---

## Who this is for

| You… | Start here |
|------|------------|
| Want to code the app yourself | [Quick start](#quick-start) → [How you work day to day](#how-you-work-day-to-day) → [Flight docs ↔ this skeleton](#flight-docs--this-skeleton) |
| Use any AI coding agent | Same as above, then [AI-assisted development](#ai-assisted-development), root **`AGENTS.md`**, and **`SECURITY.md`** |
| Are comparing to older Flight demos | [Flight docs ↔ this skeleton](#flight-docs--this-skeleton) |

Flight’s **framework APIs** live in the docs and in `vendor/flightphp/core`. This skeleton’s job is a **default application layout** (folders, DI, config, views, models) so you are not inventing structure on day one.

---

## Requirements

- PHP 8.1+ recommended (app code stays careful about syntax; some deps such as Runway 1.x need 8.2+)
- Composer
- `ext-json`, `ext-pdo` (`pdo_sqlite` for the default database)

---

## Create a project

```bash
composer create-project flightphp/skeleton cool-project-name
cd cool-project-name
```

That step copies `config_sample.php` → `config.php`, `.env.example` → `.env` (when present), creates cache/log dirs, and writes `.runway-config.json` if needed.

---

## Quick start

```bash
# Optional: edit .env or app/config/config.php
composer start
# → http://localhost:8000

# Sample data (posts table + ActiveRecord example)
php runway migrate
# → http://localhost:8000/posts
# → http://localhost:8000/api/posts
```

### Docker

```bash
docker compose up -d
# → http://localhost:8080
```

### Vagrant

```bash
vagrant up
# → http://localhost:8000
```

---

## Project structure

```
project-root/
├── README.md             # You are here (humans first)
├── AGENTS.md             # Root AI instructions (source of truth)
├── SECURITY.md           # Security policy (secrets, headers, reporting)
├── .env.example          # Documented env overlays (secrets / deploy)
├── public/index.php      # Web entry only
├── app/
│   ├── config/           # bootstrap, routes, services + AGENTS.md
│   ├── Utils/            # Config, Env, DatabaseFactory + AGENTS.md
│   ├── Controller/       # App\Controller\* + AGENTS.md
│   ├── Middleware/       # App\Middleware\* + AGENTS.md
│   ├── Model/            # App\Model\* + AGENTS.md
│   ├── commands/         # Runway CLI + AGENTS.md
│   ├── views/            # Twig + AGENTS.md
│   ├── cache/
│   └── log/
├── migrations/           # SQL + AGENTS.md
└── tests/                # PHPUnit + AGENTS.md
```

Namespaces are **`App\…`** (PascalCase folders: `Controller`, not `controllers`). Framework code stays **`flight\…`**.

---

## How you work day to day

You do not need an AI tool. The loop is:

1. **Route** — add a line in `app/config/routes.php`
2. **Controller** — class under `app/Controller/` with constructor injection
3. **View or JSON** — Twig under `app/views/`, or `$this->app->json(...)`
4. **Database (optional)** — migration in `migrations/`, model under `app/Model/`, inject `SimplePdo`

### Minimal controller

```php
namespace App\Controller;

use flight\Engine;

class HelloController
{
    private $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    public function index(): void
    {
        $this->app->render('welcome', [
            'message' => 'Hello from a controller',
        ]);
    }
}
```

```php
// app/config/routes.php
$router->get('/hello', [HelloController::class, 'index']);
```

Dice builds the controller and injects the **same** `Engine` instance used at boot (see `app/config/services.php`). That matches Flight’s dependency-injection and unit-testing guidance: prefer `$app` / injected services over the static `Flight::` facade in application classes.

### Configuration

Three layers:

1. **`.env`** — secrets and deploy overrides (`DB_PASSWORD`, `APP_ENV`, Docker)
2. **`app/config/config.php`** — structured **literal** defaults (safe for `runway config:set`)
3. **Bootstrap merge** — mapped env keys win when set (`App\Utils\Config::mergeEnv`)

| Task | Where |
|------|--------|
| Local defaults / non-secret flags | `config.php` or `php runway config:set …` |
| Secrets / production | `.env` (gitignored) |
| Read file config | `php runway config:get` |

Do **not** put `$_ENV[...]` expressions inside `config.php`. Runway rewrites that file as static PHP and would bake resolved values (including secrets) into the file.

Full env→config map: **`AGENTS.md`**.

### Useful commands

| Command | Purpose |
|---------|---------|
| `composer start` | PHP built-in server on port 8000 |
| `composer test` | PHPUnit |
| `composer analyse` | PHPStan level 8 |
| `composer check` | PHPUnit + PHPStan |
| `php runway migrate` | Apply migrations for active driver (`.sql` / `.mysql.sql`) |
| `php runway --help` | List CLI commands |
| `php runway config:get` / `config:set` | File config helpers |

Only rely on commands that actually appear in `php runway --help` for your install.

---

## Stack (this skeleton’s defaults)

| Concern | Choice | Why this default |
|---------|--------|------------------|
| Framework | [flightphp/core](https://docs.flightphp.com) (`Engine`, `SimplePdo`) | Long-term Flight APIs |
| DI | [Dice](https://docs.flightphp.com/en/v3/learn/dependency-injection-container) + Engine substitutions | Testable controllers; official DI pattern |
| Views | [Twig](https://twig.symfony.com/) | Wide ecosystem; `$app->render()` is mapped to Twig |
| Models | [ActiveRecord](https://docs.flightphp.com/awesome-plugins/active-record) | One model story |
| DB connection | [`SimplePdo`](https://docs.flightphp.com/en/v3/learn/simple-pdo) | Preferred over deprecated PdoWrapper |
| Sessions | [flightphp/session](https://docs.flightphp.com/awesome-plugins/session) | Injectable; avoid raw `$_SESSION` |
| CLI | [Runway](https://docs.flightphp.com/awesome-plugins/runway) | Migrations + scaffolding host |
| Debugger | Tracy (+ tracy-extensions in dev) | Error UX in development |

These are **deliberate product defaults for the official starter**, not the only way to use Flight. A micro app can still be a single file and `Flight::route()` — that path is documented in core docs / zip installs, not duplicated here.

---

## Flight docs ↔ this skeleton

Docs teach the **framework**. The skeleton fixes an **application shape** so copy-paste from tutorials does not fight the tree. When they differ, **prefer this repository’s layout for code you add under `app/`**, and use docs for method names, options, and plugins.

| Topic | Docs often show | This skeleton expects |
|-------|-----------------|------------------------|
| Entry / demo style | `Flight::route(...)`, sometimes one-file | `public/index.php` → bootstrap → `routes.php` + controllers |
| App handle | `Flight::…` static facade | Inject `flight\Engine $app` in controllers/middleware; bootstrap may still call `Flight::app()` |
| Controllers | Various namespaces / ad hoc classes | `App\Controller\…` → `app/Controller/` |
| Routing file | Inline in index or mixed | All HTTP routes in `app/config/routes.php` |
| Views | Built-in PHP views, Latte examples, etc. | **Twig only** under `app/views/`; `$app->render('name', $data)` |
| Database helper | Older **PdoWrapper** examples still around | **`SimplePdo`** (PdoWrapper is deprecated as of core 3.18) |
| Models | Raw SQL, or ActiveRecord in plugin docs | ActiveRecord under `App\Model\`; connection is SimplePdo |
| Config | Arrays, env snippets, register() | Literal `config.php` + `.env` overlay; inject `App\Utils\Config` |
| DI | Optional / several containers | Dice wired in `services.php` with **Engine substitutions** |
| Testing | Construct controller with `new Engine()` + mocks | Same idea; see `tests/Unit/` and the [unit testing guide](https://docs.flightphp.com/en/v3/guides/unit-testing) |

**Reading docs without fighting the skeleton**

1. Learn the API from docs (`request()`, `json()`, `route` patterns, middleware `before`, ActiveRecord methods, SimplePdo helpers).
2. Place new code in this tree (`Controller`, `Middleware`, `Model`, `views`, `routes.php`, `services.php`).
3. Prefer constructor injection over new static `Flight::` calls inside app classes.
4. If a doc example uses `Flight::db()` or `Flight::render()`, the equivalent here is usually injected `SimplePdo` / `$this->app->render()` (render is already mapped to Twig).

**Where docs and skeleton already agree**

Flight’s own [unit testing guide](https://docs.flightphp.com/en/v3/guides/unit-testing) steers away from `Flight::` globals toward `Engine` injection and DI — the same stance this skeleton takes for `app/` code. Short facade examples in learn pages remain valid for quick experiments; they are not the house style for this starter.

**Docs site updates**

Install / structure pages on docs.flightphp.com should stay in sync with this README (especially `App\` namespaces and Twig/SimplePdo defaults) whenever the skeleton ships a breaking layout change. Until then, **this README is the source of truth for create-project layout**.

---

## AI-assisted development (optional)

Nothing in the runtime requires an AI tool. There is **no create-project question** about which assistant you use.

This repo standardizes on the open **`AGENTS.md`** convention only (no separate Copilot / Cursor / Gemini / Windsurf rule files):

| File | Role |
|------|------|
| **[AGENTS.md](AGENTS.md)** | Root rules + **routing table** to scoped files |
| **`app/**/AGENTS.md`**, **`migrations/AGENTS.md`**, **`tests/AGENTS.md`** | Light, area-specific tips (controllers, Twig, Runway, …) loaded when working in that tree |
| **[SECURITY.md](SECURITY.md)** | Secrets, headers, XSS/SQL, reporting — keep security deliberate and separate |

If you use an AI assistant:

1. Point it at root **`AGENTS.md`** (and let it follow links to scoped files when editing those folders).
2. Prefer [docs.flightphp.com](https://docs.flightphp.com) and MCP `https://mcp.flightphp.com/mcp`.
3. Verify APIs under `vendor/flightphp/core` — do not invent Flight methods.
4. **Project AGENTS / SECURITY win** over generic training data.
5. After application-code changes: add/update unit tests and run **`composer check`** (PHPUnit + PHPStan level 8).

Hand-written and AI-generated code should look the same: one controller style, one config path, one view layer.

---

## First customization checklist

1. Add a route in `app/config/routes.php`
2. Add `app/Controller/YourController.php` (constructor injection)
3. Add a Twig template under `app/views/` **or** return JSON from the controller
4. For DB: SQL file in `migrations/` (`.sql` for SQLite, `.mysql.sql` for MySQL), `php runway migrate`, model in `app/Model/`, inject `SimplePdo`
5. After code changes: add/update tests, then run `composer check` (PHPUnit + PHPStan level 8)

---

## License

MIT — see [LICENSE](LICENSE).

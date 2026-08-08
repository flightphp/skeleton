# AGENTS — Runway commands (`app/commands`)

Read root [AGENTS.md](../../AGENTS.md) first. Security: [SECURITY.md](../../SECURITY.md).

## Purpose

CLI commands for this project, discovered by **Runway**. Example: `MigrateCommand` → `php runway migrate`.

## Critical layout nuance

| Fact | Detail |
|------|--------|
| **Disk path** | `app/commands/*.php` (lowercase `commands`) — Runway globs this when `app_root` is `app/` |
| **Namespace** | `App\Command` (singular Command) |
| **PSR-4** | Root composer maps `App\` → `app/`; this folder is also in **classmap** because path case ≠ `Command/` |
| **Loading** | Runway `require`s the file and reads `namespace` from the source — do not rely only on autoload for discovery |

Do **not** move commands to `app/Command/` without updating Runway discovery (`.runway-config.json` / `final_paths`).

## Flight / Runway nuances

1. **Extend** `flight\commands\AbstractBaseCommand` (from runway package).
2. **Constructor:** `public function __construct(array $config)` where `$config` is Runway’s config merge (includes `runway` keys from config.php / `.runway-config.json`) — **not** automatically `App\Utils\Config`.
3. **Register the command name** in `parent::__construct('migrate', 'Description', $config)`.
4. **I/O:** `$this->app()->io()` for info/ok/error (adhocore CLI).
5. **App config / DB:** load like `MigrateCommand` does — `Env::load`, `require config.php`, `Config::mergeEnv`, `DatabaseFactory::create`. Do not assume web bootstrap already ran with Dice.
6. **`services.php` early-return:** Runway requires `services.php` with **array** `$config` and no Engine. Full Tracy/Dice wiring is skipped — your command must be self-contained for config/DB.
7. **Only document commands that exist** after `php runway --help`.
8. **Do not** productize around `ai:init` / `ai:generate-instructions` for this skeleton’s story.

## Pattern

```php
namespace App\Command;

use flight\commands\AbstractBaseCommand;

class ExampleCommand extends AbstractBaseCommand
{
    public function __construct(array $config)
    {
        parent::__construct('example', 'Short description', $config);
    }

    public function execute(): void
    {
        $io = $this->app()->io();
        $io->info('Hello', true);
    }
}
```

## Do not

- Put long-running web request logic here without a clear CLI UX.
- Write secrets into config files from commands by default (prefer `.env`).

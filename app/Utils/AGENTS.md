# AGENTS — Utils classes (`App\Utils`)

Read root [AGENTS.md](../../AGENTS.md) first. Security: [SECURITY.md](../../SECURITY.md). Wiring files: [../config/AGENTS.md](../config/AGENTS.md).

**Why Utils?** Flight docs put app-specific classes under something like `app/UTILS` (not under `config/`). This folder holds those classes. Using `Utils` (not `Config`) avoids a case-only clash with `app/config/` on Windows/macOS.

## Classes

| Class | Role |
|-------|------|
| `Config` | Immutable-ish bag: `get('a.b')`, `all()`, `isDebug()`, `env()`, `baseUrl()`, `mergeEnv()` |
| `Env` | Minimal `.env` loader → `putenv` / `$_ENV` (bootstrap only) |
| `DatabaseFactory` | Build `SimplePdo` from Config (web + CLI) |

## Nuances

1. **Only bootstrap / Env / mergeEnv / CLI entrypoints should touch `$_ENV`.** Controllers inject `Config`.
2. **`ENV_MAP`** is the allowlist of env → dotted config paths. Unmapped env vars are ignored on purpose (no magic dump of all env into config).
3. **Adding a setting:** (1) default in `config_sample.php`, (2) optional env name in `.env.example` + `ENV_MAP`, (3) read via `Config::get`.
4. **`mergeEnv`:** env wins when the variable is set and **non-empty**. Empty string does not override file defaults.
5. **`isDebug()`** accepts bool or common string forms (`true`/`false`/`1`/`0`).
6. **DatabaseFactory** throws if driver empty/unsupported — commands should catch and print a clear CLI error.
7. Keep these classes free of Flight facades and HTTP concerns.

## Do not

- Turn `Config` into a service locator for random objects.
- Parse `.env` inside controllers “just once.”
- Reintroduce `app/Config/` next to `app/config/` (Windows case-insensitive FS collision).

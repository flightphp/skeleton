# AGENTS — Tests (`tests/`)

Read root [AGENTS.md](../../AGENTS.md) first.

## Purpose

PHPUnit tests that lock skeleton behavior.

After application-code changes (see root [AGENTS.md](../AGENTS.md) **Testing & static analysis**):

```bash
composer test
composer analyse   # PHPStan level 8
# or: composer check
```

## Flight nuances (easy to get wrong)

1. **Prefer unit tests** that construct controllers/middleware with a **mocked or real `Engine`**, not a full HTTP boot through `public/index.php`.
2. **`render`, `json`, `response`, `request`** on Engine are often **mapped / magic (`__call`)**, not always real PHP methods. With PHPUnit mocks use `addMethods(['render'])` (or similar) for magic methods; use `onlyMethods` for real methods like `get` / `set`.
3. **Do not use `Flight::` statics** in tests if you can inject Engine — matches production app code.
4. **No real external services** (SMTP, paid APIs). Mock them.
5. **SQLite temp files** are fine for migration/DB helper tests; clean up in `tearDown`.
6. **Config tests** should cover `mergeEnv` (env wins, empty ignored) — this is security-adjacent for deploy overlays.
7. **Do not** assert on Tracy HTML or full layout chrome unless necessary; assert behavior and key output.

## Layout

- `tests/Unit/` — default for fast tests
- Mirror class names: `HomeControllerTest` for `HomeController`

## Do not

- Require a running web server for unit tests.
- Depend on developer’s personal `.env` secrets; construct Config arrays in the test.

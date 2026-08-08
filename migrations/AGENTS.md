# AGENTS — Migrations

Read root [AGENTS.md](../../AGENTS.md) first. Security: [SECURITY.md](../../SECURITY.md).

## Purpose

Schema (and rare seed) changes as **timestamped SQL files**, applied by `php runway migrate`.

Driver comes from `database.driver` in config (overridable by `DB_DRIVER` in `.env`). The migrator applies **only** files for that driver.

## Conventions

1. **Filename by driver:**
   - **SQLite (default):** `YYYYMMDDHHMMSS_description.sql`
   - **MySQL:** `YYYYMMDDHHMMSS_description.mysql.sql`
   - Prefer the same timestamp prefix for a paired change so humans can match SQLite ↔ MySQL files.
2. **Directory:** project root `migrations/` only.
3. **Tracking table:** `_migrations` (`name`, `applied_at`) — DDL is driver-specific inside `MigrateCommand`.
4. **Idempotency:** command skips already-applied names; still write SQL that is safe to reason about (`CREATE TABLE IF NOT EXISTS` where appropriate).
5. **Do not put MySQL SQL in plain `.sql` files** (or SQLite SQL in `*.mysql.sql`). Glob would match both; the command filters by suffix.
6. **Multi-statement:** migrate splits on statement boundaries; keep statements clear; line `--` comments are stripped.
7. **App models** must match tables after migrate (e.g. `posts` ↔ `App\Model\Post`).
8. **Do not** put secrets or production data dumps in migrations committed to git.

## Workflow

```bash
# SQLite (default): add migrations/20260315120000_add_widgets.sql
# MySQL:            add migrations/20260315120000_add_widgets.mysql.sql
php runway migrate
```

## Do not

- Run arbitrary user input as SQL through the migrator.
- Invent a second migration tool (Phinx, Doctrine Migrations) unless the project consciously adopts it and removes this one.
- Edit an already-applied migration that has shipped to others — add a new file instead.
- Rely on “mostly portable” SQL shared across drivers — ship paired files instead.

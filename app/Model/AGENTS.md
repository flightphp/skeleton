# AGENTS — Models (`App\Model`)

Read root [AGENTS.md](../../AGENTS.md) first. Security: [SECURITY.md](../../SECURITY.md).

## Purpose

Database entities via **flightphp/active-record** only. One table (or clear mapping) per class. Example: `Post`.

## Flight / ActiveRecord nuances (easy to get wrong)

1. **Extend `flight\ActiveRecord`**, not Eloquent, not a custom static base.
2. **Constructor receives the DB connection** (PDO / SimplePdo-compatible):

   ```php
   public function __construct($databaseConnection)
   {
       parent::__construct($databaseConnection, 'posts');
   }
   ```

3. **Controllers inject `SimplePdo`**, then `new Post($this->db)`. Do not hide a global PDO inside the model.
4. **Table name** is the second constructor argument (or options array) — keep it explicit.
5. **Document columns** with `@property` PHPDoc for IDEs and agents (`@property int $id`, etc.).
6. **Find / save:** `$model->find($id)`, `$model->findAll()`, `$model->save()`, `$model->insert()`, `$model->update()`, `$model->delete()`. Prefer library helpers (`eq`, `like`, `orderBy`) over string-concatenated SQL.
7. **`isHydrated()`** — check after `find()` before trusting properties (see `PostController`).
8. **Dirty data / mass assign:** use `copyFrom` / `dirty` carefully; never mass-assign untrusted request arrays without allowlisting fields.
9. **Relationships** — define `$relations` per ActiveRecord docs; do not invent Laravel-style `hasMany()` method APIs unless they exist in this library.
10. **Schema changes** belong in `migrations/*.sql`, not in model constructors.
11. **Raw SQL** for reports/migrations: inject `SimplePdo` in the controller/command — models stay ActiveRecord-centric.

Docs: https://docs.flightphp.com/awesome-plugins/active-record

## Do not

- Create a parallel `app/Models` + Eloquent story.
- Use deprecated `PdoWrapper` as the documented connection type (use `SimplePdo`).
- Interpolate user input into `where("id = {$id}")`.

# AGENTS — App config wiring (`app/config`)

Read root [AGENTS.md](../../AGENTS.md) first. Security: [SECURITY.md](../../SECURITY.md). Config **classes** live in `app/Utils/` — see [../Utils/AGENTS.md](../Utils/AGENTS.md).

## Files

| File | Role |
|------|------|
| `bootstrap.php` | Load env, merge config, create Config, set flight.*, require services + routes, `$app->start()` |
| `config_sample.php` | **Literal** defaults committed to git (template for create-project) |
| `config.php` | Local copy (gitignored) — literals only |
| `services.php` | Tracy, SimplePdo, Twig, Session, Dice + container handler |
| `routes.php` | All HTTP routes |

## Nuances (easy to get wrong)

1. **`config.php` must return a plain array of literals.** No `$_ENV['x'] ??` in that file — `runway config:set` rewrites the return array and would bake resolved values (including secrets) into disk.
2. **Env overlay happens in bootstrap** via `Config::mergeEnv` + `App\Utils\Env::load('.env')`. Env wins for mapped keys when set and non-empty.
3. **Side effects** (timezone, `flight.*`, CSP nonce) belong in **bootstrap**, not inside the returned config array.
4. **`services.php` dual load:**
   - Web: `$app` is Engine, `$config` is `App\Utils\Config` → full wiring.
   - Runway CLI: `$config` is **array**, often **no** `$app` → **return early** (do not call `$config->isDebug()` on an array).
5. **Dice:** always reassign `$container = $container->addRule(...)`. Always substitute `Engine::class => $app`.
6. **Twig render map:** `$app->map('render', ...)` so controllers share one view path.
7. **Routes:** use `[Class::class, 'method']` so the container builds controllers. Closures are OK for tiny demos but break the “one pattern” rule for real features.
8. **New shared service:** add construct/substitution in `services.php`, then type-hint in controllers — do not `$app->set('foo', ...)` as a global bag for app logic.
9. **New config key:** update `config_sample.php`, local `config.php` if needed, `.env.example` + `Config::ENV_MAP` if overridable by env.

## Do not

- Reintroduce dual simple/robust front controllers.
- Register database as deprecated PdoWrapper for new code.
- Put route definitions in controllers.

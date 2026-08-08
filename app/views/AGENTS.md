# AGENTS — Views (Twig)

Read root [AGENTS.md](../../AGENTS.md) first. Security: [SECURITY.md](../../SECURITY.md).

## Purpose

HTML (and fragments) only. **Twig** under `app/views/`. No PHP view files for new UI.

## Nuances (easy to get wrong)

1. **Engine:** Twig is registered in `services.php`. Controllers call `$app->render('name', $data)` which maps to Twig (`.twig` optional in the name).
2. **Layout:** extend `layout.twig` for full pages:

   ```twig
   {% extends 'layout.twig' %}
   {% block title %}…{% endblock %}
   {% block body %}…{% endblock %}
   ```

3. **Globals available:** `csp_nonce`, `app_env`, `base_url` (from services). Prefer these over hardcoding hosts.
4. **Escaping:** Twig escapes by default. **Do not** use `|raw` unless content is trusted and you accept XSS risk (see SECURITY.md).
5. **No business logic:** no DB calls, no `$_ENV`, no Flight facades in templates. Pass data from the controller.
6. **Links:** `{{ base_url }}posts` — Twig global is always normalized with a trailing slash (`Config::baseUrl()`), so `/` or `/myapp` both join correctly.
7. **CSP:** if you add inline `<script>` or `<style>`, attach `nonce="{{ csp_nonce }}"` and keep `SecurityHeadersMiddleware` in mind.
8. **Cache:** production uses `app/cache/twig`; debug disables cache. Do not commit compiled cache artifacts.
9. **Partials:** use `{% include %}` / `{% embed %}`; keep names clear (`posts/_row.twig`).
10. **JSON APIs** do not use Twig — controllers return `$app->json(...)`.

## Do not

- Add Latte, Blade, or raw PHP templates as a second system.
- Put secrets or env dumps in templates for “debugging” on shared environments.
- Disable autoescape globally.

# Security Policy — FlightPHP Skeleton

Security-related expectations for this application. **AI tools and humans** should treat this as authoritative for secrets, headers, input handling, and reporting. Root [AGENTS.md](AGENTS.md) points here so security stays deliberate and separate from general coding style.

---

## Reporting a vulnerability

If you believe you found a security issue in **this skeleton template** (not a site built from it by someone else):

1. Prefer a **private** report to the maintainers (e.g. GitHub Security Advisory on the skeleton repository, or the contact listed in the package metadata / org security policy).
2. Do **not** open a public issue with exploit details until a fix is available or maintainers confirm disclosure is OK.
3. Include: affected version/commit, reproduction steps, impact, and any suggested fix.

For apps **you** deploy from this skeleton, you own runtime security (hosting, TLS, backups, dependency updates).

---

## Secrets and configuration

| Do | Do not |
|----|--------|
| Put secrets in **`.env`** (gitignored) or the real environment | Commit passwords, API keys, session encryption keys, or private tokens |
| Keep `config.php` defaults empty for passwords | Use `runway config:set` to write real production secrets into a file you commit |
| Use `App\Utils\Config` after bootstrap merge | Read `$_ENV` / `getenv` inside controllers, middleware, models, or Twig |
| Put `$_ENV[...]` expressions only in the env loader / merge map | Put `$_ENV` expressions inside `config.php` (Runway rewrites them to literals and can bake secrets into the file) |

`.env` and `app/config/config.php` are gitignored for local copies; **`config_sample.php` and `.env.example` must never contain real credentials**.

---

## Web root and public files

- Only **`public/`** should be the web server document root.
- Do not place secrets, `.env`, SQLite DB files, or `app/` under a publicly served path.
- Default SQLite file lives at project root (`database.sqlite`) — ensure the server cannot serve the project root as static files.

---

## HTTP security headers

This skeleton ships `App\Middleware\SecurityHeadersMiddleware` with:

- `Content-Security-Policy` (nonce for scripts; see CSP below)
- `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`
- `Strict-Transport-Security` (meaningful only over HTTPS)
- `Permissions-Policy`

When adding pages with third-party scripts, styles, or iframes, **update CSP deliberately** — do not set `script-src *` or disable the middleware “to make it work” without a tighter alternative.

### CSP nonce

- Bootstrap generates `csp_nonce` per request and stores it on the Engine.
- Twig gets `csp_nonce` as a global; use it on inline `<script>` / `<style>` if you add any.
- Prefer external assets under `public/` with `'self'` over large inline blocks.

---

## Input, output, and XSS

- Read input via **`$app->request()`** (query, data, cookies) — not `$_GET` / `$_POST` / `$_COOKIE` in app code.
- **Twig auto-escapes** by default — do not use `|raw` / unescaped output unless the content is trusted and you understand the risk.
- For JSON APIs, do not reflect unsanitized input into HTML elsewhere without escaping.
- Validate and authorize **server-side**; never trust client-only checks.

---

## Database and SQL

- Use **prepared statements** / SimplePdo helpers with bound parameters.
- ActiveRecord condition helpers (`eq`, `like`, etc.) — do not interpolate untrusted strings into raw `where()` SQL.
- Migrations are developer-controlled SQL; do not run user-supplied SQL as migrations.

---

## Sessions and authentication

- Prefer **`flightphp/session`** (already wired) over raw `$_SESSION`.
- When you add login: regenerate session id after privilege change; use HTTPS in production; set cookie flags appropriately for your deploy.
- Do not store passwords in plain text; use `password_hash` / `password_verify` (or a maintained auth library).
- CSRF protection is **not** shipped by default in this minimal skeleton — if you add cookie-session form POSTs that change state, add CSRF (or equivalent) and document it; see Flight security docs.

---

## Dependencies and debug mode

- Keep Composer dependencies updated; review advisories for `flightphp/*`, Twig, Tracy, etc.
- **`app.debug` / Tracy** must be **off in production** (`APP_DEBUG=false` / non-development env). Debug bars and stack traces leak internals.
- Do not enable Tracy “show bar” on public production hosts.

---

## What AI tools must not do

- Invent “temporary” hardcoded admin passwords or API keys in source.
- Disable security middleware or CSP without replacing with an equal or stronger control.
- Log full session cookies, passwords, or card data.
- Add file-upload endpoints without type/size checks and storage outside the web root (or with safe serving).
- Assume Flight facades or Laravel-style auth exist — verify in vendor/docs.

---

## Related docs

- Flight security: https://docs.flightphp.com/en/v3/learn/security  
- Project conventions: [AGENTS.md](AGENTS.md)  
- Human setup: [README.md](README.md)

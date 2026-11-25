# AGENTS.md — Bartek

## Shape & routing
- Vanilla PHP 8.2 / MySQL MVC. Composer only provides PHPMailer; there is no framework and no autoloader for `app/` classes.
- Root `index.php` is the sole front controller: Apache serves the repo root and `.htaccess` rewrites everything except `public/` assets to it (Docker does the same on :80). `public/public_index.php` is just a 1-line shim to root; README's `public/index.php` does not exist.
- Routes live in a manual `$rutas` map in `index.php`: full-path match first, then first URL segment; action defaults to `index`, hyphens in actions become camelCase, and the third segment is passed as one argument only when present. New controller/model/view requires adding `require_once` lines **and** a route entry in `index.php`.
- `menu-publico` bypasses the router and requires `public/publico.php` directly; its drink catalog and prices are hardcoded there, not read from `MenuModel`/`inventario`.
- `BaseController::render('module/view', $datos, $layout)` loads `app/views/<view>.php` inside `app/views/layouts/<layout>.php`; `cssModulo` comes from the view's first segment and auto-loads `public/css/dashboard-<modulo>.css` if it exists. `app/views/ayuda.php/index.php` is an intentional directory name used by `AyudaController` — don't "fix" it without changing the render call.

## Setup & environment
- DB comes from `getenv()` (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`) with defaults `localhost`, `3306`, `bartek_db`, `root`, empty. Nothing loads `.env` despite `.gitignore` and config comments — set env vars in Apache/PHP-FPM or edit config locally.
- Mail uses `SMTP_HOST`, `SMTP_USERNAME`, `SMTP_PASSWORD`, `SMTP_PORT`, `SMTP_SECURE`; password recovery/contact need them. `APP_ENV` defaults to `development` (errors displayed); set `production` explicitly to hide them.
- Import `sql/bartek_db_1.7.sql` into an empty `bartek_db` (full dump, includes `configuracion_puntos`/`recompensas_puntos`); `1.6` is older and there is no migration tool. README's SQL versions and file tree are stale — trust `index.php`, `sql/`, and `config/`.
- Local XAMPP runs the app in a subfolder (`/bartek`). `BASE_URL` is derived from `SCRIPT_NAME` in `config/app.php`; build links with `BASE_URL`, never hardcoded `/`.
- Table count comes from `TOTAL_MESAS` in `config/app.php` (default 10), used by `VentaController`, not just the view markup.

## Conventions & verification
- Roles are `dueno` and `empleado`: call `requerirAutenticacion()` then `requerirRol(...)`. CSRF tokens are keyed by form name (`generarTokenCSRF('venta')` ⇄ `validarTokenCSRF($token, 'venta')`); the key must match exactly.
- Use `BaseController` helpers `post()`/`get()`/`entero()` (they sanitize with `htmlspecialchars`) plus `redirigir()`, `json()`, and `flashMensaje()`/`obtenerFlash()` instead of ad-hoc response handling.
- No tests, linter, formatter, build, or CI config exist. Verify PHP edits with `php -l <file>` (PHP 8.2 on PATH; `C:\xampp\php\php.exe` works too). `composer install --no-dev --optimize-autoloader` only refreshes PHPMailer.

## Web server gotchas
- Root `.htaccess` blocks direct access to `app/`, `config/`, `setup/`, `sql/` and sensitive file extensions; `public/` is the only asset-serving directory.
- `public/api/*.php` are standalone JSON endpoints that bootstrap only `config/database.php` — no session, helpers, or router are available there.

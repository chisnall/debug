# Copilot instructions

## Project overview

- This is a PHP web debugger (`chisnall/debug`) using a small application framework under `app/`.
- PHP compatibility is defined by `composer.json` as `^7.0`; do not introduce PHP 8-only syntax or APIs.
- Composer autoloading maps the `App\\` namespace to `app/`. Shared function files are loaded from `app/Core/functions.php` and `src/helpers.php`.

## Repository layout

- `app/Core/`: framework primitives such as the application, request/response, router, session, views, logging, and error handling.
- `app/Controllers/`: HTTP controllers.
- `app/Services/`: application services and domain operations.
- `app/Exception/`: application and framework exceptions.
- `app/Views/`: PHP templates; keep presentation logic here rather than in controllers.
- `app/Routes/`: route definitions and other excluded routing configuration.
- `public/`: web entry point and static assets.
- `src/`: additional procedural helpers.
- `tests/`: PHPUnit tests, organized to mirror the code under test.
- `config.php`: runtime configuration, including filesystem paths, page settings, and debug options.

## Development workflow

- **NO COMMANDS CAN BE RUN.** The development environment is remote and inaccessible from this session; do not run `composer install`, `vendor/bin/phpunit`, or any other shell/CLI command, and do not ask to run one — assume execution is impossible.
- Make all changes by reasoning through the code and existing conventions alone, without executing anything to verify.
- Install dependencies with `composer install` when the existing `vendor/` directory is unavailable (for reference only; this cannot actually be executed here).
- Run the test suite with `vendor/bin/phpunit --configuration phpunit.xml` (for reference only; this cannot actually be executed here).
- PHPUnit is pinned to 5.7.27; keep tests compatible with that version and with the project PHP target.
- The test bootstrap is `tests/helpers.php`; the suite stops on the first error or failure and writes coverage to `tests/coverage/`.
- Since tests cannot be run, be extra careful to manually trace logic and mentally verify behavior before finishing.

## Implementation conventions

- Follow the existing PSR-4 namespaces, class naming, and directory structure.
- Reuse the existing `Request`, `Response`, `Router`, `Controller`, `View`, exception, and configuration helpers instead of adding parallel mechanisms.
- Preserve the current exception and error-handler flow; surface failures explicitly rather than silently falling back.
- Keep route definitions and view files out of the application logic and respect the PHPUnit whitelist exclusions.
- Treat values in `config.php` as deployment-specific. Do not commit real credentials, tokens, host paths, or environment-specific secrets.
- Add or update tests for behavior changes, especially for framework classes and request/response handling.
- Keep changes narrowly scoped and avoid modifying generated or dependency files under `vendor/`.

## Change validation

- Check `git diff` before finishing and ensure only intended files changed.
- No commands can be run in this environment, so PHPUnit tests cannot be executed here; review changes carefully by reading the code instead.
- Do not commit `tests/coverage/` or other generated artifacts unless explicitly requested.

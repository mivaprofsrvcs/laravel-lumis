# Lumis for Laravel — Agent & Contributor Guide

Guidance for making safe, consistent changes to the **`mvps/laravel-lumis`** package
("Lumis for Laravel"). Read this before adding features, fixing bugs, or updating tests
and documentation. The aim is changes that match the existing code, stay backward
compatible, and pass the quality gates on the first try.

> **Single source of truth.** `CLAUDE.md` and `AGENTS.md` are kept byte-for-byte
> identical. When you change one, mirror the change to the other in the same commit.

## Quick reference

Common commands (defined in `composer.json` → `scripts`):

| Task | Command |
| --- | --- |
| Run the test suite (Pest) | `composer test` |
| Static analysis (PHPStan, level 8) | `composer phpstan` |
| Check formatting (Pint, dry run) | `composer pint` |
| PHPStan + Pint together | `composer lint` |
| Auto-fix formatting | `vendor/bin/pint` |

Run the narrowest check that covers your change, then `composer lint` and `composer test`
before finalizing. CI runs Pest, PHPStan, and Pint and must stay green.

**Dependencies are not locked in git** — `composer.lock` is `.gitignore`d. Use
`composer update` locally to install/refresh; CI resolves the latest versions matching
`composer.json` on every run, so the code must work against the newest allowed releases
of every dependency.

## What this package is

Lumis for Laravel is a clean integration layer around the **Miva JSON API**:

- Configuration-driven store connections (`config/miva.php`, env-driven via `MM_*` variables)
- Connection managers (`MivaApiManager`, `MivaStoreManager`) built on `Illuminate\Support\Manager`
- Container bindings and package bootstrapping (service providers in `src/Providers`)
- Stable, public entry points (facades in `src/Facades`)
- An install command (`lumis:install`) for publishing config and printing setup hints

It is backend-only: it provides store configuration, API authentication, connection
management, and request helpers. It must not assume an application structure beyond what
Testbench provides in tests.

## Architecture & code map

```
src/
  Console/InstallCommand.php      # `lumis:install` — publishes config, prints .env + next steps
  Facades/MivaApi.php             # Facade → MivaApiManager (Miva API access)
  Facades/Store.php               # Facade → MivaStoreManager (store URLs / auth)
  Providers/
    LumisServiceProvider.php      # merges + publishes config (`lumis-config` tag), registers command
    MivaApiServiceProvider.php    # binds MivaApiManager, ApiClientService, raw Miva Client
    StoreServiceProvider.php      # binds MivaStoreManager, StoreService
  MivaApiManager.php              # connection(?name) → ApiClientService; builds the Miva client
  MivaStoreManager.php            # connection(?name) → StoreService
  Services/
    ApiClientService.php          # wraps pdeans\Miva\Api\Client; listLoadQuery(), sendRequest()
    StoreService.php              # final readonly value object: store code/url/path/auth helpers
config/miva.php                   # `default` connection name + `connections` map (api + store config)
tests/                            # Pest + Orchestra Testbench (flat layout)
```

Two parallel stacks, each wired the same way (**provider → manager → service → facade**):

- **API stack:** `MivaApi` facade → `MivaApiManager` → `ApiClientService` (wraps `pdeans\Miva\Api\Client`).
- **Store stack:** `Store` facade → `MivaStoreManager` → `StoreService`.

Resolution flow: a manager reads `miva.connections.{name}` from config (default name from
`miva.default`), constructs the service for that connection, and caches it as a driver.
`ApiClientService` proxies unknown method calls straight to the underlying Miva client via
`__call`, so the client's full builder API is available through the service and the
`MivaApi` facade. Providers and the `MivaApi` / `Store` aliases are auto-registered through
Laravel package discovery (`composer.json` → `extra.laravel`).

## Core stack & version support

You are expected to be an expert in modern PHP and Laravel package development.

- **PHP:** `^8.3` (see `composer.json`).
- **Laravel:** `illuminate/support` `^10 || ^11 || ^12 || ^13`. Do not use APIs unavailable
  in the **lowest** supported version (Laravel 10).
- **Miva client:** `pdeans/miva-api` `^3.0` — the primary Miva JSON API dependency.
- **Autoloading:** PSR-4 — `MVPS\Lumis\` → `src/`, `Tests\` → `tests/`.
- **Standards:** PSR-12 formatting (Pint); PHPStan level 8.

Testing / tooling (dev dependencies): `orchestra/testbench` `^10.8 || ^11.0`
(Testbench 10 → Laravel 12, Testbench 11 → Laravel 13), `pestphp/pest` `^4`,
`phpstan/phpstan` `^2`, `laravel/pint` `^1`.

> The Miva client uses **Guzzle 7.10+** as its HTTP transport. Do not change the HTTP
> client implementation or its transport layer unless explicitly instructed. Apply all
> request customization through Guzzle request options (the `api.http_client` connection key).

## Public API & stability

Treat the following as public API and keep it stable unless a breaking change is explicitly
requested:

- **Facades:** `MVPS\Lumis\Facades\MivaApi`, `MVPS\Lumis\Facades\Store`
- **Managers:** `MVPS\Lumis\MivaApiManager`, `MVPS\Lumis\MivaStoreManager`
- **Services:** `MVPS\Lumis\Services\ApiClientService`, `MVPS\Lumis\Services\StoreService`
- **Config + env:** keys in `config/miva.php` and the documented `MM_*` environment variables

## Conventions & architecture rules

**Follow existing structure first.**

- Preserve the layout under `src/` (`Console`, `Facades`, `Providers`, `Services`, plus the
  `*Manager` classes at the `src/` root).
- Reuse existing patterns before introducing new ones — check sibling files in the same
  directory for structure, naming, and PHPDoc style.
- Do not add new top-level directories under `src/` without approval. If you need a new
  layer (for example `DTOs` or `Tasks`), propose it first and justify why it is needed.

**Container bindings & providers.**

- Keep providers focused on container bindings and bootstrapping — no business logic.
- Bind shared services with `singleton()` using explicit class bindings.
- Use `DeferrableProvider` where appropriate (the manager/service providers already do;
  `LumisServiceProvider` is intentionally non-deferred because it publishes config and
  registers the command).

**Managers & connections.**

- `*Manager` classes extend `Illuminate\Support\Manager` and expose
  `connection(?string $name = null)` as the main access method.
- Keep connection resolution configuration-driven via `config('miva...')`. Do not read
  environment variables directly in runtime code.
- When adding connection options, update `config/miva.php` and keep defaults sensible and
  backward compatible.

**Services.**

- Keep services small, testable, and injectable.
- `ApiClientService` wraps `pdeans\Miva\Api\Client` and adds higher-level helpers (for
  example `listLoadQuery()`, `sendRequest()`); other calls are proxied to the client via
  `__call`.
- `StoreService` is a `final readonly` value object for store URL and auth-header
  generation — preserve its predictable behavior and string normalization (trimming and
  slash handling).

**Facades.**

- Keep facades thin; they point at container bindings via `getFacadeAccessor()`.
- When adding methods intended for facade use, ensure the underlying service is
  container-resolvable and update the facade `@method` PHPDoc annotations for IDE support.

## PHP standards

- **Types & signatures:** explicit parameter and return types for all new and modified
  methods. Prefer modern features already used here (typed properties, `readonly`, named
  arguments, `fn` closures).
- **`strict_types`:** new PHP files start with `declare(strict_types=1);`. Adding it to an
  existing file is potentially breaking — only do so when the task calls for it and tests
  confirm it is safe.
- **PHPDoc:** use it where PHP types cannot express intent — array shapes (e.g.
  `array{username:string,password:string}|array{}`), external library structures, and
  facade `@method` annotations. Avoid inline comments unless the logic is genuinely
  non-obvious.
- **Exceptions:** prefer specific exceptions (e.g. `InvalidArgumentException`) for
  misconfiguration; keep messages actionable and consistent across related classes (see the
  managers' "connection not configured" messages).

## Laravel package rules

- **Do it the Laravel way:** use the service container and dependency injection; prefer
  injecting services over pulling from the container. Use `config()` at runtime — never call
  `env()` outside config files.
- **Config publishing:** config lives in `config/miva.php` and is published via the
  `lumis-config` tag. If you change published assets (paths, tags, provider names), update
  `InstallCommand` and the tests that assert publishing behavior.
- **Console commands:** keep commands non-interactive by default and safe to run repeatedly.
  Update `tests/Console/*` when changing command output, options, or side effects.

## Testing (Pest + Orchestra Testbench)

- Tests use **Pest** under **Orchestra Testbench** and live flat in `tests/` (no
  `Feature/` / `Unit/` split).
- Prefer integration-style tests that exercise the public surface — container bindings,
  managers, services, facades, and commands.
- Keep tests deterministic; no external network calls.
- Package providers are registered in `tests/TestCase.php`, which also defines the `miva`
  test config (two connections: `default` and `store02`). Shared helpers live in
  `tests/Support/`.
- Any behavior change must include a test that fails before the change and passes after, or
  an update to existing tests that reflects the new intended behavior. Do not remove or
  weaken existing tests without explicit instruction.

## Tooling & quality gates

- **Pint:** PSR-12 preset (`pint.json`). If `composer pint` fails, run `vendor/bin/pint` to
  apply fixes, then re-check.
- **PHPStan:** level 8 over `src` and `tests` (`phpstan.neon.dist`). Fix the root cause
  (better types, narrower unions, explicit shapes) rather than suppressing. Do not add ignore
  rules or a baseline unless explicitly instructed.

## Making changes — decision & change-management rules

Apply these in order when deciding how to implement something:

1. Follow existing patterns and naming in this repo.
2. Choose clarity over cleverness.
3. Keep behavior predictable and avoid hidden magic.
4. Keep changes minimal and tightly scoped to the request.
5. Preserve backward compatibility where reasonably possible.

Change management:

- Do not remove features, helpers, methods, config keys, or tests unless explicitly requested.
- Avoid breaking changes. If one is required, call it out clearly and update documentation
  and tests accordingly.
- Do not add new runtime dependencies, CI tooling, or dev tooling without explicit instruction.

## Documentation

- Update `README.md` when you change public API, installation steps, configuration, or
  user-facing behavior. Add a `CHANGELOG.md` entry under `[Unreleased]` for notable changes
  (match the tone of existing entries).
- Keep documentation concise, accurate, and aligned with the repo's tone.
- Do not add new documentation files unless explicitly requested.
- This guidance is duplicated in `CLAUDE.md` and `AGENTS.md` — update both together.

# Lumis for Laravel Agent Guidelines

These guidelines are for making safe, consistent changes to the `mvps/laravel-lumis` package. Follow them closely when adding features, fixing bugs, or updating tests and documentation.


=== foundation rules ===

## Package purpose

Lumis for Laravel is a Laravel package that provides a clean integration layer around the Miva JSON API, including:

- Configuration-driven store connections (`config/miva.php`)
- Connection managers (`MivaApiManager`, `MivaStoreManager`)
- Container bindings and package bootstrapping (service providers in `src/Providers`)
- Public, stable entry points (facades in `src/Facades`)
- A package install command (`src/Console/InstallCommand.php`) for publishing config and showing setup hints

## Core stack and constraints

You are expected to be an expert in modern PHP and Laravel package development.

- PHP: 8.3+ (`composer.json`)
- Laravel support: `illuminate/support` `^10 || ^11 || ^12` (do not use APIs unavailable to supported versions)
- Miva client: `pdeans/miva-api` (this is the primary Miva JSON API dependency)
- Standards: PSR-4 autoloading and PSR-aligned formatting (Pint)
- Autoloading: PSR-4 `MVPS\\Lumis\\` mapped to `src/`

Note: The Miva client uses Guzzle 7.10.0+ as the underlying HTTP transport. Do not change the HTTP client implementation or modify its transport layer unless explicitly instructed. All request customization must be applied through Guzzle request options.

## Public API and stability

Assume the following are public API and should remain stable unless explicitly asked to introduce a breaking change:

- Facades: `MVPS\\Lumis\\Facades\\MivaApi`, `MVPS\\Lumis\\Facades\\Store`
- Managers: `MVPS\\Lumis\\MivaApiManager`, `MVPS\\Lumis\\MivaStoreManager`
- Services: `MVPS\\Lumis\\Services\\ApiClientService`, `MVPS\\Lumis\\Services\\StoreService`
- Configuration keys in `config/miva.php` and the documented `MM_*` environment variables


=== conventions and architecture rules ===

## Follow existing structure first

- Preserve the existing directory layout under `src/` (`Console`, `Facades`, `Providers`, `Services`, plus manager classes at `src/*Manager.php`).
- Reuse existing patterns before introducing new ones. Check sibling files in the same directory and follow their structure, naming, and PHPDoc style.
- Do not introduce new top-level directories under `src/` without explicit approval. If you need a new layer (for example `DTOs` or `Tasks`), propose it first and justify why it is needed.

## Container bindings and providers

- Keep service providers focused on container bindings and package bootstrapping. Do not move business logic into providers.
- Prefer explicit class bindings using `singleton()` for shared services.
- Use `DeferrableProvider` when appropriate (existing providers already follow this pattern for managers/services).

## Managers and connections

- `*Manager` classes extend `Illuminate\Support\Manager` and expose `connection(?string $name = null)` as the main access method.
- Keep connection resolution configuration-driven via `config('miva...')`. Do not read environment variables directly in runtime code.
- When adding new connection options, update `config/miva.php` and keep defaults sensible and backward compatible.

## Services

- Services should be small, testable, and injectable.
- `ApiClientService` is a wrapper around `pdeans\\Miva\\Api\\Client` and provides higher-level helpers (for example `listLoadQuery()`).
- `StoreService` is a small value object style service used for store URL and auth header generation. Preserve its predictable behavior and string normalization.

## Facades

- Facades should remain thin and point at container bindings (see `getFacadeAccessor()`).
- If you add methods that are intended to be used via facades, ensure the underlying service is resolvable from the container and update facade PHPDoc `@method` annotations when useful for IDE support.


=== agent behavior and decision rules ===

## Decision rules (apply in order)

1. Follow existing patterns and naming in this repo.
2. Choose clarity over cleverness.
3. Keep behavior predictable and avoid hidden magic.
4. Keep changes minimal and tightly scoped to the request.
5. Preserve backward compatibility where reasonably possible.

## Change management

- Do not remove features, helpers, methods, config keys, or tests unless explicitly requested.
- Avoid breaking changes. If a breaking change is required, call it out clearly and update documentation and tests accordingly.
- Do not add new runtime dependencies, CI tooling, or dev tooling without explicit instruction.


=== php rules ===

## Types and signatures

- Use strict typing practices: explicit parameter types and explicit return types for all new and modified methods.
- Prefer modern PHP features already used in the repo (typed properties, `readonly` where appropriate, named arguments, `fn` closures).

## `strict_types`

- New PHP files should start with `declare(strict_types=1);`.
- If you add `strict_types` to an existing file, treat it as a potentially breaking change and only do so when the change request calls for it and tests confirm it is safe.

## PHPDoc

- Use PHPDoc when it adds information that PHP types cannot express, especially for:
  - Array shapes (example: `array{username:string,password:string}|array{}`)
  - Mixed or external library structures
  - Facade `@method` annotations for IDE support
- Avoid inline comments inside code unless the logic is genuinely non-obvious.

## Exceptions and messages

- Prefer specific exceptions (for example `InvalidArgumentException`) for misconfiguration.
- Keep exception messages actionable and consistent across related classes.


=== laravel package rules ===

## Do things the Laravel way

- Use the service container and dependency injection. Prefer injecting services over pulling from the container.
- Use configuration via `config()` calls. Do not call `env()` outside of config files.
- Respect package boundaries: the package should not assume an application structure beyond what Testbench provides in tests.

## Config publishing

- Package config lives in `config/miva.php` and is published via the `lumis-config` tag.
- If you change published assets (config paths, tags, provider names), update the install command and tests that assert publishing behavior.

## Console commands

- Keep console commands non-interactive by default and safe to run multiple times.
- Update `tests/Console/*` when changing command output, options, or side effects.


=== tooling and quality rules ===

## Required checks

Prefer running the narrowest check that covers your changes before finalizing.

- Tests: `composer test` (runs Pest)
- Style check: `composer pint` (runs `pint --test`)
- Static analysis: `composer phpstan`
- Convenience: `composer lint` (runs phpstan and pint checks)

## Pint (formatting)

- This repo uses Pint for PSR-aligned formatting checks.
- If `composer pint` fails, run `vendor/bin/pint` to apply fixes, then rerun `composer pint`.

## PHPStan

- Do not suppress PHPStan issues casually. Prefer fixing the root cause by improving types, narrowing unions, and making shapes explicit.
- Aim for PHPStan strictness equivalent to level 8 (or higher) for new and modified code.
- Avoid adding ignore rules or baselines unless explicitly instructed.


=== pest and testing rules ===

## Test approach

- Tests are written with Pest and run under Orchestra Testbench.
- Prefer integration-style tests that exercise the package through its public surface (service container bindings, managers, services, facades, and commands).
- Keep tests deterministic and avoid external network calls.

## Test conventions in this repo

- Tests live directly under `tests/` (not split into `Feature/` and `Unit/` here).
- Shared helpers live in `tests/Support` (see `tests/Support/TestHelpers.php`).
- Package providers are registered in `tests/TestCase.php`.
- Do not remove or weaken existing tests without explicit instruction.

## When changing behavior

- Any change that affects behavior should include:
  - A test that fails before the change and passes after, or
  - Updates to existing tests that correctly reflect the new intended behavior


=== documentation rules ===

## Updating existing docs

- Update `README.md` when you change public API, installation steps, configuration, or behavior that users rely on.
- Keep documentation concise and accurate, and align with the repo tone.

## Creating new docs

- Do not add new documentation files unless explicitly requested.

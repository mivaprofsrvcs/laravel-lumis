# Release Notes

## [Unreleased](https://github.com/mivaprofsrvcs/laravel-lumis/compare/2.0.0...HEAD)

## [v2.0.0](https://github.com/mivaprofsrvcs/laravel-lumis/compare/1.0.0...2.0.0) - 2025-12-12

- chore: bump `pdeans/miva-api` dependency to `^3.0` and expose new client options (`timeout`, `binary_encoding`, `range`, `ssh_auth`, `http_client`)
- docs: add configuration guidance for new Miva client options and update `README.md`
- tests: expand coverage for invalid connections, client swapping via facade, install command output, and `StoreService` path normalization
- build: align PHPStan/Pint annotations and linting fixes for new client surface
- build: add Pint/PHPStan tooling

## v1.0.0 - 2025-11-07

- feat: initial Lumis for Laravel release with Miva API/Store managers, service providers, and facades
- feat: add `ApiClientService::listLoadQuery()` helpers for filters, sorting, count/offset, and on-demand columns
- feat: publishable `config/miva.php` with env-driven connection settings and `verify_ssl` support
- feat: add `lumis:install` console command with next steps and `.env` guidance
- build: add Pest test suite and GitHub Actions workflow

# ClickChin Landing & Component Builder API

## Overview

This repository contains the landing/component-builder subsystem of ClickChin. It is the Laravel API for storing reusable components and JSON landing configurations. The companion [Next.js frontend](https://github.com/SamanAziziSiyan/clickchin-landing-frontend) provides the editor.

## Context

This is the ClickChin landing/component-builder subsystem. The separate company generator, export pipeline, and deployment system are outside this repository.

## Architecture

`routes/api.php` exposes component, landing, upload, and authentication routes. Controllers in `app/Http/Controllers/Api` use Eloquent models and migrations. The companion Next.js client sends editor state to this API.

## Technology Stack

PHP 8.1+, Laravel 10, Composer, Eloquent, MySQL/MariaDB.

## Key Features

Component and landing CRUD, JSON landing data, user registration/login endpoints, and image upload.

## My Contribution

The inspected backend history contains 50 Saman-alias commits out of 50. This supports substantial direct backend implementation, not ownership of the whole ClickChin product.

## Collaboration

Built in the Panjere Studio team. The companion client has significant Solaiman Naderi work; project and design ownership remain shared.

## Repository Scope

Only the Laravel application was extracted from `landing-backend/landing`. Docker environment, original history, vendor, runtime data, and company deployment files are absent.

## Setup

From the repository root: `composer install`; copy `.env.example` to `.env`; configure a local database; run `php artisan key:generate`, `php artisan migrate`, then `php artisan serve`.

Inspect `routes/api.php` for `/api/v1/components`, `/api/v1/landings`, and auth endpoints. The companion client supplies the editor UI.

## Configuration

Use only local credentials in untracked `.env`. Set `CORS_ALLOWED_ORIGINS` and `SANCTUM_STATEFUL_DOMAINS` for the local client as needed.

## Testing

Run `php artisan test --compact` (10 tests, 47 assertions passed). PHP syntax and Laravel route/config checks should be rerun after any change. See [API.md](API.md) for request shapes and status codes.

## Screenshots / Demo

No approved screenshot or public demo is included. Use synthetic data and rights-cleared visuals for a future demo.

## Security

The public export requires Sanctum authentication and checks ownership for landing and component CRUD. The server assigns owner IDs. The checked-in dependency constraints still require a security upgrade/re-audit before this snapshot should be treated as production-ready. In particular, the pinned Laravel/Mediable dependency family should be reviewed against current security advisories before operational use. Historical database credentials in the original repository require rotation if used.

## Limitations

No full-site generation, WordPress/Laravel output, or deployment pipeline is present. This export has not been proven against a clean local database.

## Project Status

Public source snapshot of the ClickChin landing/component-builder API. The dependency advisories listed above and a fresh database smoke test remain open for operational use.

## License

No repository-wide first-party license has been selected. Public visibility alone does not grant reuse rights. Dependency licenses remain separate.

## GitHub metadata

**Description:** Laravel API for ClickChin’s landing and component builder subsystem.

**Topics:** laravel, php, api, landing-page-builder, eloquent

**Subtitle/tagline:** The data and API layer for a collaborative landing editor.

**Suggested pinned-profile description:** Laravel component and landing API with JSON editor state; a scoped part of the ClickChin platform.

# Task manager

A small Laravel + Vue 3 app for managing tasks grouped by project.

- Create, rename and delete tasks.
- Reorder tasks by drag and drop. Priority updates automatically: #1 at the top, #2 below it, and so on.
- Create and delete projects, and pick a project from a dropdown to see only its tasks.
- Data is stored in MySQL.

## Requirements

Docker with Compose. PHP 8.5, Composer, Node and MySQL 8.4 all run in containers (Laravel Sail).

## Setup and run

```bash
./run.sh
```

This installs dependencies, starts the containers, runs migrations, seeds sample data and builds the frontend.
The app is then available at http://localhost (change the port with `APP_PORT` in `.env`).
Running `./run.sh` again is safe.

Useful commands:

```bash
./vendor/bin/sail npm run dev    # frontend dev server with hot reload
./vendor/bin/sail artisan test   # run the test suite
./vendor/bin/sail down           # stop the containers
```

## Deployment

1. Install dependencies: `composer install --no-dev --optimize-autoloader` and `npm ci && npm run build`.
2. Set `APP_ENV=production`, `APP_DEBUG=false` and the database credentials in `.env`, then run `php artisan key:generate` if no key is set.
3. Run `php artisan migrate`.
4. Cache config: `php artisan config:cache route:cache view:cache`.
5. Point the web server's document root at `public/`.

## Structure

- `src/` holds the backend, split into modules (`Project`, `Task`, `Shared`), each with Domain, Application, Infrastructure and Presentation layers.
- `resources/js/` holds the Vue 3 frontend, which talks to the JSON API under `/api`.
- `tests/` has unit tests for the domain and feature tests for the handlers and API.

Design decisions are recorded in [docs/architecture-decisions.md](docs/architecture-decisions.md).
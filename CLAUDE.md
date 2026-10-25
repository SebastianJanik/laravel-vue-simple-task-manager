# CLAUDE.md

Guidance for Claude Code when working in this repository.

## Project

A very simple task management web application (recruitment task). Keep it small, readable and idiomatic.

**Stack:** PHP >= 8.3 (Sail container runs 8.5), Laravel 13, MySQL 8.4, a Vue 3 SPA (Vite, vue-router, vuedraggable) on top of a JSON API (originally Blade + Alpine.js).

**Functional scope:**

- Create a task (name, priority, timestamps).
- Edit a task.
- Delete a task.
- Reorder tasks by drag and drop in the browser. Priority is derived from position: #1 at the top, #2 below, and so on. Persist the new order after every drop.
- Tasks are stored in a MySQL table.
- Tasks belong to a project. A dropdown selects a project, and only that project's tasks are shown.

## Project creation and setup

The app started from the stock Laravel skeleton (`composer create-project laravel/laravel`) and was then reshaped:

- `composer.json` maps the `App\` namespace to `src/` (not `app/`); there is no `app/` directory. Tests use `Tests\` → `tests/`.
- `bootstrap/app.php` is deliberately minimal (health route + empty middleware/exception hooks). `bootstrap/providers.php` registers `SharedServiceProvider`, `ProjectServiceProvider` and `TaskServiceProvider`; a new module must be added there.
- The runtime is Laravel Sail (`compose.yaml`: PHP 8.5 app container + MySQL 8.4). Local PHP/Node/MySQL are not required.
- `.env.example` is the template (MySQL host `mysql`, database `laravel`, user `sail`, `APP_LOCALE=en`).

**Start the app (fresh clone):** `./run.sh`. It copies `.env`, installs Composer dependencies in a throwaway container, starts Sail, generates `APP_KEY`, migrates, seeds sample data (idempotent) and builds the frontend. The app is at http://localhost (`APP_PORT` in `.env`).

Without Docker, `composer setup` runs install, `.env` copy, `key:generate`, `migrate --force`, `npm install` and `npm run build`; point `DB_*` at a local MySQL and run `php artisan serve`.

### Commands

```bash
./vendor/bin/sail up -d / down            # containers
./vendor/bin/sail artisan test            # tests (or `php artisan test` / `composer test` locally)
./vendor/bin/sail artisan migrate         # migrations
./vendor/bin/sail npm run dev             # Vite dev server with hot reload (writes public/hot; run.sh removes a stale one)
./vendor/bin/sail npm run build           # production assets
./vendor/bin/sail npm run lint            # ESLint on resources/js
./vendor/bin/sail npm run format          # Prettier on resources/js
./vendor/bin/pint                         # PHP code style (rules: laravel preset + declare_strict_types + final_class)
```

## Repository layout (beyond `src/`)

- `src/<Module>/<Module>ServiceProvider.php` sits at the module root; it binds ports to adapters (`$singletons`) and loads routes.
- Module HTTP API routes live in `Presentation/Routes/api.php` and are loaded with `Shared\Presentation\ApiRoutes::load()` (adds the `api` middleware and `/api` prefix). The SPA catch-all lives in `Shared/Presentation/Routes/web.php`.
- `SharedServiceProvider` maps `NotFound` → 404 and other `DomainError` → 422 JSON, translated through `Shared\Presentation\DomainErrorMessage`.
- Application layer is CQRS-style: `Command/` (command + handler), `Query/` (handler), `ReadModel/` (port + view DTO). The read-model adapters live in `Infrastructure/ReadModels/`.
- A module may declare a port that another module implements (e.g. `Project\Domain\ProjectTasksRepository` is answered by `Task\Infrastructure\Repositories\ProjectTasksRepository`, bound in `TaskServiceProvider`).
- `database/migrations`, `database/factories` (factories target the Eloquent models in `Infrastructure/Models`), `database/seeders`.
- `lang/en/errors.php` (domain error codes), `lang/en/app.php`; `resources/js/i18n/` for frontend strings.
- Frontend (`resources/js/`): `app.js`, `router.js` (routes `/` projects, `/projects/:projectId` tasks), `api.js` (`apiFetch` for `/api`), `pages/`, `components/`, `composables/`. Built with Vite + Tailwind 4; the only Blade view is `resources/views/app.blade.php`.
- `phpunit.xml` has `Unit` and `Feature` suites and limits coverage `<source>` to `Domain` and `Application` directories (Infrastructure and Presentation are excluded).

## Architecture: modules first, four layers inside each

All application code lives under `src/`, split by **module**, then by layer:

```
src/
├── Shared/           # Kernel used by every module. Must not depend on Project or Task.
│   ├── Domain/       # Uuid, Clock, DomainError, NotFound
│   ├── Application/  # TransactionManager
│   ├── Infrastructure/
│   └── Presentation/ # web routes
├── Project/
│   ├── Domain/
│   ├── Application/
│   ├── Infrastructure/
│   └── Presentation/ # HTTP, module routes
└── Task/
    ├── Domain/
    ├── Application/
    ├── Infrastructure/
    └── Presentation/
```

Each module owns its service provider (`ProjectServiceProvider`, `TaskServiceProvider`, `SharedServiceProvider`). The provider binds that module's ports to adapters and registers that module's routes and HTTP mappings. Do not register module HTTP routes or domain exception responses from `bootstrap/app.php`.

### Dependency rule

Dependencies point inwards only, **inside a module**:

```
Presentation ──► Application ──► Domain ◄── Infrastructure
```

Modules may depend on `Shared` and on another module's **Domain** (or Application read-model ports when a use case must check existence). They must not reach into another module's Infrastructure or Presentation.

- `Domain` is organised in `Aggregate/`, `ValueObject/` and `Exception/`; ports stay at the root. `Infrastructure` is organised in `Models/`, `Repositories/` and `ReadModels/`.
- `Domain` depends on nothing (no framework classes, no `Illuminate\*`).
- `Application` depends on `Domain` only. It talks to persistence through repository **interfaces** defined in `Domain`.
- `Infrastructure` implements those interfaces (Eloquent lives here and only here) and is bound in the module's service provider.
- `Presentation` calls `Application` use cases. Controllers stay thin: validate (Form Request), call a handler, return a response. No business logic and no Eloquent queries in controllers.
- Routes live in the module (`Presentation/Routes`) and are loaded from the module service provider.

### Where business rules live

- Priority/position rules (reordering, keeping priorities contiguous and starting at 1, appending new tasks at the bottom) belong in `Domain`, not in controllers, Eloquent models or JavaScript.
- Use value objects for concepts with rules (e.g. `TaskName`, `Priority`), and domain exceptions for invalid states.
- Prefer `final` classes, `readonly` properties, constructor promotion, enums and strict types (`declare(strict_types=1);`).
- Aggregates and entities keep their state `private` and expose a getter (`id()`, `name()`) only where a caller needs it, so state changes go through methods that guard the invariants.

### Laravel way, within the layers

Use Laravel's conventions wherever they do not break the dependency rule: migrations, Form Requests, route model binding or explicit IDs, resourceful routes, service provider bindings, Blade components, Pint for code style. When Laravel's defaults and the layering conflict, the layering wins.

## DDD layering rules (details for the architecture above)

### Dependency direction

Infrastructure → Application → Domain. Dependencies always point inward.
The Domain layer never depends on Application or Infrastructure.

### Domain layer: no framework, minimal libraries

- No framework classes (`Illuminate\*`, Eloquent, facades, `Str`, `now()`, helpers like `app()`/`config()`).
- Third-party libraries are allowed only if they are small, stable and stateless: pure functions or value types,
  with no I/O, no hidden or global state, and no lifecycle (nothing to boot, configure or tear down).
- Value Objects validate themselves in their classes and throw a domain exception on invalid input.

### Ports and adapters

- If the domain needs something from the outside world (persistence, time generation, external services),
  define an interface (port) in Domain, e.g. `OrderRepository`, `Clock`.
- Implement the interface (adapter) in Infrastructure, e.g. `Repositories\OrderRepository` (no `Eloquent` prefix; the technology is visible in the namespace), `SystemClock`.
- Bind port → adapter in that **module's** service provider. Application tests use the real repositories; only the clock (and similar nondeterminism) is fixed.
- Purely application-level interfaces (e.g. `Mailer`, `EventBus`) may live in Application.

### Shared kernel

- `Shared/` holds only generic building blocks used by more than one module: base VOs (`Uuid`), `Clock`, `TransactionManager`, `DomainError`.
- `Shared` must not depend on any specific module. Modules depend on `Shared`, never the reverse.
- Do not create `Utils`/`Helpers`/`Common`. If something has no clear home, rethink where it belongs.

### Identifiers

- Each aggregate has a typed ID (`OrderId`, `CustomerId`) extending the base `Uuid` VO. Do not pass raw `Uuid`/string IDs between layers.
- IDs are created in the Application layer with `TaskId::generate()` (UUID v7) and passed into the aggregate. Aggregates do not generate their own IDs.
- `Shared\Domain\ValueObject\Uuid` is the only file allowed to import `Ramsey\Uuid\*` (tests excepted). Each ID is `final readonly class XxxId extends Uuid {}`; Eloquent models cast ID columns with `UuidCast`.

## Multilingual

The app must stay multilingual. No user-facing text is hard-coded, in PHP or in Vue: all user-facing text goes through translations. English is the only shipped locale for now.

- Domain errors carry no message. `DomainError` holds an `errorCode` (the translation key), a `context` for the placeholders and an optional `previous`; Presentation translates them from `lang/{locale}/errors.php`.
- Every new domain error, validation message or UI string gets an entry in every shipped locale (currently `en`); the frontend keeps its strings in `resources/js/i18n/`.
- The locale comes from the `Accept-Language` header. Domain and Application never know about languages.

## Testing policy

| Layer          | Tests          | Coverage target |
|----------------|----------------|-----------------|
| Domain         | Required, unit | **100%**        |
| Application    | Required, feature (real repositories + test database) | **100%** |
| Infrastructure | **Not tested** | n/a             |
| Presentation   | Not mandated; keep controllers thin | n/a |

- Domain tests are pure unit tests: no database, no framework container.
- Application tests (`tests/Feature/<Module>/Application/`) run the handlers against the real repositories and the test database (`RefreshDatabase`). Do not write fake/in-memory repositories.
- Infrastructure is deliberately excluded from tests and from coverage reports (configure the exclusion in `phpunit.xml`).
- Run the suite and coverage before finishing any change:
  ```bash
  php artisan test
  php artisan test --coverage --min=100   # scoped to Domain + Application
  ```

## Code style

- Code documents itself: precise names, small methods, value objects instead of primitives.
- No extensive comments and no docblocks that restate the signature. Add a comment only when the *why* is not obvious from the code (a non-trivial invariant, a workaround, a deliberate trade-off).
- Follow PSR-12 via Laravel Pint: `./vendor/bin/pint`.
- Static typing everywhere: parameter, return and property types.

## Working agreements for Claude

1. Read this file before starting; keep it in sync with the code.
2. Make small, focused changes. Do not add features or dependencies beyond the scope above.
3. Write the domain and its tests first, then the use cases and their tests, then infrastructure and presentation.
4. Never put business logic in controllers, Blade views or JavaScript.
5. Reorder must be a single transactional operation persisted in one request (send the full ordered list of IDs), not one request per task.
6. Before finishing: Pint is clean, tests pass, coverage is 100% for Domain and Application, and the setup steps (`./run.sh`) work on a fresh clone.

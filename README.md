# PHP Refactoring Tasks

A collection of small PHP refactoring exercises, each with the original "before" code, a refactored "after" version and PHPUnit tests for both.

## Context

Learning project built in April and May 2025 to practise refactoring legacy-style PHP code towards SOLID principles, design patterns and testable code.

## Features

- `task1`: order handling; item logic moved into an `OrderItem` class, flag-based search replaced by a `findItem` method, no array references.
- `task2`: shipping cost calculation; `if-else` chain replaced by the Strategy pattern (`ShippingType` interface, one class per shipping type, `ShippingStrategy` enum), so new types do not require changes in `ShippingCalculator`.
- `task3`: invoice; arrays replaced by an `InvoiceItem` class with types and validation (`InvoiceItemValidator`), total calculation delegated to `InvoiceItem::calculateTotal()`.
- `task4`: user management; split into `User` model, `UserRepository`, `Validator` / `UserValidator` and `Notification` / `Mailer`, with `UserManager` as a thin facade.
- `task5` to `task7`: task descriptions and "before" code are prepared, refactored solutions are not written yet (placeholders).
- `example`: template for a new task.

## Tech stack

- PHP 8.1 (Docker image `php:8.1-apache`)
- PHPUnit 10.5
- MySQL 8.0 (available in Docker Compose)
- Composer, Docker Compose

## Architecture

Each task lives in `src/refactors/<task>/`:

- `before/`: original code
- `after/`: refactored code
- `tests/`: `ClassBeforeTest.php` and `ClassAfterTest.php`
- `phpunit.xml` and a `README.md` with the task and test command

`public/index.php` runs the `before` and `after` versions of a selected task in the browser.

## Getting started

```bash
cp .env.example .env
docker compose up -d
docker compose exec app composer install
```

Then open http://127.0.0.1:8080 to see the output of the task enabled in `public/index.php`.

## Testing

Each task has its own PHPUnit configuration:

```bash
docker compose exec app vendor/bin/phpunit -c src/refactors/task1/phpunit.xml
```

Replace `task1` with `task2` ... `task7` or `example`.

## Status

Learning project (2025), partly finished (tasks 1 to 4 solved), not maintained.

## License

MIT, see [LICENSE](LICENSE).

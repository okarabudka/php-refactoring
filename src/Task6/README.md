# Task 6: Cart in the session with a query per line

## Smell

`Cart` mixes three concerns: it starts the session and writes `$_SESSION` directly (**Global Data**), it opens its
own database connection, and `getTotal()` runs one `SELECT` per cart line: the classic **N+1 query**. A product that
no longer exists silently costs `0`, because `fetchColumn()` returns `false` and `false * $qty` is `0`.

## Goal

Separate the cart from where it is stored and from where prices come from, load all prices in one query, and make
an unknown product an error instead of a free item.

## Refactorings applied

- **Extract Class**: `Cart` (quantities and the rule that a quantity is positive), `CartService` (the use case),
  `SessionCartStorage`, `PdoPriceRepository`.
- **Extract Interface** (Fowler, 1st edition): `CartStorage` and `PriceRepository`, so tests use
  `InMemoryCartStorage` and the service never touches `$_SESSION` or PDO.
- **Encapsulate Variable**: `$_SESSION['cart']` is read and written in one class only.
- **Substitute Algorithm**: N single-row queries become one `WHERE id IN (...)` query (batching).
- **Replace Error Code with Exception**: a missing price raises `UnknownProduct`.

## Why

The N+1 is invisible with three items in a test and expensive with fifty in production. A query-counting PDO in the
tests makes it visible: `Before` prepares one statement per line, `After` exactly one. `SessionCartStorage` keeps the
old session key and shape, so carts of users who are logged in during the deploy stay intact; a shared test reads a
session written by the other version.

## Deliberate behaviour change

An unknown product id used to count as free; now `getTotal()` throws `UnknownProduct` naming the ids. The session is
no longer started by the cart: `SessionCartStorage` refuses to work without an active session, so session handling
stays with the front controller.

## Getting the "before" under test

Two changes to the before code: the inline `new PDO('mysql:...')` became `Legacy\Database::connection()` (a static
seam for SQLite), and the unconditional `session_start()` calls are guarded with `session_status()`, because a second
`session_start()` raises a notice.

## Tests

[`tests/Task6/CartTest.php`](../../tests/Task6/CartTest.php): totals, accumulation, empty cart and session
compatibility run against both implementations; query counts are asserted for each.

```bash
make test ARGS="--filter Task6"
```

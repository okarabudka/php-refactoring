# Task 5: Controller that queries and echoes

## Smell

`ProductController::handleRequest()` reads `$_GET`, opens its own database connection, runs SQL and `echo`es HTML,
all in one method. It is **Global Data** (`$_GET`), a hidden infrastructure dependency and mixed responsibilities.
The output is not escaped, so a product name containing `<script>` is an XSS hole.

## Goal

A controller that receives its dependencies through the constructor and the request data as an argument, delegates
data access to a repository and rendering to a view, and returns the result instead of printing it.

## Refactorings applied

- **Extract Class**: `PdoProductRepository` (SQL), `ProductView` (HTML), `Product` (the row as a typed object).
- **Extract Interface** (Fowler, 1st edition): `ProductRepository`, so the controller does not know about PDO.
- **Parameterize Function**: the query parameters are passed in (`show(array $query)`) instead of read from `$_GET`.
- **Separate Query from Modifier**: `show()` returns the page body; sending it is the caller's side effect.
- **Replace Magic Literal**: the two messages are class constants.

## Why

Returning a string makes the controller a plain function of its input and its collaborators, testable without output
buffering or superglobals. With the view in its own class, escaping happens in exactly one place.

## Deliberate behaviour changes

- Output is HTML-escaped: `Fish & Chips <deluxe>` becomes `Fish &amp; Chips &lt;deluxe&gt;`. A characterization test
  pins the unescaped output of `Before`.
- An id that is not a positive integer (`'abc'`, `'1 OR 1=1'`) is treated as missing instead of being sent to the
  database.

## Getting the "before" under test

As in task 4, the only change to the before code is that its inline `new PDO('mysql:...')` became
`Legacy\Database::connection()`, a static seam the tests point at SQLite. The tests set `$_GET` and capture the echo
with output buffering.

## Tests

[`tests/Task5/ProductControllerTest.php`](../../tests/Task5/ProductControllerTest.php): found, missing id and
unknown product run against both implementations.

```bash
make test ARGS="--filter Task5"
```

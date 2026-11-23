# PHP Refactoring Exercises

[![CI](https://github.com/okarabudka/php-refactoring/actions/workflows/ci.yml/badge.svg)](https://github.com/okarabudka/php-refactoring/actions/workflows/ci.yml)

Ten small refactoring exercises in PHP. Each one has deliberately smelly "before" code, a refactored "after"
version, and characterization tests that pin the behaviour of the old code and run unchanged against the new one.

## Why it exists

Refactoring is changing structure without changing behaviour, and the second half of that sentence is the hard part.
These exercises show the whole loop on code small enough to read in a few minutes: name the smell, pin what the code
does today with tests, apply named refactorings from Martin Fowler's catalogue, and make every intended behaviour
change explicit instead of accidental.

It started in 2025 as a set of practice tasks and was reworked in 2026: PHP 8.5, one PSR-4 layout, characterization
tests for every task, four unfinished tasks completed and three new scenarios.

For refactorings at a larger scale, see two case studies of my own code from 2017:
[news_portal](https://github.com/okarabudka/news_portal) and
[php-auth-test](https://github.com/okarabudka/php-auth-test).

## Tasks

| Task                         | Smell                                                         | Main techniques                                                               |
|------------------------------|---------------------------------------------------------------|-------------------------------------------------------------------------------|
| [1](src/Task1/README.md)     | Order lines as arrays, by-reference loop, duplicated total    | Encapsulate Record, Extract Class, Replace Derived Variable with Query        |
| [2](src/Task2/README.md)     | if/elseif chain on a shipping type string                     | Replace Conditional with Polymorphism, Strategy injected for open/closed      |
| [3](src/Task3/README.md)     | Invoice rows as arrays with magic keys, late failures         | Introduce Parameter Object, Move Function, Split Phase                        |
| [4](src/Task4/README.md)     | One method validates, stores in MySQL, sends mail             | Extract Class, Extract Interface, injected PSR-20 clock                       |
| [5](src/Task5/README.md)     | Controller reads `$_GET`, queries and echoes unescaped HTML   | Extract Class (repository, view), Parameterize Function, return instead of echo |
| [6](src/Task6/README.md)     | Cart writes `$_SESSION` directly and runs an N+1 query        | Extract Interface (storage, prices), Substitute Algorithm (batched `IN` query) |
| [7](src/Task7/README.md)     | Discount rules nested four levels deep, flag argument         | Guard Clauses, Decompose Conditional, Remove Flag Argument                    |
| [8](src/Task8/README.md)     | Money as `float` plus a currency string                       | Replace Primitive with Object (`Money`), Change Reference to Value            |
| [9](src/Task9/README.md)     | Hidden static config, logger, database and `new DateTime()`   | Dependency injection, PSR-20 clock, PSR-3 logger, pure `TrialPolicy`          |
| [10](src/Task10/README.md)   | Template-method hierarchy with one subclass per combination   | Replace Subclass with Delegate, Replace Superclass with Delegate              |

Each task README names the smell, the goal, the refactorings applied, why, and any deliberate behaviour change.

## How to use it

```bash
git clone https://github.com/okarabudka/php-refactoring.git && cd php-refactoring
make install
make check
```

You only need Docker and make. Everything runs in a PHP 8.5 CLI container; the tests use in-memory SQLite, so there
is no database server.

### Day-to-day work

| Target         | What it does                                               |
|----------------|------------------------------------------------------------|
| `make install` | Build the PHP 8.5 CLI image and install dependencies       |
| `make test`    | Run the PHPUnit tests; `make test ARGS="--filter Task6"` for one task |
| `make stan`    | Run PHPStan (level max)                                    |
| `make cs`      | Check code style (PHP CS Fixer, PER-CS)                    |
| `make fix`     | Fix code style                                             |
| `make check`   | Code style, PHPStan and tests                              |
| `make sh`      | Open a shell in the container                              |
| `make help`    | List all targets                                           |

## Architecture

```
src/TaskN/Before/   the smelly original, untyped on purpose
src/TaskN/After/    the refactored code
src/TaskN/README.md smell, goal, refactorings, why
tests/TaskN/        one test class per task, run against Before and After
```

| Path              | Contents                                                                                  |
|-------------------|-------------------------------------------------------------------------------------------|
| `src/Legacy/`     | `Database`, the static connection the "before" code uses, with a static setter as test seam |
| `src/Shared/`     | `SystemClock`, the PSR-20 clock used by the "after" code                                   |
| `tests/Support/`  | `FrozenClock`, `RecordingLogger`, in-memory SQLite helper                                  |
| `docker/`         | PHP 8.5 CLI image                                                                          |

The namespace is `Okarabudka\Refactoring\TaskN\Before|After`. All code uses `declare(strict_types=1)`; the "after"
code is `final` or `readonly` where possible and fully typed. PHPStan runs at level max with strict rules on `src/`
and `tests/`, except the "before" directories, whose missing types are part of the smell.

## Testing

```bash
make test
```

Most test classes get their implementations from a data provider, so each case runs once with `before` and once with
`after` in its name. Where the refactoring changes behaviour on purpose (escaping, rejecting invalid input, not losing
a cent), a test pins the old behaviour on `Before` and a second test shows the new one on `After`.

To get legacy code under test with as few edits as possible, the "before" code reaches its database through
`Legacy\Database::connection()`, which the tests point at SQLite (Michael Feathers' "Introduce Static Setter"). Each
README of tasks 4 to 6 lists exactly what was changed in their "before" code for that.

## Status

Maintained.

## License

MIT, see [LICENSE](LICENSE).

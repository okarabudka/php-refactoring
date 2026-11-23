# Task 9: Hidden dependencies on time, config and static services

## Smell

`TrialService::status()` looks like a simple query, but it has four **hidden dependencies**: the static
`Legacy\Database`, the static `Config::$trialDays`, the static `Logger`, and the system clock via `new DateTime()`. The
result changes with the wall clock, so a test has to compute its fixture relative to "now" and cannot check an exact
boundary. The user id is concatenated into the SQL.

## Goal

Make every input explicit, so the rule can be tested at an exact second, with any trial length, without a database
for the rule itself.

## Refactorings applied

- **Parameterize Function / dependency injection**: the clock (PSR-20 `ClockInterface`), the logger (PSR-3
  `LoggerInterface`) and the repository come in through the constructor.
- **Extract Class**: the rule becomes `TrialPolicy::statusAfter(int $elapsedDays)`, a pure function; the trial
  length and warning period are its constructor values instead of global config.
- **Extract Interface** (Fowler, 1st edition): `TrialRepository`, implemented with a prepared statement in
  `PdoTrialRepository`.
- **Replace Nested Conditional with Guard Clauses** and a `match` over the thresholds.
- **Replace Primitive with Object**: the returned strings become the `TrialStatus` enum (with the same values).

## Why

`FrozenClock` makes the boundary test exact (`08:59:59` is still "ending", `09:00:00` on day 31 is "expired"). The
policy can be tested in isolation and reconfigured (14-day trial) without touching global state. PSR interfaces keep
the service independent of a framework: any PSR-20 clock and PSR-3 logger fit.

## Tests

[`tests/Task9/TrialServiceTest.php`](../../tests/Task9/TrialServiceTest.php): the same table of elapsed days runs
against both implementations. For `Before` the fixture is written relative to the real clock (one hour of margin),
which is exactly the fragility the refactoring removes.

```bash
make test ARGS="--filter Task9"
```

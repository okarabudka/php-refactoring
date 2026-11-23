# Task 4: UserManager that does everything

## Smell

`UserManager::register()` is a **Large Class** in one method with **Divergent Change**: it validates input, builds an
untyped `stdClass` user, hashes the password, reads the system time, opens a database connection and sends mail with
`mail()`. None of those dependencies can be replaced, so the happy path can only be tested against a real database
and a real mail transport.

## Goal

Make `UserManager` a thin application service whose collaborators (storage, mail, time) are injected, so each piece
can be tested on its own and the whole use case without infrastructure.

## Refactorings applied

- **Extract Class**: `Registration` (validated input), `User` (the new account), `PdoUserRepository` (SQL),
  `NativeMailer` (the `mail()` call).
- **Extract Interface** (Fowler, 1st edition): `UserRepository` and `Mailer`, so `UserManager`
  depends on roles, not on PDO and `mail()`.
- **Replace Primitive with Object**: the `stdClass` with dynamic properties becomes the immutable `User`, the raw
  array becomes `Registration`.
- **Replace Constructor with Factory Function**: `User::register()` is the only way to create a user, and it always
  hashes the password.
- **Parameterize Function**: the current time comes from an injected PSR-20 `ClockInterface` instead of `date()`.
- **Replace Error Code with Exception**, made specific: `InvalidRegistration` keeps the old messages.

## Why

With the collaborators injected, the welcome mail and the timestamp can be asserted exactly (`SpyMailer`,
`FrozenClock`), and the SQL is checked against an in-memory SQLite database. The old messages are kept so that
callers that show them do not notice the change. A failed `mail()` used to be ignored; `NativeMailer` now reports it.

## Getting the "before" under test

The original opened `new PDO('mysql:...')` inline. The only change to the before code is that it asks the static
`Legacy\Database::connection()` for that connection, a seam the characterization tests use to swap in SQLite
(Feathers, *Working Effectively with Legacy Code*: "Introduce Static Setter"). `mail()` goes to `/bin/true` via
`sendmail_path` in the Docker image and in CI.

## Tests

[`tests/Task4/UserManagerTest.php`](../../tests/Task4/UserManagerTest.php): storing the user and rejecting invalid
input run against both implementations; mail and clock are checked on `After` only, because `Before` cannot observe
them.

```bash
make test ARGS="--filter Task4"
```

# Task 3: Invoice computed from loose arrays

## Smell

`Invoice` takes an array of arrays and reads `price`, `quantity`, `discount` and `tax` by **magic keys** in the middle
of the calculation. It is **Primitive Obsession** on the row level: a wrong shape, a missing key or a 150 % discount
is accepted on construction and only fails (or silently computes nonsense) later.

## Goal

Validate the input once at the boundary and keep the arithmetic where the data lives.

## Refactorings applied

- **Encapsulate Record / Introduce Parameter Object**: each row becomes an immutable `InvoiceItem`.
- **Move Function**: the line calculation moves from `Invoice::getTotal()` to `InvoiceItem::total()`.
- **Split Phase**: parsing the legacy array (`Invoice::fromArray()`, `InvoiceItem::fromArray()`) is separated from
  computing; the constructors only accept typed values.
- **Replace Loop with Pipeline**: the total is `array_sum` over the item totals.

## Why

Once an `InvoiceItem` exists it is valid, so `total()` needs no checks. Callers that already have typed values use
the constructor; only the legacy edge uses `fromArray()`. The validator class of the first solution was folded into
the value object, because invariants belong to the type that has them.

## Deliberate behaviour change

A flat row instead of a list of rows used to be accepted and failed with a `TypeError` when the total was computed;
now it is rejected with an `InvalidArgumentException` on construction. Out-of-range values (negative price, discount
above 1) are rejected instead of computed. Numeric strings such as `'100'` are rejected too: the boundary must cast.

## Tests

[`tests/Task3/InvoiceTest.php`](../../tests/Task3/InvoiceTest.php): totals run against both implementations.

```bash
make test ARGS="--filter Task3"
```

# Task 8: Money as a float

## Smell

**Primitive Obsession**: `Checkout` keeps prices as `float` and the currency as a separate string that travels
alongside (a **Data Clump**). Three bugs follow from it:

- `0.1 + 0.2` is `0.30000000000000004`; only the final `number_format()` hides it;
- splitting 100.00 into three pays out `33.33` three times, so a cent disappears;
- `total()` adds EUR and USD lines without complaint, because the currency never reaches the arithmetic.

## Goal

A value object that makes these bugs impossible instead of carefully avoided.

## Refactorings applied

- **Replace Primitive with Object**: `Money` (integer cents plus `Currency`), `Currency` as an enum.
- **Combine Functions into Class**: total, formatting and splitting move onto `Money` (`add()`, `multiply()`,
  `format()`, `allocate()`); `Checkout` only orchestrates.
- **Change Reference to Value**: `Money` is immutable and compared with `equals()`.
- **Introduce Parameter Object**: a line is a `LineItem` (price and quantity) instead of an array with a currency key
  that is never checked.
- **Move Function**: formatting per currency moves to `Currency::format()`.

## Why

Integer cents make addition exact. `allocate()` distributes the leftover cents, so the parts always add up to the
whole (`33.34 + 33.33 + 33.33`). Adding two amounts in different currencies throws `CurrencyMismatch`. `Money::of()`
takes a decimal string, never a float, so no binary rounding enters at the boundary.

## Deliberate behaviour changes

`split()` returns parts that add up to the total, and mixing currencies is an error. Both old behaviours are pinned by
characterization tests on `Before`.

## Tests

[`tests/Task8/CheckoutTest.php`](../../tests/Task8/CheckoutTest.php): formatted totals run against both
implementations; the three bugs are pinned on `Before` and shown fixed on `After`.

```bash
make test ARGS="--filter Task8"
```

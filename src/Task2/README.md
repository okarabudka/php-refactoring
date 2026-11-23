# Task 2: Shipping cost as an if/elseif chain

## Smell

`ShippingCalculator::calculate()` is a **Repeated Switch** on a string type: every new shipping type means editing
the calculator, and every type's formula sits in the same method.

## Goal

Remove the conditional and make the calculator closed for modification: a new shipping type is a new class, nothing
else changes.

## Refactorings applied

- **Replace Conditional with Polymorphism**: one `ShippingMethod` implementation per type (`StandardShipping`,
  `ExpressShipping`, `OvernightShipping`), each with its formula.
- **Move Function**: the formula of each branch moves into its class (`cost()`), and each class names its own key
  (`type()`).
- **Parameterize Function / dependency injection**: the calculator receives its methods through the constructor
  instead of instantiating them, so the composition root decides which types exist.
- **Replace Error Code with Exception**, made specific: `UnknownShippingType` instead of a bare `Exception`.

## Why

The first solution of this exercise still listed the three types in the calculator's constructor (plus an enum of
them), so a new type still meant editing the calculator: the Strategy pattern without its benefit. Injecting the
strategies is what actually delivers open/closed; the test adds a `pickup` type without touching the calculator.

## Tests

[`tests/Task2/ShippingCalculatorTest.php`](../../tests/Task2/ShippingCalculatorTest.php): costs and the unknown-type
error run against both implementations.

```bash
make test ARGS="--filter Task2"
```

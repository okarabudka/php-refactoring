# Task 7: Discount rules in nested conditionals

## Exercise

`Before\DiscountCalculator::calculate($customer, $amount, $isBlackFriday, $coupon)` returns the price after
discount. The business rules are:

- inactive customers get no discount at all;
- VIP customers get 20 % on orders above 100 and 10 % otherwise;
- other customers get 5 % after more than 5 years;
- Black Friday adds 10 percentage points;
- the coupon `WELCOME10` adds 10 points on the customer's first order;
- the discount never exceeds 30 %; the price is rounded to cents.

Refactor the method so that each rule can be read on its own, without changing a single result. First pin the
current behaviour with a table of cases (including the boundaries: exactly 100, exactly 5 years, the cap), then
restructure.

## Smell

**Long Function** with conditionals nested four levels deep, a **Flag Argument** (`$isBlackFriday`), **magic
numbers** (`0.2`, `100`, `5`, `0.3`, `'WELCOME10'`), and the customer as an untyped array with magic keys. Percentages
are added as floats, so `0.2 + 0.1` is `0.30000000000000004` and only the cap hides it.

## Refactorings applied

- **Replace Nested Conditional with Guard Clauses**: an inactive customer returns early.
- **Decompose Conditional / Extract Function**: `tierPercent()`, `couponPercent()` and `discounted()` each hold one
  rule.
- **Remove Flag Argument**: `calculate(..., $isBlackFriday)` becomes `price()` and `blackFridayPrice()`; the caller
  states the intent in the method name.
- **Replace Magic Literal**: every threshold and percentage is a named constant.
- **Replace Primitive with Object**: the customer array becomes `Customer` with a `CustomerTier` enum and an
  `isFirstOrder()` query.
- **Substitute Algorithm**: discounts are whole percentages (integers), so adding and capping them is exact.

## Why

Each rule now fits on one line next to its name, and the boundaries (`>` vs `>=`) are visible in one place. Removing
the flag means a call site reads `blackFridayPrice($customer, $amount)` instead of `calculate($customer, $amount,
true)`.

## Tests

[`tests/Task7/DiscountCalculatorTest.php`](../../tests/Task7/DiscountCalculatorTest.php): one table of 13 cases runs
against both implementations; every case produces exactly the same price.

```bash
make test ARGS="--filter Task7"
```

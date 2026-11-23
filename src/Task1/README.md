# Task 1: Order kept as arrays

## Smell

`Order` stores each line as an associative array with public properties, finds an existing line with a flag and a
`foreach` by reference, and keeps a running `$totalPrice` next to the per-line totals. Nothing stops a caller from
breaking the arrays from outside, and the two totals can drift apart.

## Goal

Same receipt and total for the same input, with the line logic in its own class and no state that can get out of
sync.

## Refactorings applied

- **Encapsulate Record**: the `['name', 'price', 'quantity', 'total']` array becomes `OrderItem`.
- **Extract Class**: quantity rules and the line total move into `OrderItem`.
- **Substitute Algorithm**: the flag and by-reference loop become a lookup in an array keyed by name.
- **Replace Derived Variable with Query**: `$totalPrice` is no longer stored; `getTotalPrice()` sums the lines.
- **Extract Function**: `receipt()` builds the text, `printReceipt()` only echoes it, so the text is testable without
  output buffering.

## Why

A record that only one class can change keeps its invariants (positive quantity, non-negative price). A total that
is computed cannot disagree with the lines it is computed from.

## Deliberate behaviour change

Adding an item that is already in the order at a different price used to keep the first price on the line but add
the new price to its total (`Apple x3 - $5` at 1.5 each). The characterization test pins that quirk for `Before`;
`After` rejects it with an exception instead of silently mixing prices.

An earlier version of this solution declared `OrderItem::getTotal(): int` and truncated `1.5 * 3` to `4`; the shared
receipt test now catches that.

## Tests

[`tests/Task1/OrderTest.php`](../../tests/Task1/OrderTest.php): the receipt and total tests run against both
`Before\Order` and `After\Order`.

```bash
make test ARGS="--filter Task1"
```

# Task 10: Exporter hierarchy that multiplies

## Smell

`ReportExporter` is a template method with `header()`, `row()` and `footer()` hooks. Two independent variations, the
output format (CSV, HTML) and an optional totals line, are both expressed through inheritance, so every combination
needs its own subclass: `CsvExporter`, `CsvExporterWithTotals`, `HtmlExporter`, `HtmlExporterWithTotals`. The totals
logic is copied between the two `WithTotals` classes (**Duplicated Code**), and a third format or a second summary
line (averages) would multiply the classes again. It is a **Parallel Inheritance Hierarchy** in a single tree.

## Goal

One exporter that is configured with a format and any number of summary rows, so that formats and summaries vary
independently.

## Refactorings applied

- **Replace Subclass with Delegate**: the format-specific hooks move into `Format` implementations (`CsvFormat`,
  `HtmlFormat`), which the exporter holds instead of inheriting from.
- **Replace Superclass with Delegate**: the totals line no longer comes from a subclass that overrides `footer()`; it
  is a `SummaryRow` (`TotalsRow`) passed in.
- **Remove Subclass** (four times): the exporter is a single `final` class.
- **Extract Function**: the duplicated summing loop exists once, in `TotalsRow`.

## Why

Two axes of change in one inheritance tree grow as M × N classes; as delegates they grow as M + N. The test adds a
Markdown format as an anonymous class and gets the totals line for free, which would have needed two new subclasses
before. Composition also keeps each piece small enough to test alone.

## Deliberate behaviour changes

`HtmlFormat` escapes cell content. An empty report throws `InvalidArgumentException` instead of a PHP warning on
`$rows[0]`.

## Tests

[`tests/Task10/ReportExporterTest.php`](../../tests/Task10/ReportExporterTest.php): all four combinations produce the
same output in `Before` and `After`.

```bash
make test ARGS="--filter Task10"
```

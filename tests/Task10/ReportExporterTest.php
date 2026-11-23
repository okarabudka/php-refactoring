<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Tests\Task10;

use InvalidArgumentException;
use Okarabudka\Refactoring\Task10\After;
use Okarabudka\Refactoring\Task10\After\CsvFormat;
use Okarabudka\Refactoring\Task10\After\Format;
use Okarabudka\Refactoring\Task10\After\HtmlFormat;
use Okarabudka\Refactoring\Task10\After\TotalsRow;
use Okarabudka\Refactoring\Task10\Before;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ReportExporterTest extends TestCase
{
    private const array ROWS = [
        ['product' => 'Keyboard', 'units' => 3, 'revenue' => 89.7, 'region' => 'DE'],
        ['product' => 'Mouse', 'units' => 10, 'revenue' => '120.5', 'region' => 'AT'],
    ];

    private const string CSV = "product,units,revenue,region\nKeyboard,3,89.7,DE\nMouse,10,120.5,AT\n";
    private const string CSV_TOTALS = "TOTAL,13,210.2,\n";
    private const string HTML = "<table><tr><th>product</th><th>units</th><th>revenue</th><th>region</th></tr>\n"
        . "<tr><td>Keyboard</td><td>3</td><td>89.7</td><td>DE</td></tr>\n"
        . "<tr><td>Mouse</td><td>10</td><td>120.5</td><td>AT</td></tr>\n";
    private const string HTML_TOTALS = "<tr><td>TOTAL</td><td>13</td><td>210.2</td><td></td></tr>\n";

    /** @return iterable<string, array{Before\ReportExporter|After\ReportExporter, string}> */
    public static function exporters(): iterable
    {
        yield 'before, csv' => [new Before\CsvExporter(), self::CSV];
        yield 'before, csv with totals' => [new Before\CsvExporterWithTotals(), self::CSV . self::CSV_TOTALS];
        yield 'before, html' => [new Before\HtmlExporter(), self::HTML . "</table>\n"];
        yield 'before, html with totals' => [new Before\HtmlExporterWithTotals(), self::HTML . self::HTML_TOTALS . "</table>\n"];
        yield 'after, csv' => [new After\ReportExporter(new CsvFormat()), self::CSV];
        yield 'after, csv with totals' => [new After\ReportExporter(new CsvFormat(), new TotalsRow()), self::CSV . self::CSV_TOTALS];
        yield 'after, html' => [new After\ReportExporter(new HtmlFormat()), self::HTML . "</table>\n"];
        yield 'after, html with totals' => [new After\ReportExporter(new HtmlFormat(), new TotalsRow()), self::HTML . self::HTML_TOTALS . "</table>\n"];
    }

    #[DataProvider('exporters')]
    public function testExport(Before\ReportExporter|After\ReportExporter $exporter, string $expected): void
    {
        self::assertSame($expected, $exporter->export(self::ROWS));
    }

    public function testANewFormatGetsTotalsWithoutANewClass(): void
    {
        $markdown = new class implements Format {
            public function header(array $columns): string
            {
                return $this->row($columns) . '|' . str_repeat('---|', count($columns)) . "\n";
            }

            public function row(array $cells): string
            {
                return '| ' . implode(' | ', $cells) . " |\n";
            }

            public function footer(): string
            {
                return '';
            }
        };

        $report = new After\ReportExporter($markdown, new TotalsRow())->export(self::ROWS);

        self::assertStringEndsWith("| TOTAL | 13 | 210.2 |  |\n", $report);
    }

    public function testHtmlIsEscaped(): void
    {
        $report = new After\ReportExporter(new HtmlFormat())->export([['product' => '<b>Mouse</b> & Pad']]);

        self::assertStringContainsString('<td>&lt;b&gt;Mouse&lt;/b&gt; &amp; Pad</td>', $report);
    }

    public function testAnEmptyReportIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new After\ReportExporter(new CsvFormat())->export([]);
    }
}

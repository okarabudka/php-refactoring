<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task3\After;

use InvalidArgumentException;

final readonly class Invoice
{
    /** @var list<InvoiceItem> */
    private array $items;

    public function __construct(InvoiceItem ...$items)
    {
        $this->items = array_values($items);
    }

    /** @param array<mixed> $rows rows in the legacy format: price, quantity, discount, tax */
    public static function fromArray(array $rows): self
    {
        return new self(...array_map(
            static fn(mixed $row): InvoiceItem => is_array($row)
                ? InvoiceItem::fromArray($row)
                : throw new InvalidArgumentException('Each invoice row must be an array.'),
            array_values($rows),
        ));
    }

    public function total(): float
    {
        return array_sum(array_map(static fn(InvoiceItem $item): float => $item->total(), $this->items));
    }
}

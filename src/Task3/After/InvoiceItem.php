<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task3\After;

use InvalidArgumentException;

final readonly class InvoiceItem
{
    private const array FIELDS = ['price', 'quantity', 'discount', 'tax'];

    /**
     * @param float $discount fraction of the line, 0.1 = 10 %
     * @param float $tax      fraction added after the discount, 0.2 = 20 %
     */
    public function __construct(
        public float $price,
        public int $quantity,
        public float $discount = 0.0,
        public float $tax = 0.0,
    ) {
        if ($price < 0) {
            throw new InvalidArgumentException('Price must not be negative.');
        }
        if ($quantity < 0) {
            throw new InvalidArgumentException('Quantity must not be negative.');
        }
        self::assertFraction('discount', $discount);
        self::assertFraction('tax', $tax);
    }

    /**
     * Parses one row of the legacy array format at the boundary.
     *
     * @param array<mixed> $row
     */
    public static function fromArray(array $row): self
    {
        foreach (self::FIELDS as $field) {
            if (!array_key_exists($field, $row)) {
                throw new InvalidArgumentException("Missing field: {$field}");
            }
        }
        if (!is_int($row['quantity'])) {
            throw new InvalidArgumentException('Quantity must be an integer.');
        }

        return new self(
            self::number('price', $row['price']),
            $row['quantity'],
            self::number('discount', $row['discount']),
            self::number('tax', $row['tax']),
        );
    }

    public function total(): float
    {
        $subtotal = $this->price * $this->quantity;
        $discounted = $subtotal - $subtotal * $this->discount;

        return $discounted + $discounted * $this->tax;
    }

    private static function number(string $field, mixed $value): float
    {
        if (!is_int($value) && !is_float($value)) {
            throw new InvalidArgumentException("{$field} must be a number.");
        }

        return (float) $value;
    }

    private static function assertFraction(string $field, float $value): void
    {
        if ($value < 0 || $value > 1) {
            throw new InvalidArgumentException("{$field} must be between 0 and 1.");
        }
    }
}

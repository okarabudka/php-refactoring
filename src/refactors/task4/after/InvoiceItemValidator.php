<?php

namespace src\refactors\task4\after;

use InvalidArgumentException;

class InvoiceItemValidator
{
    public static function validate(array $item): void {
        $requiredFields = ['price', 'quantity', 'discount', 'tax'];

        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $item)) {
                throw new InvalidArgumentException("Missing required field: $field");
            }
        }

        if (!is_numeric($item['price']) || $item['price'] < 0) {
            throw new InvalidArgumentException("Invalid price value");
        }

        if (!is_int($item['quantity']) || $item['quantity'] < 0) {
            throw new InvalidArgumentException("Invalid quantity value");
        }

        if (!is_numeric($item['discount']) || $item['discount'] < 0 || $item['discount'] > 1) {
            throw new InvalidArgumentException("Invalid discount value");
        }

        if (!is_numeric($item['tax']) || $item['tax'] < 0 || $item['tax'] > 1) {
            throw new InvalidArgumentException("Invalid tax value");
        }
    }
}

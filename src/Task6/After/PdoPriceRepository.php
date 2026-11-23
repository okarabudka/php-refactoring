<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task6\After;

use PDO;
use UnexpectedValueException;

final readonly class PdoPriceRepository implements PriceRepository
{
    public function __construct(private PDO $pdo) {}

    public function pricesFor(array $productIds): array
    {
        if ($productIds === []) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($productIds), '?'));
        $statement = $this->pdo->prepare("SELECT id, price FROM products WHERE id IN ({$placeholders})");
        $statement->execute($productIds);

        $prices = [];
        foreach ($statement->fetchAll(PDO::FETCH_KEY_PAIR) as $id => $price) {
            if (!is_numeric($price)) {
                throw new UnexpectedValueException("Price of product {$id} is not a number.");
            }
            $prices[(int) $id] = (float) $price;
        }

        return $prices;
    }
}

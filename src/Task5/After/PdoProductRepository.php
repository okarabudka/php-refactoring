<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task5\After;

use PDO;
use UnexpectedValueException;

final readonly class PdoProductRepository implements ProductRepository
{
    public function __construct(private PDO $pdo) {}

    public function find(int $id): ?Product
    {
        $statement = $this->pdo->prepare('SELECT id, name, price FROM products WHERE id = ?');
        $statement->execute([$id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        if (!is_numeric($row['id']) || !is_string($row['name']) || !is_numeric($row['price'])) {
            throw new UnexpectedValueException("Product row {$id} has an unexpected shape.");
        }

        return new Product((int) $row['id'], $row['name'], (float) $row['price']);
    }
}

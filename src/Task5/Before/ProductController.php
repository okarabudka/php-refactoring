<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task5\Before;

use Okarabudka\Refactoring\Legacy\Database;

class ProductController
{
    public function handleRequest()
    {
        $pdo = Database::connection();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo 'Missing product ID';
            return;
        }

        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if (!$product) {
            echo 'Product not found';
            return;
        }

        echo "<h1>{$product['name']}</h1>";
        echo "<p>Price: {$product['price']}</p>";
    }
}

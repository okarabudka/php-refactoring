<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task6\Before;

use Okarabudka\Refactoring\Legacy\Database;

class Cart
{
    public function addProduct($productId, $quantity)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
    }

    public function getTotal()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $pdo = Database::connection();
        $total = 0;

        foreach ($_SESSION['cart'] ?? [] as $productId => $qty) {
            $stmt = $pdo->prepare('SELECT price FROM products WHERE id = ?');
            $stmt->execute([$productId]);
            $price = $stmt->fetchColumn();
            $total += $price * $qty;
        }

        return $total;
    }
}

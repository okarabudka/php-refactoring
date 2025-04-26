<?php

namespace src\refactors\task6\before;

class Cart
{
    public function addProduct($productId, $quantity) {
        session_start();
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
    }

    public function getTotal() {
        session_start();
        $pdo = new PDO('mysql:host=localhost;dbname=shop', 'root', '');
        $total = 0;

        foreach ($_SESSION['cart'] ?? [] as $productId => $qty) {
            $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $price = $stmt->fetchColumn();
            $total += $price * $qty;
        }

        return $total;
    }
}

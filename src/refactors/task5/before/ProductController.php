<?php

namespace src\refactors\task5\before;

class ProductController
{
    public function handleRequest() {
        $pdo = new PDO('mysql:host=localhost;dbname=shop', 'root', '');

        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "Missing product ID";
            return;
        }

        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if (!$product) {
            echo "Product not found";
            return;
        }

        echo "<h1>{$product['name']}</h1>";
        echo "<p>Price: {$product['price']}</p>";
    }
}

<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task5\After;

final class ProductView
{
    public function render(Product $product): string
    {
        return '<h1>' . $this->escape($product->name) . '</h1>'
            . '<p>Price: ' . $this->escape((string) $product->price) . '</p>';
    }

    private function escape(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

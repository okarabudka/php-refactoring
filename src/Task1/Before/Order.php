<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task1\Before;

class Order
{
    public $items;
    public $totalPrice;

    public function __construct()
    {
        $this->items = [];
        $this->totalPrice = 0;
    }

    public function addItem($name, $price, $quantity)
    {
        $found = false;
        foreach ($this->items as &$item) {
            if ($item['name'] == $name) {
                $item['quantity'] += $quantity;
                $item['total'] += $price * $quantity;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $this->items[] = [
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
                'total' => $price * $quantity,
            ];
        }
        $this->totalPrice += $price * $quantity;
    }

    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    public function printReceipt()
    {
        echo "Receipt:\n";
        foreach ($this->items as $item) {
            echo "{$item['name']} x{$item['quantity']} - \${$item['total']}\n";
        }
        echo "Total: \${$this->totalPrice}\n";
    }
}

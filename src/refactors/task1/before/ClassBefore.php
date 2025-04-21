<?php

namespace src\refactors\task1\before;

class ClassBefore
{
    public static function run(): void
    {
        // Example usage
        $order = new Order();
        $order->addItem("Apple", 1.5, 2);
        $order->addItem("Banana", 1.0, 3);
        $order->addItem("Apple", 1.5, 1);
        $order->printReceipt();
    }
}

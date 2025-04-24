<?php

namespace src\refactors\task3\before;

class ClassBefore
{
    public static function run(): void
    {
        $invoice = new Invoice([
            ['price' => 100, 'quantity' => 2, 'discount' => 0.1, 'tax' => 0.2], // 100*2 = 200 → -10% = 180 → +20% = 216
            ['price' => 50, 'quantity' => 1, 'discount' => 0, 'tax' => 0]       // = 50
        ]);

        echo $invoice->getTotal();
    }
}

<?php

namespace src\refactors\task2\before;

class ClassBefore
{
    public static function run(): void
    {
        $calculator = new ShippingCalculator();
        echo $calculator->calculate("standard", 2) . "\n";
        echo $calculator->calculate("express", 2) . "\n";
        echo $calculator->calculate("overnight", 2) . "\n";
    }
}

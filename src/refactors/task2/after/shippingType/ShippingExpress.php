<?php

namespace src\refactors\task2\after\shippingType;

class ShippingExpress implements ShippingType
{

    public function calculate(float $weight): float
    {
        return $weight * 2.5 + 10;
    }
}

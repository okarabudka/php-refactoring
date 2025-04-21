<?php

namespace src\refactors\task2\after\shippingType;

use src\refactors\task2\after\shippingType\ShippingType;

class ShippingStandart implements ShippingType
{

    public function calculate(float $weight): float
    {
        return $weight * 1.2;
    }
}

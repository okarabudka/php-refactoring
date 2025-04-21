<?php

namespace src\refactors\task2\after\shippingType;

class ShippingOvernight implements ShippingType
{

    public function calculate(float $weight): float
    {
        return $weight * 5 + 20;
    }
}

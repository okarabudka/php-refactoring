<?php

namespace src\refactors\task2\after\shippingType;

interface ShippingType
{
    public function calculate(float $weight): float;
}

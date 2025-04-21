<?php

namespace src\refactors\task2\after;

use Exception;
use src\refactors\task2\after\shippingType\ShippingExpress;
use src\refactors\task2\after\shippingType\ShippingOvernight;
use src\refactors\task2\after\shippingType\ShippingStandart;
use src\refactors\task2\after\shippingType\ShippingStrategy;
use src\refactors\task2\after\shippingType\ShippingType;

class ShippingCalculator
{
    private array $shippingTypes;
    public function __construct(){
        $this->shippingTypes[ShippingStrategy::Standard->value] = new ShippingStandart();
        $this->shippingTypes[ShippingStrategy::Express->value] = new ShippingExpress();
        $this->shippingTypes[ShippingStrategy::Overnight->value] = new ShippingOvernight();
    }
    public function calculate(string $type, float $weight) {
        if(!isset($this->shippingTypes[$type])){
            throw new Exception("Shipping strategy not defined for type: {$type}");
        }

        return $this->shippingTypes[$type]->calculate($weight);
    }
}

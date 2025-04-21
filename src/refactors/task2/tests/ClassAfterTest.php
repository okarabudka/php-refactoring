<?php

namespace src\refactors\task2\tests;

use Exception;
use PHPUnit\Framework\TestCase;
use src\refactors\task2\after\ShippingCalculator;

class ClassAfterTest extends TestCase
{
    public function testSimple()
    {
        $calculator = new ShippingCalculator();

        $this->assertEquals(2.4, $calculator->calculate("standard", 2));
        $this->assertEquals(15, $calculator->calculate("express", 2));
        $this->assertEquals(30, $calculator->calculate("overnight", 2));
    }

    public function testException(){
        $calculator = new ShippingCalculator();

        $this->expectException(Exception::class);
        $calculator->calculate("other", 2);
    }
}

<?php

namespace src\refactors\task1\tests;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use src\refactors\task1\after\Order;

class ClassAfterTest extends TestCase
{
    public function testAddNewItem()
    {
        $order = new Order();
        $order->addItem("Apple", 1.5, 2);

        $this->expectOutputString("Receipt:\nApple x2 - \$3\nTotal: \$3\n");
        $order->printReceipt();
    }

    public function testAddSameItemIncreasesQuantity()
    {
        $order = new Order();
        $order->addItem("Banana", 1.0, 3);
        $order->addItem("Banana", 1.0, 2);

        $this->expectOutputString("Receipt:\nBanana x5 - \$5\nTotal: \$5\n");
        $order->printReceipt();
    }

    public function testTotalPriceCalculation()
    {
        $order = new Order();
        $order->addItem("Apple", 2.0, 1);
        $order->addItem("Banana", 1.0, 2);

        $this->assertEquals(4.0, $order->getTotalPrice());
    }

    /*public function testFindItemReturnsNullIfNotFound()
    {
        $reflection = new ReflectionClass(Order::class);
        $method = $reflection->getMethod('findItem');
        $method->setAccessible(true);

        $order = new Order();
        $this->assertNull($method->invoke($order, "NonExistentItem"));
    }*/
}

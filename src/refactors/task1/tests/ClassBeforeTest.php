<?php

namespace src\refactors\task1\tests;

use PHPUnit\Framework\TestCase;
use src\refactors\task1\before\Order;

class ClassBeforeTest extends TestCase
{
    public function testAddSingleItem(): void
    {
        $order = new Order();
        $order->addItem("Apple", 1.5, 2);

        $this->assertCount(1, $order->items);

        $item = $order->items[0];
        $this->assertEquals("Apple", $item['name']);
        $this->assertEquals(1.5, $item['price']);
        $this->assertEquals(2, $item['quantity']);
        $this->assertEquals(3.0, $item['total']);
    }

    public function testAddSameItemIncrementsQuantity()
    {
        $order = new Order();
        $order->addItem("Apple", 1.5, 2);
        $order->addItem("Apple", 1.5, 1);

        $this->assertCount(1, $order->items);
        $this->assertEquals(4.5, $order->getTotalPrice());

        $item = $order->items[0];
        $this->assertEquals(3, $item['quantity']);
        $this->assertEquals(4.5, $item['total']);
    }

    public function testAddMultipleItems()
    {
        $order = new Order();
        $order->addItem("Apple", 1.5, 2);
        $order->addItem("Banana", 1.0, 3);

        $this->assertCount(2, $order->items);
        $this->assertEquals(6.0, $order->getTotalPrice());

        $this->assertEquals("Apple", $order->items[0]['name']);
        $this->assertEquals("Banana", $order->items[1]['name']);
    }

    public function testTotalPriceCalculation()
    {
        $order = new Order();
        $order->addItem("Apple", 2.0, 1);
        $order->addItem("Banana", 1.5, 2);
        $order->addItem("Apple", 2.0, 2); // Total Apple = 3 items

        $this->assertEquals(9.0, $order->getTotalPrice());
    }
}

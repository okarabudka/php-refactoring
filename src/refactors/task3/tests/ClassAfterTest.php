<?php

namespace src\refactors\task3\tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use src\refactors\task3\after\Invoice;
use TypeError;

class ClassAfterTest extends TestCase
{
    public function testCorrectTotal()
    {
        $invoice = new Invoice([
            ['price' => 100, 'quantity' => 2, 'discount' => 0.1, 'tax' => 0.2], // 100*2 = 200 → -10% = 180 → +20% = 216
            ['price' => 50, 'quantity' => 1, 'discount' => 0, 'tax' => 0]       // = 50
        ]);

        $this->assertEquals(266, $invoice->getTotal());
    }

    public function testIncorrectTotal()
    {
        $invoice = new Invoice([
            ['price' => 100, 'quantity' => 2, 'discount' => 0.1, 'tax' => 0.2], // 100*2 = 200 → -10% = 180 → +20% = 216
            ['price' => 50, 'quantity' => 1, 'discount' => 0, 'tax' => 0]       // = 50
        ]);

        $this->assertNotEquals(100, $invoice->getTotal());
    }

    public function testFalseIncomingData()
    {
        $this->expectException(InvalidArgumentException::class);

        $invoice = new Invoice([
            'price' => '100', 'quantity' => '2', 'discount' => '0.1', 'tax' => '0.2'
        ]);

        $invoice->getTotal();
    }
}

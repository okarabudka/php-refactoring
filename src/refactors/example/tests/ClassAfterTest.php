<?php

namespace src\refactors\example\tests;

use PHPUnit\Framework\TestCase;
use src\refactors\example\after\ClassAfter;

class ClassAfterTest extends TestCase
{
    public function testPalindrome()
    {
        $this->assertTrue(ClassAfter::isPalindrome("A man a plan a canal Panama"));
        $this->assertFalse(ClassAfter::isPalindrome("Hello"));
    }
}

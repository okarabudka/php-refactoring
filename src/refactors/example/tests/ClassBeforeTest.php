<?php

namespace src\refactors\example\tests;

use PHPUnit\Framework\TestCase;
use src\refactors\example\before\ClassBefore;

class ClassBeforeTest extends TestCase
{
    public function testPalindrome()
    {
        $this->assertTrue(ClassBefore::isPalindrome("A man a plan a canal Panama"));
        $this->assertFalse(ClassBefore::isPalindrome("Hello"));
    }
}

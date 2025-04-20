<?php

namespace src\refactors\example\after;

class ClassAfter
{
    public static function isPalindrome(string $string): bool
    {
        $normalized = strtolower(str_replace(' ', '', $string));
        return $normalized === strrev($normalized);
    }
}

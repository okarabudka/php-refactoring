<?php

namespace src\refactors\example\before;

class ClassBefore
{
    public static function isPalindrome($string)
    {
        $string = strtolower(str_replace(' ', '', $string));
        $reversed = strrev($string);
        if ($reversed === $string) {
            return true;
        } else {
            return false;
        }
    }
}

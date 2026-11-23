<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task2\Before;

use Exception;

class ShippingCalculator
{
    public function calculate($type, $weight)
    {
        if ($type === 'standard') {
            return $weight * 1.2;
        } elseif ($type === 'express') {
            return $weight * 2.5 + 10;
        } elseif ($type === 'overnight') {
            return $weight * 5 + 20;
        } else {
            throw new Exception('Unknown shipping type');
        }
    }
}

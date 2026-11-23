<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task8\Before;

class Checkout
{
    // $lines: [['price' => 19.99, 'qty' => 2, 'currency' => 'EUR'], ...]
    public function total($lines)
    {
        $total = 0;
        foreach ($lines as $line) {
            $total += $line['price'] * $line['qty'];
        }
        return $total;
    }

    public function format($amount, $currency)
    {
        if ($currency == 'EUR') {
            return number_format($amount, 2, ',', '.') . ' €';
        }
        return '$' . number_format($amount, 2, '.', ',');
    }

    public function split($amount, $parts)
    {
        $share = round($amount / $parts, 2);
        return array_fill(0, $parts, $share);
    }
}

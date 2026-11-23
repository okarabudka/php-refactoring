<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task7\Before;

class DiscountCalculator
{
    public function calculate($customer, $amount, $isBlackFriday, $coupon = null)
    {
        $discount = 0;
        if ($customer['active']) {
            if ($customer['type'] == 'vip') {
                if ($amount > 100) {
                    $discount = 0.2;
                } else {
                    $discount = 0.1;
                }
            } else {
                if ($customer['years'] > 5) {
                    $discount = 0.05;
                }
            }
            if ($isBlackFriday) {
                $discount = $discount + 0.1;
            }
            if ($coupon != null) {
                if ($coupon == 'WELCOME10') {
                    if ($customer['orders'] == 0) {
                        $discount = $discount + 0.1;
                    }
                }
            }
            if ($discount > 0.3) {
                $discount = 0.3;
            }
        }
        return round($amount - $amount * $discount, 2);
    }
}

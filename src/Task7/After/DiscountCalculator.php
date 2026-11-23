<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task7\After;

/**
 * Discounts are whole percentages, so adding and capping them involves no float arithmetic.
 */
final class DiscountCalculator
{
    public const string WELCOME_COUPON = 'WELCOME10';

    private const int MAX_PERCENT = 30;
    private const int VIP_LARGE_ORDER_PERCENT = 20;
    private const int VIP_PERCENT = 10;
    private const float VIP_LARGE_ORDER_ABOVE = 100.0;
    private const int LOYALTY_PERCENT = 5;
    private const int LOYALTY_YEARS_ABOVE = 5;
    private const int BLACK_FRIDAY_PERCENT = 10;
    private const int WELCOME_COUPON_PERCENT = 10;

    public function price(Customer $customer, float $amount, ?string $coupon = null): float
    {
        return $this->discounted($customer, $amount, $this->percent($customer, $amount, $coupon));
    }

    public function blackFridayPrice(Customer $customer, float $amount, ?string $coupon = null): float
    {
        $percent = $this->percent($customer, $amount, $coupon) + self::BLACK_FRIDAY_PERCENT;

        return $this->discounted($customer, $amount, $percent);
    }

    private function percent(Customer $customer, float $amount, ?string $coupon): int
    {
        return $this->tierPercent($customer, $amount) + $this->couponPercent($customer, $coupon);
    }

    private function tierPercent(Customer $customer, float $amount): int
    {
        if ($customer->tier === CustomerTier::Vip) {
            return $amount > self::VIP_LARGE_ORDER_ABOVE ? self::VIP_LARGE_ORDER_PERCENT : self::VIP_PERCENT;
        }

        return $customer->yearsAsCustomer > self::LOYALTY_YEARS_ABOVE ? self::LOYALTY_PERCENT : 0;
    }

    private function couponPercent(Customer $customer, ?string $coupon): int
    {
        return $coupon === self::WELCOME_COUPON && $customer->isFirstOrder() ? self::WELCOME_COUPON_PERCENT : 0;
    }

    private function discounted(Customer $customer, float $amount, int $percent): float
    {
        if (!$customer->active) {
            return round($amount, 2);
        }

        return round($amount - $amount * min($percent, self::MAX_PERCENT) / 100, 2);
    }
}

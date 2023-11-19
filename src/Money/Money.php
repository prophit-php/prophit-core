<?php

namespace Prophit\Core\Money;

use Brick\Money\Money as BrickMoney;

class Money
{
    private BrickMoney $money;

    public function __construct(
        private int $amount,
        private string $currency,
    ) {
        $this->money = BrickMoney::of($amount, $currency);
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function isEqualTo(self $other): bool
    {
        return $this->money->isEqualTo($other->money);
    }

    public function isGreaterThan(self $other): bool
    {
        return $this->money->isGreaterThan($other->money);
    }

    public function isGreaterThanOrEqualTo(self $other): bool
    {
        return $this->money->isGreaterThanOrEqualTo($other->money);
    }

    public function isLessThanOrEqualTo(self $other): bool
    {
        return $this->money->isLessThanOrEqualTo($other->money);
    }
}

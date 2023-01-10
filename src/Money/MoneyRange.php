<?php

namespace Prophit\Core\Money;

use Brick\Money\Money;

class MoneyRange
{
    private Money $minimumInclusive;

    private Money $maximumInclusive;

    public function __construct(
        Money $minimumInclusive,
        Money $maximumInclusive,
    ) {
        $swap = $minimumInclusive->isGreaterThan($maximumInclusive);
        $this->minimumInclusive = $swap ? $maximumInclusive : $minimumInclusive;
        $this->maximumInclusive = $swap ? $minimumInclusive : $maximumInclusive;
    }

    public function getMinimumInclusive(): Money
    {
        return $this->minimumInclusive;
    }

    public function getMaximumInclusive(): Money
    {
        return $this->maximumInclusive;
    }

    public function contains(Money $money): bool
    {
        return $money->isGreaterThanOrEqualTo($this->minimumInclusive)
            && $money->isLessThanOrEqualTo($this->maximumInclusive);
    }
}

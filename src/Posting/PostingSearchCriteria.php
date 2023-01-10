<?php

namespace Prophit\Core\Posting;

use Brick\Money\Money;

use DateTimeInterface;

use Prophit\Core\{
    Account\AccountIterator,
    Date\DateRange,
    Money\MoneyRange,
};

class PostingSearchCriteria
{
    /**
     * @param string[]|null $ids
     */
    public function __construct(
        private ?array $ids = null,
        private ?AccountIterator $accounts = null,
        private Money|MoneyRange|null $amounts = null,
        private DateTimeInterface|DateRange|null $createdDates = null,
        private DateTimeInterface|DateRange|null $clearedDates = null,
    ) { }

    /**
     * @return string[]|null
     */
    public function getIds(): ?array
    {
        return $this->ids;
    }

    public function getAccounts(): ?AccountIterator
    {
        return $this->accounts;
    }

    public function getAmounts(): Money|MoneyRange|null
    {
        return $this->amounts;
    }

    public function getCreatedDates(): DateTimeInterface|DateRange|null
    {
        return $this->createdDates;
    }

    public function getClearedDates(): DateTimeInterface|DateRange|null
    {
        return $this->clearedDates;
    }
}

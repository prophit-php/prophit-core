<?php

namespace Prophit\Core\Posting;

use DateTimeInterface;

use Prophit\Core\{
    Date\DateRange,
    Money\Money,
    Money\MoneyRange,
};

class PostingSearchCriteria
{
    /**
     * @param string[]|null $ids
     * @param string[]|null $accountIds
     */
    public function __construct(
        private ?array $ids = null,
        private ?array $accountIds = null,
        private Money|MoneyRange|null $amounts = null,
        private DateTimeInterface|DateRange|null $clearedDates = null,
    ) { }

    /**
     * @return string[]|null
     */
    public function getIds(): ?array
    {
        return $this->ids;
    }

    /**
     * @return string[]|null
     */
    public function getAccountIds(): ?array
    {
        return $this->accountIds;
    }

    public function getAmounts(): Money|MoneyRange|null
    {
        return $this->amounts;
    }

    public function getClearedDates(): DateTimeInterface|DateRange|null
    {
        return $this->clearedDates;
    }
}

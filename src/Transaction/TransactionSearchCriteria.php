<?php

namespace Prophit\Core\Transaction;

use Prophit\Core\{
    Account\Account,
    Date\DateRange,
    Money\Money,
    Money\MoneyRange,
};

use DateTimeInterface;

class TransactionSearchCriteria
{
    /**
     * @param string[]|null $ids
     * @param Account[]|null $accounts
     */
    public function __construct(
        private ?array $ids = null,
        private DateTimeInterface|DateRange|null $transactionDates = null,
        private ?string $description = null,
        private ?array $accounts = null,
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

    public function getTransactionDates(): DateTimeInterface|DateRange|null
    {
        return $this->transactionDates;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return Account[]|null
     */
    public function getAccounts(): ?array
    {
        return $this->accounts;
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

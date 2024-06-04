<?php

namespace Prophit\Core\Transaction;

use Prophit\Core\{
    Account\Account,
    Date\DateRange,
    Ledger\Ledger,
    Money\Money,
    Money\MoneyRange,
    Transaction\PostingStatus,
    Transaction\TransactionStatus,
};

use DateTimeInterface;

class TransactionSearchCriteria
{
    /**
     * @param string[]|null $ids
     * @param Ledger[]|null $ledgers
     * @param Account[]|null $accounts
     * @param PostingStatus[]|null $postingStatuses
     * @param TransactionStatus[]|null $transactionStatuses
     */
    public function __construct(
        private ?array $ids = null,
        private DateTimeInterface|DateRange|null $transactionDates = null,
        private ?string $description = null,
        private ?array $accounts = null,
        private Money|MoneyRange|null $amounts = null,
        private DateTimeInterface|DateRange|null $clearedDates = null,
        private ?array $postingStatuses = null,
        private ?array $transactionStatuses = null,
        private ?array $ledgers = null,
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

    /**
     * @return PostingStatus[]|null
     */
    public function getPostingStatuses(): ?array
    {
        return $this->postingStatuses;
    }

    /**
     * @return TransactionStatus[]|null
     */
    public function getTransactionStatuses(): ?array
    {
        return $this->transactionStatuses;
    }

    /**
     * @return Ledger[]|null
     */
    public function getLedgers(): ?array
    {
        return $this->ledgers;
    }

    public function hasCriteria(): bool
    {
        return !(
            $this->ids === null &&
            $this->transactionDates === null &&
            $this->description === null &&
            $this->accounts === null &&
            $this->amounts === null &&
            $this->clearedDates === null &&
            $this->postingStatuses === null &&
            $this->transactionStatuses === null &&
            $this->ledgers === null
        );
    }
}

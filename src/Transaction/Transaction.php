<?php

namespace Prophit\Core\Transaction;

use DateTimeInterface;
use Prophit\Core\Ledger\Ledger;

class Transaction
{
    private Ledger $ledger;

    /**
     * @param Posting[] $postings
     */
    public function __construct(
        private string $id,
        private DateTimeInterface $transactionDate,
        private TransactionStatus $status,
        private array $postings,
        private ?string $description = null,
    ) {
        $firstPosting = reset($postings);
        $this->ledger = $firstPosting->getAccount()->getLedger();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTransactionDate(): DateTimeInterface
    {
        return $this->transactionDate;
    }

    public function getStatus(): TransactionStatus
    {
        return $this->status;
    }

    public function isActive(): bool
    {
        return $this->status === TransactionStatus::Active;
    }

    public function isDeleted(): bool
    {
        return $this->status === TransactionStatus::Deleted;
    }

    /**
     * @return Posting[]
     */
    public function getPostings(): array
    {
        return $this->postings;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getLedger(): Ledger
    {
        return $this->ledger;
    }
}

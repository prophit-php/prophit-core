<?php

namespace Prophit\Core\Transaction;

use DateTimeInterface;

class Transaction
{
    /**
     * @param Posting[] $postings
     */
    public function __construct(
        private string $id,
        private DateTimeInterface $transactionDate,
        private TransactionStatus $status,
        private array $postings,
        private ?string $description = null,
    ) { }

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
}

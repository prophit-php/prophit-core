<?php

namespace Prophit\Core\Transaction;

use DateTimeInterface;

use Prophit\Core\{
    Account\Account,
    Money\Money,
};

class Posting
{
    public function __construct(
        private string $id,
        private Account $account,
        private Money $amount,
        private PostingStatus $status,
        private ?DateTimeInterface $clearedDate = null,
    ) { }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getStatus(): PostingStatus
    {
        return $this->status;
    }

    public function isActive(): bool
    {
        return $this->status === PostingStatus::Active;
    }

    public function isDeleted(): bool
    {
        return $this->status === PostingStatus::Deleted;
    }

    public function getClearedDate(): ?DateTimeInterface
    {
        return $this->clearedDate;
    }

    public function hasCleared(): bool
    {
        return $this->clearedDate !== null;
    }
}

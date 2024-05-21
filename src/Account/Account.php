<?php

namespace Prophit\Core\Account;

use Prophit\Core\Ledger\Ledger;

class Account
{
    public function __construct(
        private string $id,
        private Ledger $ledger,
        private string $name,
        private string $currency,
        private AccountStatus $status,
    ) { }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLedger(): Ledger
    {
        return $this->ledger;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getStatus(): AccountStatus
    {
        return $this->status;
    }

    public function isActive(): bool
    {
        return $this->status === AccountStatus::Active;
    }

    public function isDeleted(): bool
    {
        return $this->status === AccountStatus::Deleted;
    }

    public function isSame(self $account): bool
    {
        return $this->id === $account->id;
    }
}

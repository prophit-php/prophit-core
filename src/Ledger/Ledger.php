<?php

namespace Prophit\Core\Ledger;

class Ledger
{
    public function __construct(
        private string $id,
        private string $name,
        private LedgerStatus $status,
    ) { }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): LedgerStatus
    {
        return $this->status;
    }

    public function isActive(): bool
    {
        return $this->status === LedgerStatus::Active;
    }

    public function isDeleted(): bool
    {
        return $this->status === LedgerStatus::Deleted;
    }

    public function isLocked(): bool
    {
        return $this->status === LedgerStatus::Locked;
    }

    public function isSame(self $ledger): bool
    {
        return $this->id === $ledger->id;
    }
}

<?php

namespace Prophit\Core\Posting;

use Brick\Money\Money;
use DateTimeInterface;
use Prophit\Core\Account\Account;

class Posting
{
    public function __construct(
        private string $id,
        private Account $account,
        private Money $amount,
        private DateTimeInterface $createdDate,
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

    public function getCreatedDate(): DateTimeInterface
    {
        return $this->createdDate;
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

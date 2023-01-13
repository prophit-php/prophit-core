<?php

namespace Prophit\Core\Posting;

use DateTimeInterface;

use Prophit\Core\{
    Account\Account,
    Money\Money,
    User\User,
};

class Posting
{
    public function __construct(
        private string $id,
        private Account $account,
        private Money $amount,
        private DateTimeInterface $modifiedDate,
        private User $modifiedUser,
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

    public function getModifiedDate(): DateTimeInterface
    {
        return $this->modifiedDate;
    }

    public function getModifiedUser(): User
    {
        return $this->modifiedUser;
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

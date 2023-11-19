<?php

namespace Prophit\Core\Account;

use DateTimeInterface;

use Prophit\Core\User\User;

class Account
{
    public function __construct(
        private string $id,
        private string $name,
        private string $currency,
    ) { }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function isSame(self $account): bool
    {
        return $this->id === $account->id;
    }
}

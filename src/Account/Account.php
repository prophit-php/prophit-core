<?php

namespace Prophit\Core\Account;

use DateTimeInterface;

class Account
{
    public function __construct(
        private string $id,
        private string $name,
        private DateTimeInterface $modifiedDate,
    ) { }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getModifiedDate(): DateTimeInterface
    {
        return $this->modifiedDate;
    }

    public function isSame(self $account): bool
    {
        return $this->id === $account->id;
    }
}

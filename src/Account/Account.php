<?php

namespace Prophit\Core\Account;

class Account
{
    public function __construct(
        private string $id,
        private string $name,
    ) { }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isSame(self $account): bool
    {
        return $this->id === $account->id;
    }
}

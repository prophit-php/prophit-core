<?php

namespace Prophit\Core\Account;

class Account
{
    public function __construct(
        private string $id,
        private string $name,
        private ?string $parentId = null,
    ) { }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function hasParent(): bool
    {
        return $this->parentId !== null;
    }
}

<?php

namespace Prophit\Core\User;

class SimpleUser implements User
{
    public function __construct(
        private string $id,
        private string $displayName,
    ) { }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }
}

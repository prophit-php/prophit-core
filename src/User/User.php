<?php

namespace Prophit\Core\User;

class User
{
    public function __construct(
        private string $id,
        private string $displayName,
        private UserStatus $status,
    ) { }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active;
    }

    public function isDeleted(): bool
    {
        return $this->status === UserStatus::Deleted;
    }

    public function isLocked(): bool
    {
        return $this->status === UserStatus::Locked;
    }
}

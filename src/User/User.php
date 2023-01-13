<?php

namespace Prophit\Core\User;

interface User
{
    public function getId(): string;

    public function getDisplayName(): string;
}

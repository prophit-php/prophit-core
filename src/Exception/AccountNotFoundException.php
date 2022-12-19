<?php

namespace Prophit\Core\Exception;

class AccountNotFoundException extends CoreException
{
    public function __construct(string $id)
    {
        parent::__construct('Account not found: ' . $id);
    }
}

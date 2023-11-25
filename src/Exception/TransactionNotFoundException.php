<?php

namespace Prophit\Core\Exception;

class TransactionNotFoundException extends CoreException
{
    public function __construct(string $id)
    {
        parent::__construct('Transaction not found: ' . $id);
    }
}

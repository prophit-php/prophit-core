<?php

namespace Prophit\Core\Exception;

class LedgerNotFoundException extends CoreException
{
    public function __construct(string $id)
    {
        parent::__construct('Ledger not found: ' . $id);
    }
}

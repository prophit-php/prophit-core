<?php

namespace Prophit\Core\Exception;

use Prophit\Core\Ledger\Ledger;

class TransactionNotFoundException extends CoreException
{
    public function __construct(Ledger $ledger, string $id)
    {
        parent::__construct('Transaction ' . $id . ' not found in ledger ' . $ledger->getId());
    }
}

<?php

namespace Prophit\Core\Transaction;

use Prophit\Core\{
    Ledger\Ledger,
    ProphitException,
};

class TransactionException extends ProphitException
{
    const CODE_TRANSACTION_NOT_FOUND = 1;

    private function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function transactionNotFound(Ledger $ledger, string $id)
    {
        return new self(
            'Transaction not found in ledger ' . $ledger->getId() . ': ' . $id,
            self::CODE_TRANSACTION_NOT_FOUND,
        );
    }
}

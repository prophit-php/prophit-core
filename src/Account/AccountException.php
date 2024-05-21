<?php

namespace Prophit\Core\Account;

use Prophit\Core\{
    Ledger\Ledger,
    ProphitException,
};

class AccountException extends ProphitException
{
    const CODE_ACCOUNT_NOT_FOUND = 1;

    private function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function accountNotFound(Ledger $ledger, string $id)
    {
        return new self(
            'Account not found in ledger ' . $ledger->getId() . ': ' . $id,
            self::CODE_ACCOUNT_NOT_FOUND,
        );
    }
}

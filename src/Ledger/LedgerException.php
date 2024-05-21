<?php

namespace Prophit\Core\Ledger;

use Prophit\Core\ProphitException;

class LedgerException extends ProphitException
{
    const CODE_LEDGER_NOT_FOUND = 1;

    private function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function ledgerNotFound(string $id)
    {
        return new self(
            'Ledger not found: ' . $id,
            self::CODE_LEDGER_NOT_FOUND,
        );
    }
}

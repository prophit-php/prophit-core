<?php

namespace Prophit\Core\Ledger;

use Prophit\Core\Exception\LedgerNotFoundException;

interface LedgerRepository
{
    public function saveLedger(Ledger $ledger): void;

    /**
     * @throws LedgerNotFoundException if ledger is not found
     */
    public function getLedgerById(string $id): Ledger;

    /**
     * @return iterable<Ledger>
     */
    public function getAllLedgers(): iterable;
}

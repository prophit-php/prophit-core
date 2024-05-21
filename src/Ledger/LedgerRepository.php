<?php

namespace Prophit\Core\Ledger;

interface LedgerRepository
{
    public function saveLedger(Ledger $ledger): void;

    /**
     * @throws LedgerException if ledger is not found
     */
    public function getLedgerById(string $id): Ledger;

    /**
     * @return iterable<Ledger>
     */
    public function getAllLedgers(): iterable;
}

<?php

namespace Prophit\Core\Ledger;

class ArrayLedgerRepository implements LedgerRepository
{
    /** @var array<string, Ledger> **/
    private array $ledgers;

    public function __construct(Ledger... $ledgers)
    {
        $this->ledgers = [];
        foreach ($ledgers as $ledger) {
            $this->saveLedger($ledger);
        }
    }

    public function saveLedger(Ledger $ledger): void
    {
        $this->ledgers[$ledger->getId()] = $ledger;
    }

    public function getLedgerById(string $id): Ledger
    {
        if (!isset($this->ledgers[$id])) {
            throw LedgerException::ledgerNotFound($id);
        }
        return $this->ledgers[$id];
    }

    public function getAllLedgers(): iterable
    {
        foreach ($this->ledgers as $ledger) {
            yield $ledger;
        }
    }
}

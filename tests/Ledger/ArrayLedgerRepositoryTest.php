<?php

use Prophit\Core\{
    Ledger\Ledger,
    Ledger\ArrayLedgerRepository,
    Exception\LedgerNotFoundException,
    Tests\Ledger\LedgerFactory,
};

beforeEach(function () {
    $this->factory = new LedgerFactory;
});

it('saves ledger', function () {
    $ledger = $this->factory->create();
    $repository = new ArrayLedgerRepository;
    $repository->saveLedger($ledger);

    $expectedLedger = $ledger;
    $actualLedger = $repository->getLedgerById($ledger->getId());
    expect($expectedLedger)->toBe($actualLedger);
});

it('gets existing ledger by ID', function () {
    [$foundLedger, $notFoundLedger] = $ledgers = $this->factory->count(2);
    $repository = new ArrayLedgerRepository(...$ledgers);

    $expectedLedger = $foundLedger;
    $actualLedger = $repository->getLedgerById($expectedLedger->getId());
    expect($expectedLedger)->toBe($actualLedger);
});

it('does not get nonexistent ledger by ID', function () {
    $notFoundLedger = $this->factory->create();
    $repository = new ArrayLedgerRepository($notFoundLedger);
    $repository->getLedgerById('-1');
})->throws(LedgerNotFoundException::class);

it('gets all ledgers', function () {
    $ledgers = $this->factory->count(2);
    $repository = new ArrayLedgerRepository(...$ledgers);

    $expectedLedgers = $ledgers;
    $actualLedgers = iterator_to_array($repository->getAllLedgers());
    expect($expectedLedgers)->toBe($actualLedgers);
});

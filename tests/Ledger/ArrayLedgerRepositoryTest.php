<?php

use Prophit\Core\{
    Ledger\ArrayLedgerRepository,
    Tests\Ledger\LedgerRepositoryTestFactory,
};

LedgerRepositoryTestFactory::createTests(ArrayLedgerRepository::class);

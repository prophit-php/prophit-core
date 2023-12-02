<?php

use Prophit\Core\{
    Ledger\Ledger,
    Ledger\LedgerStatus,
    Tests\Ledger\LedgerFactory,
};

beforeEach(function () {
    $this->factory = new LedgerFactory;
});

it('gets ID', function () {
    $id = '1';
    $ledger = $this->factory->create(id: $id);
    expect($ledger->getId())->toBe($id);
});

it('gets name', function () {
    $name = 'Test';
    $ledger = $this->factory->create(name: $name);
    expect($ledger->getName())->toBe($name);
});

it('gets status', function () {
    $ledger = $this->factory->create();
    expect($ledger->getStatus())->toBe(LedgerStatus::Active);
});

it('is active', function () {
    $ledger = $this->factory->create();
    expect($ledger->isActive())->toBe(true);
    expect($ledger->isDeleted())->toBe(false);
    expect($ledger->isLocked())->toBe(false);
});

it('is deleted', function () {
    $ledger = $this->factory->create(status: LedgerStatus::Deleted);
    expect($ledger->isActive())->toBe(false);
    expect($ledger->isDeleted())->toBe(true);
    expect($ledger->isLocked())->toBe(false);
});

it('is locked', function () {
    $ledger = $this->factory->create(status: LedgerStatus::Locked);
    expect($ledger->isActive())->toBe(false);
    expect($ledger->isDeleted())->toBe(false);
    expect($ledger->isLocked())->toBe(true);
});

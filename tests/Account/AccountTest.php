<?php

use Prophit\Core\{
    Account\Account,
    Account\AccountStatus,
    Tests\Account\AccountFactory,
    Tests\Ledger\LedgerFactory,
    Tests\User\UserFactory,
    User\SimpleUser,
};

beforeEach(function () {
    $this->factory = new AccountFactory;
});

it('gets ID', function () {
    $id = '1';
    $account = $this->factory->create(id: $id);
    expect($account->getId())->toBe($id);
});

it('gets ledger', function () {
    $ledger = (new LedgerFactory)->create();
    $account = $this->factory->create(ledger: $ledger);
    expect($account->getLedger())->toBe($ledger);
});

it('gets name', function () {
    $name = 'Test';
    $account = $this->factory->create(name: $name);
    expect($account->getName())->toBe($name);
});

it('gets currency', function () {
    $currency = 'USD';
    $account = $this->factory->create(currency: $currency);
    expect($account->getCurrency())->toBe($currency);
});

it('gets status', function () {
    $account = $this->factory->create();
    expect($account->getStatus())->toBe(AccountStatus::Active);
});

it('is active', function () {
    $account = $this->factory->create();
    expect($account->isActive())->toBe(true);
    expect($account->isDeleted())->toBe(false);
});

it('is deleted', function () {
    $account = $this->factory->create(status: AccountStatus::Deleted);
    expect($account->isActive())->toBe(false);
    expect($account->isDeleted())->toBe(true);
});

it('is same', function () {
    $account = $this->factory->create();
    expect($account->isSame($account))->toBeTrue();
});

it('is not same', function () {
    [$account, $otherAccount] = $this->factory->count(2);
    expect($account->isSame($otherAccount))->toBeFalse();
});

<?php

use Prophit\Core\{
    Account\Account,
    Tests\Account\AccountFactory,
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

it('is same', function () {
    $account = $this->factory->create();
    expect($account->isSame($account))->toBeTrue();
});

it('is not same', function () {
    [$account, $otherAccount] = $this->factory->count(2);
    expect($account->isSame($otherAccount))->toBeFalse();
});

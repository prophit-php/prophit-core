<?php

use Prophit\Core\Account\{
    Account,
    AccountIterator,
};

use Prophit\Core\Tests\Account\AccountFactory;

beforeEach(function () {
    $this->factory = new AccountFactory;
});

it('iterates', function () {
    $accounts = $this->factory->count(2);
    $iterator = new AccountIterator(...$accounts);
    expect(iterator_to_array($iterator))->toBe($accounts);
});

it('contains account', function () {
    $containedAccount = $this->factory->create();
    $iterator = new AccountIterator($containedAccount);
    expect($iterator->contains($containedAccount))->toBeTrue();
});

it('does not contain account', function () {
    $containedAccount = $this->factory->create();
    $iterator = new AccountIterator($containedAccount);
    $uncontainedAccount = $this->factory->create();
    expect($iterator->contains($uncontainedAccount))->toBeFalse();
});

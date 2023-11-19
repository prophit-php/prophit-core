<?php

use Prophit\Core\{
    Money\Money,
    Posting\Posting,
    Tests\Account\AccountFactory,
    Tests\Posting\PostingFactory,
};

beforeEach(function () {
    $this->factory = new PostingFactory;
});

it('gets ID', function () {
    $id = '1';
    $posting = $this->factory->create(id: $id);
    expect($posting->getId())->toBe($id);
});

it('gets account', function () {
    $account = (new AccountFactory)->create();
    $posting = $this->factory->create(account: $account);
    expect($posting->getAccount())->toBe($account);
});

it('gets amount', function () {
    $amount = new Money(100, 'USD');
    $posting = $this->factory->create(amount: $amount);
    expect($posting->getAmount())->toBe($amount);
});

it('gets cleared date', function () {
    $clearedDate = new DateTime;
    $posting = $this->factory->create(clearedDate: $clearedDate);
    expect($posting->getClearedDate())->toBe($clearedDate);
});

it('has cleared', function () {
    $clearedDate = new DateTime;
    $posting = $this->factory->create(clearedDate: $clearedDate);
    expect($posting->hasCleared())->toBeTrue();
});

it('has not cleared', function () {
    $posting = $this->factory->create();
    expect($posting->hasCleared())->toBeFalse();
});

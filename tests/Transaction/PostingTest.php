<?php

use Prophit\Core\{
    Money\Money,
    Tests\Account\AccountFactory,
    Transaction\Posting,
};

beforeEach(function () {
    $this->account = (new AccountFactory)->create();
    $this->amount = new Money(1, 'USD');
    $this->clearedDate = new DateTime('2023-11-24');
    $this->posting = new Posting(
        '1',
        $this->account,
        $this->amount,
        $this->clearedDate,
    );
});

it('gets ID', function () {
    expect($this->posting->getId())->toBe('1');
});

it('gets account', function () {
    expect($this->posting->getAccount())->toBe($this->account);
});

it('gets amount', function () {
    expect($this->posting->getAmount())->toBe($this->amount);
});

it('gets cleared date', function () {
    expect($this->posting->getClearedDate())->toBe($this->clearedDate);
});

it('has cleared', function () {
    expect($this->posting->hasCleared())->toBe(true);
});

it('has not cleared', function () {
    $posting = new Posting(
        '1',
        $this->account,
        $this->amount,
    );
    expect($posting->hasCleared())->toBe(false);
});

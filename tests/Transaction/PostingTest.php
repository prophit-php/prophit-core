<?php

use Prophit\Core\{
    Money\Money,
    Tests\Account\AccountFactory,
    Transaction\Posting,
    Transaction\PostingStatus,
};

beforeEach(function () {
    $this->account = (new AccountFactory)->create();
    $this->amount = new Money(1, 'USD');
    $this->clearedDate = new DateTime('2023-11-24');
    $this->posting = new Posting(
        '1',
        $this->account,
        $this->amount,
        PostingStatus::Active,
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

it('gets status', function () {
    expect($this->posting->getStatus())->toBe(PostingStatus::Active);
});

it('is active', function () {
    expect($this->posting->isActive())->toBe(true);
    expect($this->posting->isDeleted())->toBe(false);
});

it('is deleted', function () {
    $posting = new Posting(
        $this->posting->getId(),
        $this->posting->getAccount(),
        $this->posting->getAmount(),
        PostingStatus::Deleted,
        $this->posting->getClearedDate(),
    );
    expect($posting->isActive())->toBe(false);
    expect($posting->isDeleted())->toBe(true);
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
        PostingStatus::Active,
    );
    expect($posting->hasCleared())->toBe(false);
});

<?php

use Prophit\Core\{
    Transaction\Posting,
    Transaction\PostingFactory,
    Transaction\Transaction,
    Transaction\TransactionStatus,
    Tests\Transaction\TransactionFactory,
};

beforeEach(function () {
    $this->transactionFactory = new TransactionFactory;
});

it('gets ID', function () {
    $transaction = $this->transactionFactory->create(id: '1');
    expect($transaction->getId())->toBe('1');
});

it('gets transaction date', function () {
    $transactionDate = new DateTime;
    $transaction = $this->transactionFactory->create(transactionDate: $transactionDate);
    expect($transaction->getTransactionDate())->toBe($transactionDate);
});

it('gets status', function () {
    $transaction = $this->transactionFactory->create();
    expect($transaction->getStatus())->toBe(TransactionStatus::Active);
});

it('is active', function () {
    $transaction = $this->transactionFactory->create();
    expect($transaction->isActive())->toBe(true);
    expect($transaction->isDeleted())->toBe(false);
});

it('is deleted', function () {
    $transaction = $this->transactionFactory->create(status: TransactionStatus::Deleted);
    expect($transaction->isActive())->toBe(false);
    expect($transaction->isDeleted())->toBe(true);
});

it('gets postings', function () {
    $transaction = $this->transactionFactory->create();
    expect($transaction->getPostings())
        ->toBeArray()
        ->toContainOnlyInstancesOf(Posting::class);
});

it('gets description', function () {
    $transaction = $this->transactionFactory->create(description: 'foo');
    expect($transaction->getDescription())->toBe('foo');
});

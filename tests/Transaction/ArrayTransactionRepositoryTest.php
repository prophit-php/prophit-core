<?php

use Prophit\Core\{
    Date\DateRange,
    Exception\TransactionNotFoundException,
    Money\Money,
    Money\MoneyRange,
    Transaction\ArrayTransactionRepository,
    Transaction\Transaction,
    Transaction\TransactionSearchCriteria,
    Tests\Transaction\PostingFactory,
    Tests\Transaction\TransactionFactory,
};

beforeEach(function () {
    $this->postingFactory = new PostingFactory;
    $this->transactionFactory = new TransactionFactory;
});

it('saves transaction', function () {
    $transaction = $this->transactionFactory->create();
    $repository = new ArrayTransactionRepository;
    $repository->saveTransaction($transaction);

    $expectedTransaction = $transaction;
    $actualTransaction = $repository->getTransactionById($transaction->getId());
    expect($expectedTransaction)->toBe($actualTransaction);
});

it('gets existing transaction by ID', function () {
    [$foundTransaction, $notFoundTransaction] = $transactions = $this->transactionFactory->count(2);
    $repository = new ArrayTransactionRepository(...$transactions);

    $expectedTransaction = $foundTransaction;
    $actualTransaction = $repository->getTransactionById($expectedTransaction->getId());
    expect($expectedTransaction)->toBe($actualTransaction);
});

it('does not get nonexistent transaction by ID', function () {
    $notFoundTransaction = $this->transactionFactory->create();
    $repository = new ArrayTransactionRepository($notFoundTransaction);
    $repository->getTransactionById('-1');
})->throws(TransactionNotFoundException::class);

it('searches transactions by IDs', function () {
    $transactions = $this->transactionFactory->count(4);
    $repository = new ArrayTransactionRepository(...$transactions);

    $expectedTransactions = array_slice($transactions, 0, 2);
    $criteria = new TransactionSearchCriteria(
        ids: array_map(
            fn(Transaction $transaction): string => $transaction->getId(),
            $expectedTransactions,
        ),
    );
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($criteria),
    );
    expect($expectedTransactions)->toBe($actualTransactions);
});

it('searches transactions by transaction date using date', function () {
    $transactions = [
        $this->transactionFactory->create(transactionDates: new DateTime('-1 day')),
        $this->transactionFactory->create(transactionDates: new DateTime('+1 day')),
    ];
    $repository = new ArrayTransactionRepository(...$transactions);

    $expectedTransactions = array_slice($transactions, 0, 1);
    $criteria = new TransactionSearchCriteria(
        transactionDates: $transactions[0]->getTransactionDate(),
    );
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($criteria),
    );
    expect($expectedTransactions)->toBe($actualTransactions);
});

it('searches transactions by transaction date using date range', function () {
    $transactions = [
        $this->transactionFactory->create(transactionDates: new DateTime('-2 days')),
        $this->transactionFactory->create(transactionDates: new DateTime('-1 day')),
        $this->transactionFactory->create(transactionDates: new DateTime('+1 day')),
        $this->transactionFactory->create(transactionDates: new DateTime('+2 days')),
    ];
    $repository = new ArrayTransactionRepository(...$transactions);

    $expectedTransactions = array_slice($transactions, 0, 2);
    $criteria = new TransactionSearchCriteria(
        transactionDates: new DateRange(
            new DateTime('-2 days'),
            new DateTime('-1 day'),
        ),
    );
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($criteria),
    );
    expect($expectedTransactions)->toBe($actualTransactions);
});

it('searches transactions by description', function () {
    $transactions = [
        $this->transactionFactory->create(description: 'foo'),
        $this->transactionFactory->create(description: 'foobar'),
        $this->transactionFactory->create(description: 'bar'),
        $this->transactionFactory->create(description: 'baz'),
    ];
    $repository = new ArrayTransactionRepository(...$transactions);

    $expectedTransactions = array_slice($transactions, 0, 2);
    $criteria = new TransactionSearchCriteria(
        description: 'foo',
    );
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($criteria),
    );
    expect($expectedTransactions)->toBe($actualTransactions);
});

it('searches transactions by account', function () {
    $transactions = $this->transactionFactory->count(4);
    $repository = new ArrayTransactionRepository(...$transactions);

    $expectedTransactions = array_slice($transactions, 0, 2);
    $criteria = new TransactionSearchCriteria(
        accounts: [
            $transactions[0]->getPostings()[0]->getAccount(),
            $transactions[1]->getPostings()[0]->getAccount(),
        ],
    );
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($criteria),
    );
    expect($expectedTransactions)->toBe($actualTransactions);
});

it('searches transactions by amount', function () {
    $transactions = $this->transactionFactory->count(2);
    $repository = new ArrayTransactionRepository(...$transactions);

    $expectedTransactions = array_slice($transactions, 0, 1);
    $criteria = new TransactionSearchCriteria(
        amounts: $transactions[0]->getPostings()[0]->getAmount(),
    );
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($criteria),
    );
    expect($expectedTransactions)->toBe($actualTransactions);
});

it('searches transactions by amount range', function () {
    $transactions = array_map(
        fn(int $amount): Transaction => $this->transactionFactory->create(
            postings: [
                $this->postingFactory->create(amount: new Money($amount, 'USD')),
                $this->postingFactory->create(amount: new Money($amount * -1, 'USD')),
            ],
        ),
        range(1, 4),
    );
    $repository = new ArrayTransactionRepository(...$transactions);

    $expectedTransactions = array_slice($transactions, 0, 2);
    $criteria = new TransactionSearchCriteria(
        amounts: new MoneyRange(
            new Money(1, 'USD'),
            new Money(2, 'USD'),
        ),
    );
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($criteria),
    );
    expect($expectedTransactions)->toBe($actualTransactions);
});

it('searches transactions by cleared date using date', function () {
    $transactions = array_map(
        fn(int $amount): Transaction => $this->transactionFactory->create(
            postings: [
                $this->postingFactory->create(clearedDate: new DateTime('+' . $amount . ' days')),
                $this->postingFactory->create(clearedDate: new DateTime('+' . $amount . ' days')),
            ],
        ),
        range(1, 2),
    );
    $repository = new ArrayTransactionRepository(...$transactions);

    $expectedTransactions = array_slice($transactions, 0, 1);
    $criteria = new TransactionSearchCriteria(
        clearedDates: $transactions[0]->getPostings()[0]->getClearedDate(),
    );
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($criteria),
    );
    expect($expectedTransactions)->toBe($actualTransactions);
});

it('searches transactions by cleared date using date range', function () {
    $transactions = array_map(
        fn(int $amount): Transaction => $this->transactionFactory->create(
            postings: [
                $this->postingFactory->create(clearedDate: new DateTime('+' . $amount . ' days')),
                $this->postingFactory->create(clearedDate: new DateTime('+' . $amount . ' days')),
            ],
        ),
        range(1, 4),
    );
    $repository = new ArrayTransactionRepository(...$transactions);

    $expectedTransactions = array_slice($transactions, 0, 2);
    $criteria = new TransactionSearchCriteria(
        clearedDates: new DateRange(
            $transactions[0]->getPostings()[0]->getClearedDate(),
            $transactions[1]->getPostings()[0]->getClearedDate(),
        ),
    );
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($criteria),
    );
    expect($expectedTransactions)->toBe($actualTransactions);
});

<?php

use Prophit\Core\{
    Date\DateRange,
    Exception\TransactionNotFoundException,
    Ledger\Ledger,
    Money\Money,
    Money\MoneyRange,
    Transaction\ArrayTransactionRepository,
    Transaction\PostingStatus,
    Transaction\TransactionStatus,
    Transaction\Transaction,
    Transaction\TransactionSearchCriteria,
    Tests\Ledger\LedgerFactory,
    Tests\Transaction\PostingFactory,
    Tests\Transaction\TransactionFactory,
};

/**
 * @param Transaction[] $transactions
 */
function getArrayTransactionRepository(
    Ledger $ledger,
    array $transactions,
): ArrayTransactionRepository {
    $repository = new ArrayTransactionRepository;
    foreach ($transactions as $transaction) {
        $repository->saveTransaction($ledger, $transaction);
    }
    return $repository;
}

beforeEach(function () {
    $this->ledger = (new LedgerFactory)->create();
    $this->postingFactory = new PostingFactory;
    $this->transactionFactory = new TransactionFactory;
});

it('saves transaction', function () {
    $transaction = $this->transactionFactory->create();
    $repository = getArrayTransactionRepository($this->ledger, [$transaction]);

    $expectedTransaction = $transaction;
    $actualTransaction = $repository->getTransactionById($this->ledger, $transaction->getId());
    expect($expectedTransaction)->toBe($actualTransaction);
});

it('gets existing transaction by ID', function () {
    [$foundTransaction, $notFoundTransaction] = $transactions = $this->transactionFactory->count(2);
    $repository = getArrayTransactionRepository($this->ledger, $transactions);

    $expectedTransaction = $foundTransaction;
    $actualTransaction = $repository->getTransactionById($this->ledger, $expectedTransaction->getId());
    expect($expectedTransaction)->toBe($actualTransaction);
});

it('does not get nonexistent transaction by ID', function () {
    $notFoundTransaction = $this->transactionFactory->create();
    $repository = getArrayTransactionRepository($this->ledger, [$notFoundTransaction]);
    $repository->getTransactionById($this->ledger, '-1');
})->throws(TransactionNotFoundException::class);

it('searches transactions without criteria', function () {
    $transactions = [$this->transactionFactory->create()];
    $repository = getArrayTransactionRepository($this->ledger, $transactions);
    $criteria = new TransactionSearchCriteria;

    $expectedTransactions = $transactions;
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($this->ledger, $criteria),
    );
    expect($expectedTransactions)->toBe($actualTransactions);
});

it('searches transactions by IDs', function () {
    $transactions = $this->transactionFactory->count(4);
    $repository = getArrayTransactionRepository($this->ledger, $transactions);

    $expectedTransactions = array_slice($transactions, 0, 2);
    $criteria = new TransactionSearchCriteria(
        ids: array_map(
            fn(Transaction $transaction): string => $transaction->getId(),
            $expectedTransactions,
        ),
    );
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($this->ledger, $criteria),
    );
    expect($expectedTransactions)->toBe($actualTransactions);
});

it('searches transactions by transaction date using date', function () {
    $transactions = [
        $this->transactionFactory->create(transactionDate: new DateTime('-1 day')),
        $this->transactionFactory->create(transactionDate: new DateTime('+1 day')),
    ];
    $repository = getArrayTransactionRepository($this->ledger, $transactions);

    $expectedTransactions = array_slice($transactions, 0, 1);
    $criteria = new TransactionSearchCriteria(
        transactionDates: $transactions[0]->getTransactionDate(),
    );
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($this->ledger, $criteria),
    );
    expect($expectedTransactions)->toBe($actualTransactions);
});

it('searches transactions by transaction date using date range', function () {
    $transactions = [
        $this->transactionFactory->create(transactionDate: new DateTime('-2 days')),
        $this->transactionFactory->create(transactionDate: new DateTime('-1 day')),
        $this->transactionFactory->create(transactionDate: new DateTime('+1 day')),
        $this->transactionFactory->create(transactionDate: new DateTime('+2 days')),
    ];
    $repository = getArrayTransactionRepository($this->ledger, $transactions);

    $expectedTransactions = array_slice($transactions, 0, 2);
    $criteria = new TransactionSearchCriteria(
        transactionDates: new DateRange(
            new DateTime('-2 days'),
            new DateTime('-1 day'),
        ),
    );
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($this->ledger, $criteria),
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
    $repository = getArrayTransactionRepository($this->ledger, $transactions);

    $expectedTransactions = array_slice($transactions, 0, 2);
    $criteria = new TransactionSearchCriteria(
        description: 'foo',
    );
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($this->ledger, $criteria),
    );
    expect($expectedTransactions)->toBe($actualTransactions);
});

it('searches transactions by account', function () {
    $transactions = $this->transactionFactory->count(4);
    $repository = getArrayTransactionRepository($this->ledger, $transactions);

    $expectedTransactions = array_slice($transactions, 0, 2);
    $criteria = new TransactionSearchCriteria(
        accounts: [
            $transactions[0]->getPostings()[0]->getAccount(),
            $transactions[1]->getPostings()[0]->getAccount(),
        ],
    );
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($this->ledger, $criteria),
    );
    expect($expectedTransactions)->toBe($actualTransactions);
});

it('searches transactions by amount', function () {
    $transactions = $this->transactionFactory->count(2);
    $repository = getArrayTransactionRepository($this->ledger, $transactions);

    $expectedTransactions = array_slice($transactions, 0, 1);
    $criteria = new TransactionSearchCriteria(
        amounts: $transactions[0]->getPostings()[0]->getAmount(),
    );
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($this->ledger, $criteria),
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
    $repository = getArrayTransactionRepository($this->ledger, $transactions);

    $expectedTransactions = array_slice($transactions, 0, 2);
    $criteria = new TransactionSearchCriteria(
        amounts: new MoneyRange(
            new Money(1, 'USD'),
            new Money(2, 'USD'),
        ),
    );
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($this->ledger, $criteria),
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
    $repository = getArrayTransactionRepository($this->ledger, $transactions);

    $expectedTransactions = array_slice($transactions, 0, 1);
    $criteria = new TransactionSearchCriteria(
        clearedDates: $transactions[0]->getPostings()[0]->getClearedDate(),
    );
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($this->ledger, $criteria),
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
    $repository = getArrayTransactionRepository($this->ledger, $transactions);

    $expectedTransactions = array_slice($transactions, 0, 2);
    $criteria = new TransactionSearchCriteria(
        clearedDates: new DateRange(
            $transactions[0]->getPostings()[0]->getClearedDate(),
            $transactions[1]->getPostings()[0]->getClearedDate(),
        ),
    );
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($this->ledger, $criteria),
    );
    expect($expectedTransactions)->toBe($actualTransactions);
});

it('searches transactions by transaction status', function () {
    $transactions = [
        $this->transactionFactory->create(status: TransactionStatus::Deleted),
    ];
    $repository = getArrayTransactionRepository($this->ledger, $transactions);

    $expectedTransactions = [];
    $criteria = new TransactionSearchCriteria(transactionStatuses: [TransactionStatus::Active]);
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($this->ledger, $criteria),
    );
    expect($expectedTransactions)->toBe($actualTransactions);

    $expectedTransactions = $transactions;
    $criteria = new TransactionSearchCriteria(transactionStatuses: [TransactionStatus::Deleted]);
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($this->ledger, $criteria),
    );
    expect($expectedTransactions)->toBe($actualTransactions);
});

it('searches transactions by posting status', function () {
    $transactions = [
        $this->transactionFactory->create(
            postings: [
                $this->postingFactory->create(status: PostingStatus::Deleted),
                $this->postingFactory->create(status: PostingStatus::Deleted),
            ],
        ),
    ];
    $repository = getArrayTransactionRepository($this->ledger, $transactions);

    $expectedTransactions = [];
    $criteria = new TransactionSearchCriteria(postingStatuses: [PostingStatus::Active]);
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($this->ledger, $criteria),
    );
    expect($expectedTransactions)->toBe($actualTransactions);

    $expectedTransactions = $transactions;
    $criteria = new TransactionSearchCriteria(postingStatuses: [PostingStatus::Deleted]);
    $actualTransactions = iterator_to_array(
        $repository->searchTransactions($this->ledger, $criteria),
    );
    expect($expectedTransactions)->toBe($actualTransactions);
});

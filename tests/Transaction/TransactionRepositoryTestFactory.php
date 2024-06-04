<?php

namespace Prophit\Core\Tests\Transaction;

use DateTime;

use Prophit\Core\{
    Date\DateRange,
    Ledger\Ledger,
    Money\Money,
    Money\MoneyRange,
    Tests\Account\AccountFactory,
    Tests\Ledger\LedgerFactory,
    Tests\Transaction\PostingFactory,
    Tests\Transaction\TransactionFactory,
    Transaction\PostingStatus,
    Transaction\Transaction,
    Transaction\TransactionException,
    Transaction\TransactionRepository,
    Transaction\TransactionStatus,
    Transaction\TransactionSearchCriteria,
};

class TransactionRepositoryTestFactory
{
    /**
     * @param class-string $fqcn
     */
    public static function createTests(string $fqcn): void
    {
        if (!class_exists($fqcn)) {
            throw TransactionRepositoryTestFactoryException::cannotResolveClass($fqcn);
        }

        if (!in_array(TransactionRepository::class, class_implements($fqcn))) {
            throw TransactionRepositoryTestFactoryException::classMissingInterface($fqcn);
        }

        $ledgerFactory = new LedgerFactory;
        $ledger = $ledgerFactory->create();
        $postingFactory = new PostingFactory;
        $transactionFactory = new TransactionFactory;

        /**
         * @param class-string<TransactionRepository> $fqcn
         * @param Transaction[] $transactions
         */
        $getTransactionRepository = function (
            array $transactions,
        ) use ($fqcn, $ledger): TransactionRepository {
            /** @var TransactionRepository */
            $repository = new $fqcn;
            foreach ($transactions as $transaction) {
                $repository->saveTransaction($ledger, $transaction);
            }
            return $repository;
        };

        it('saves transaction', function () use ($ledger, $transactionFactory, $getTransactionRepository) {
            $transaction = $transactionFactory->create();
            $repository = $getTransactionRepository([$transaction]);

            $expectedTransaction = $transaction;
            $actualTransaction = $repository->getTransactionById($ledger, $transaction->getId());
            expect($expectedTransaction)->toBe($actualTransaction);
        });

        it('gets existing transaction by ID', function () use ($ledger, $transactionFactory, $getTransactionRepository) {
            [$foundTransaction, $notFoundTransaction] = $transactions = $transactionFactory->count(2);
            $repository = $getTransactionRepository($transactions);

            $expectedTransaction = $foundTransaction;
            $actualTransaction = $repository->getTransactionById($ledger, $expectedTransaction->getId());
            expect($expectedTransaction)->toBe($actualTransaction);
        });

        it('does not get nonexistent transaction by ID', function () use ($ledger, $transactionFactory, $getTransactionRepository) {
            $notFoundTransaction = $transactionFactory->create();
            $repository = $getTransactionRepository([$notFoundTransaction]);
            $repository->getTransactionById($ledger, '-1');
        })->throws(TransactionException::class);

        it('searches transactions without criteria', function () use ($ledger, $transactionFactory, $getTransactionRepository) {
            $transactions = [$transactionFactory->create()];
            $repository = $getTransactionRepository($transactions);
            $criteria = new TransactionSearchCriteria;

            $expectedTransactions = $transactions;
            $actualTransactions = iterator_to_array(
                $repository->searchTransactions($ledger, $criteria),
            );
            expect($expectedTransactions)->toBe($actualTransactions);
        });

        it('searches transactions by IDs', function () use ($ledger, $transactionFactory, $getTransactionRepository) {
            $transactions = $transactionFactory->count(4);
            $repository = $getTransactionRepository($transactions);

            $expectedTransactions = array_slice($transactions, 0, 2);
            $criteria = new TransactionSearchCriteria(
                ids: array_map(
                    fn(Transaction $transaction): string => $transaction->getId(),
                    $expectedTransactions,
                ),
            );
            $actualTransactions = iterator_to_array(
                $repository->searchTransactions($ledger, $criteria),
            );
            expect($expectedTransactions)->toBe($actualTransactions);
        });

        it('searches transactions by transaction date using date', function () use ($ledger, $transactionFactory, $getTransactionRepository) {
            $transactions = [
                $transactionFactory->create(transactionDate: new DateTime('-1 day')),
                $transactionFactory->create(transactionDate: new DateTime('+1 day')),
            ];
            $repository = $getTransactionRepository($transactions);

            $expectedTransactions = array_slice($transactions, 0, 1);
            $criteria = new TransactionSearchCriteria(
                transactionDates: $transactions[0]->getTransactionDate(),
            );
            $actualTransactions = iterator_to_array(
                $repository->searchTransactions($ledger, $criteria),
            );
            expect($expectedTransactions)->toBe($actualTransactions);
        });

        it('searches transactions by transaction date using date range', function () use ($ledger, $transactionFactory, $getTransactionRepository) {
            $transactions = [
                $transactionFactory->create(transactionDate: new DateTime('-2 days')),
                $transactionFactory->create(transactionDate: new DateTime('-1 day')),
                $transactionFactory->create(transactionDate: new DateTime('+1 day')),
                $transactionFactory->create(transactionDate: new DateTime('+2 days')),
            ];
            $repository = $getTransactionRepository($transactions);

            $expectedTransactions = array_slice($transactions, 0, 2);
            $criteria = new TransactionSearchCriteria(
                transactionDates: new DateRange(
                    new DateTime('-2 days'),
                    new DateTime('-1 day'),
                ),
            );
            $actualTransactions = iterator_to_array(
                $repository->searchTransactions($ledger, $criteria),
            );
            expect($expectedTransactions)->toBe($actualTransactions);
        });

        it('searches transactions by description', function () use ($ledger, $transactionFactory, $getTransactionRepository) {
            $transactions = [
                $transactionFactory->create(description: 'foo'),
                $transactionFactory->create(description: 'foobar'),
                $transactionFactory->create(description: 'bar'),
                $transactionFactory->create(description: 'baz'),
            ];
            $repository = $getTransactionRepository($transactions);

            $expectedTransactions = array_slice($transactions, 0, 2);
            $criteria = new TransactionSearchCriteria(
                description: 'foo',
            );
            $actualTransactions = iterator_to_array(
                $repository->searchTransactions($ledger, $criteria),
            );
            expect($expectedTransactions)->toBe($actualTransactions);
        });

        it('searches transactions by account', function () use ($ledger, $transactionFactory, $getTransactionRepository) {
            $transactions = $transactionFactory->count(4);
            $repository = $getTransactionRepository($transactions);

            $expectedTransactions = array_slice($transactions, 0, 2);
            $criteria = new TransactionSearchCriteria(
                accounts: [
                    $transactions[0]->getPostings()[0]->getAccount(),
                    $transactions[1]->getPostings()[0]->getAccount(),
                ],
            );
            $actualTransactions = iterator_to_array(
                $repository->searchTransactions($ledger, $criteria),
            );
            expect($expectedTransactions)->toBe($actualTransactions);
        });

        it('searches transactions by ledger', function () use (
            $ledgerFactory,
            $ledger,
            $transactionFactory,
            $postingFactory,
            $getTransactionRepository
        ) {
            $otherLedger = $ledgerFactory->create();
            $accountFactory = new AccountFactory;
            $ledgerAccount = $accountFactory->create(ledger: $ledger);
            $otherLedgerAccount = $accountFactory->create(ledger: $otherLedger);
            $ledgerPostings = [
                $postingFactory->create(account: $ledgerAccount),
                $postingFactory->create(account: $ledgerAccount),
            ];
            $otherLedgerPostings = [
                $postingFactory->create(account: $otherLedgerAccount),
                $postingFactory->create(account: $otherLedgerAccount),
            ];
            $transactions = [
                $transactionFactory->create(postings: $ledgerPostings),
                $transactionFactory->create(postings: $otherLedgerPostings),
            ];
            $repository = $getTransactionRepository($transactions);

            $expectedTransactions = array_slice($transactions, 0, 1);
            $criteria = new TransactionSearchCriteria(
                ledgers: [$ledger],
            );
            $actualTransactions = iterator_to_array(
                $repository->searchTransactions($ledger, $criteria),
            );
            expect($expectedTransactions)->toBe($actualTransactions);
        });

        it('searches transactions by amount', function () use ($ledger, $transactionFactory, $getTransactionRepository) {
            $transactions = $transactionFactory->count(2);
            $repository = $getTransactionRepository($transactions);

            $expectedTransactions = array_slice($transactions, 0, 1);
            $criteria = new TransactionSearchCriteria(
                amounts: $transactions[0]->getPostings()[0]->getAmount(),
            );
            $actualTransactions = iterator_to_array(
                $repository->searchTransactions($ledger, $criteria),
            );
            expect($expectedTransactions)->toBe($actualTransactions);
        });

        it('searches transactions by amount range', function () use ($ledger, $transactionFactory, $postingFactory, $getTransactionRepository) {
            $transactions = array_map(
                fn(int $amount): Transaction => $transactionFactory->create(
                    postings: [
                        $postingFactory->create(amount: new Money($amount, 'USD')),
                        $postingFactory->create(amount: new Money($amount * -1, 'USD')),
                    ],
                ),
                range(1, 4),
            );
            $repository = $getTransactionRepository($transactions);

            $expectedTransactions = array_slice($transactions, 0, 2);
            $criteria = new TransactionSearchCriteria(
                amounts: new MoneyRange(
                    new Money(1, 'USD'),
                    new Money(2, 'USD'),
                ),
            );
            $actualTransactions = iterator_to_array(
                $repository->searchTransactions($ledger, $criteria),
            );
            expect($expectedTransactions)->toBe($actualTransactions);
        });

        it('searches transactions by cleared date using date', function () use ($ledger, $postingFactory, $transactionFactory, $getTransactionRepository) {
            $transactions = array_map(
                fn(int $amount): Transaction => $transactionFactory->create(
                    postings: [
                        $postingFactory->create(clearedDate: new DateTime('+' . $amount . ' days')),
                        $postingFactory->create(clearedDate: new DateTime('+' . $amount . ' days')),
                    ],
                ),
                range(1, 2),
            );
            $repository = $getTransactionRepository($transactions);

            $expectedTransactions = array_slice($transactions, 0, 1);
            $criteria = new TransactionSearchCriteria(
                clearedDates: $transactions[0]->getPostings()[0]->getClearedDate(),
            );
            $actualTransactions = iterator_to_array(
                $repository->searchTransactions($ledger, $criteria),
            );
            expect($expectedTransactions)->toBe($actualTransactions);
        });

        it('searches transactions by cleared date using date range', function () use ($ledger, $postingFactory, $transactionFactory, $getTransactionRepository) {
            $transactions = array_map(
                fn(int $amount): Transaction => $transactionFactory->create(
                    postings: [
                        $postingFactory->create(clearedDate: new DateTime('+' . $amount . ' days')),
                        $postingFactory->create(clearedDate: new DateTime('+' . $amount . ' days')),
                    ],
                ),
                range(1, 4),
            );
            $repository = $getTransactionRepository($transactions);

            $expectedTransactions = array_slice($transactions, 0, 2);
            /** @var \DateTimeInterface */
            $startDate = $transactions[0]->getPostings()[0]->getClearedDate();
            /** @var \DateTimeInterface */
            $endDate = $transactions[1]->getPostings()[0]->getClearedDate();
            $criteria = new TransactionSearchCriteria(
                clearedDates: new DateRange($startDate, $endDate),
            );
            $actualTransactions = iterator_to_array(
                $repository->searchTransactions($ledger, $criteria),
            );
            expect($expectedTransactions)->toBe($actualTransactions);
        });

        it('searches transactions by transaction status', function () use ($ledger, $transactionFactory, $getTransactionRepository) {
            $transactions = [
                $transactionFactory->create(status: TransactionStatus::Deleted),
            ];
            $repository = $getTransactionRepository($transactions);

            $expectedTransactions = [];
            $criteria = new TransactionSearchCriteria(transactionStatuses: [TransactionStatus::Active]);
            $actualTransactions = iterator_to_array(
                $repository->searchTransactions($ledger, $criteria),
            );
            expect($expectedTransactions)->toBe($actualTransactions);

            $expectedTransactions = $transactions;
            $criteria = new TransactionSearchCriteria(transactionStatuses: [TransactionStatus::Deleted]);
            $actualTransactions = iterator_to_array(
                $repository->searchTransactions($ledger, $criteria),
            );
            expect($expectedTransactions)->toBe($actualTransactions);
        });

        it('searches transactions by posting status', function () use ($ledger, $postingFactory, $transactionFactory, $getTransactionRepository) {
            $transactions = [
                $transactionFactory->create(
                    postings: [
                        $postingFactory->create(status: PostingStatus::Deleted),
                        $postingFactory->create(status: PostingStatus::Deleted),
                    ],
                ),
            ];
            $repository = $getTransactionRepository($transactions);

            $expectedTransactions = [];
            $criteria = new TransactionSearchCriteria(postingStatuses: [PostingStatus::Active]);
            $actualTransactions = iterator_to_array(
                $repository->searchTransactions($ledger, $criteria),
            );
            expect($expectedTransactions)->toBe($actualTransactions);

            $expectedTransactions = $transactions;
            $criteria = new TransactionSearchCriteria(postingStatuses: [PostingStatus::Deleted]);
            $actualTransactions = iterator_to_array(
                $repository->searchTransactions($ledger, $criteria),
            );
            expect($expectedTransactions)->toBe($actualTransactions);
        });
    }
}

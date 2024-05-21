<?php

namespace Prophit\Core\Transaction;

use Prophit\Core\Ledger\Ledger;

interface TransactionRepository
{
    public function saveTransaction(
        Ledger $ledger,
        Transaction $transaction,
    ): void;

    /**
     * @throws TransactionException if transaction is not found
     */
    public function getTransactionById(
        Ledger $ledger,
        string $transactionId,
    ): Transaction;

    /**
     * @return iterable<Transaction>
     */
    public function searchTransactions(
        Ledger $ledger,
        TransactionSearchCriteria $criteria,
    ): iterable;
}

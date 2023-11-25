<?php

namespace Prophit\Core\Transaction;

use Prophit\Core\Exception\TransactionNotFoundException;

interface TransactionRepository
{
    public function saveTransaction(Transaction $transaction): void;

    /**
     * @throws TransactionNotFoundException if transaction is not found
     */
    public function getTransactionById(string $id): Transaction;

    /**
     * @return iterable<Transaction>
     */
    public function searchTransactions(TransactionSearchCriteria $criteria): iterable;
}

<?php

namespace Prophit\Core\Transaction;

use DateTimeInterface;

use Prophit\Core\{
    Account\Account,
    Date\DateRange,
    Exception\TransactionNotFoundException,
    Money\Money,
    Money\MoneyRange,
};

class ArrayTransactionRepository implements TransactionRepository
{
    /** @var array<string, Transaction> */
    private array $transactions;

    public function __construct(Transaction... $transactions)
    {
        $this->transactions = [];
        foreach ($transactions as $transaction) {
            $this->saveTransaction($transaction);
        }
    }

    public function saveTransaction(Transaction $transaction): void
    {
        $this->transactions[$transaction->getId()] = $transaction;
    }

    public function getTransactionById(string $id): Transaction
    {
        if (!isset($this->transactions[$id])) {
            throw new TransactionNotFoundException($id);
        }
        return $this->transactions[$id];
    }

    public function searchTransactions(TransactionSearchCriteria $criteria): iterable
    {
        foreach ($this->transactions as $transaction) {
            if ($this->transactionMatches($transaction, $criteria)) {
                yield $transaction;
            }
        }
    }

    private function transactionMatches(
        Transaction $transaction,
        TransactionSearchCriteria $criteria,
    ): bool {
        return
            $this->idsMatch($transaction, $criteria) ||
            $this->transactionDatesMatch($transaction, $criteria) ||
            $this->descriptionMatches($transaction, $criteria) ||
            $this->accountsMatch($transaction, $criteria) ||
            $this->amountsMatch($transaction, $criteria) ||
            $this->clearedDatesMatch($transaction, $criteria);
    }

    private function idsMatch(
        Transaction $transaction,
        TransactionSearchCriteria $criteria,
    ): bool {
        $ids = $criteria->getIds();
        return is_array($ids) && in_array($transaction->getId(), $ids);
    }

    private function transactionDatesMatch(
        Transaction $transaction,
        TransactionSearchCriteria $criteria,
    ): bool {
        return $this->dateMatches(
            $transaction->getTransactionDate(),
            $criteria->getTransactionDates(),
        );
    }

    private function descriptionMatches(
        Transaction $transaction,
        TransactionSearchCriteria $criteria,
    ): bool {
        $transactionDescription = $transaction->getDescription();
        $criteriaDescription = $criteria->getDescription();
        return is_string($transactionDescription)
            && is_string($criteriaDescription)
            && stripos($transactionDescription, $criteriaDescription) !== false;
    }

    private function accountsMatch(
        Transaction $transaction,
        TransactionSearchCriteria $criteria,
    ): bool {
        $accounts = $criteria->getAccounts();
        if (!is_array($accounts)) {
            return false;
        }
        $transactionAccounts = array_map(
            fn(Posting $posting): Account => $posting->getAccount(),
            $transaction->getPostings()
        );
        foreach ($accounts as $account) {
            foreach ($transactionAccounts as $transactionAccount) {
                if ($account->isSame($transactionAccount)) {
                    return true;
                }
            }
        }
        return false;
    }

    private function amountsMatch(
        Transaction $transaction,
        TransactionSearchCriteria $criteria,
    ): bool {
        $amounts = $criteria->getAmounts();
        if (!($amounts instanceof Money || $amounts instanceof MoneyRange)) {
            return false;
        }
        $transactionAmounts = array_map(
            fn(Posting $posting): Money => $posting->getAmount(),
            $transaction->getPostings(),
        );
        if ($amounts instanceof Money) {
            foreach ($transactionAmounts as $transactionAmount) {
                if ($transactionAmount->isEqualTo($amounts)) {
                    return true;
                }
            }
        } elseif ($amounts instanceof MoneyRange) {
            foreach ($transactionAmounts as $transactionAmount) {
                if ($amounts->contains($transactionAmount)) {
                    return true;
                }
            }
        }
        return false;
    }

    private function clearedDatesMatch(
        Transaction $transaction,
        TransactionSearchCriteria $criteria,
    ): bool {
        $clearedDates = $criteria->getClearedDates();
        if (!(
            $clearedDates instanceof DateTimeInterface ||
            $clearedDates instanceof DateRange
        )) {
            return false;
        }
        $transactionClearedDates = array_filter(
            array_map(
                fn(Posting $posting): ?DateTimeInterface => $posting->getClearedDate(),
                $transaction->getPostings()
            )
        );
        foreach ($transactionClearedDates as $transactionClearedDate) {
            if ($this->dateMatches($transactionClearedDate, $clearedDates)) {
                return true;
            }
        }
        return false;
    }

    private function dateMatches(
        DateTimeInterface $transactionDate,
        DateTimeInterface|DateRange|null $criteriaDates,
    ): bool {
        if ($criteriaDates instanceof DateTimeInterface) {
            $formattedTransactionDate = $this->formatDate($transactionDate);
            $formattedCriteriaDate = $this->formatDate($criteriaDates);
            if ($formattedTransactionDate === $formattedCriteriaDate) {
                return true;
            }
        } elseif ($criteriaDates instanceof DateRange && $criteriaDates->contains($transactionDate)) {
            return true;
        }
        return false;
    }

    private function formatDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d');
    }
}

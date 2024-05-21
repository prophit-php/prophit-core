<?php

namespace Prophit\Core\Transaction;

use DateTimeInterface;

use Prophit\Core\{
    Account\Account,
    Date\DateRange,
    Ledger\Ledger,
    Money\Money,
    Money\MoneyRange,
};

class ArrayTransactionRepository implements TransactionRepository
{
    /** @var array<string, array<string, Transaction>> */
    private array $transactions;

    public function __construct()
    {
        $this->transactions = [];
    }

    public function saveTransaction(
        Ledger $ledger,
        Transaction $transaction,
    ): void {
        $ledgerId = $ledger->getId();
        $this->transactions[$ledgerId] ??= [];
        $this->transactions[$ledgerId][$transaction->getId()] = $transaction;
    }

    public function getTransactionById(
        Ledger $ledger,
        string $transactionId,
    ): Transaction {
        $ledgerId = $ledger->getId();
        if (!isset($this->transactions[$ledgerId][$transactionId])) {
            throw TransactionException::transactionNotFound($ledger, $transactionId);
        }
        return $this->transactions[$ledgerId][$transactionId];
    }

    public function searchTransactions(
        Ledger $ledger,
        TransactionSearchCriteria $criteria,
    ): iterable {
        $ledgerId = $ledger->getId();
        if (!empty($this->transactions[$ledgerId])) {
            foreach ($this->transactions[$ledgerId] as $transaction) {
                if ($this->transactionMatches($transaction, $criteria)) {
                    yield $transaction;
                }
            }
        }
    }

    private function transactionMatches(
        Transaction $transaction,
        TransactionSearchCriteria $criteria,
    ): bool {
        return
            !$criteria->hasCriteria() ||
            $this->idsMatch($transaction, $criteria) ||
            $this->transactionDatesMatch($transaction, $criteria) ||
            $this->descriptionMatches($transaction, $criteria) ||
            $this->accountsMatch($transaction, $criteria) ||
            $this->amountsMatch($transaction, $criteria) ||
            $this->clearedDatesMatch($transaction, $criteria) ||
            $this->statusesMatch($transaction, $criteria);
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

    private function statusesMatch(
        Transaction $transaction,
        TransactionSearchCriteria $criteria,
    ): bool {
        $transactionStatuses = $criteria->getTransactionStatuses();
        if (
            is_array($transactionStatuses)
            && count($transactionStatuses) > 0
            && in_array($transaction->getStatus(), $transactionStatuses, true)
        ) {
            return true;
        }
        $postingStatuses = $criteria->getPostingStatuses();
        if (is_array($postingStatuses) && count($postingStatuses) > 0) {
            foreach ($transaction->getPostings() as $posting) {
                if (in_array($posting->getStatus(), $postingStatuses, true)) {
                    return true;
                }
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

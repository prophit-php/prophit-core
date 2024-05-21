<?php

namespace Prophit\Core\Account;

use Prophit\Core\Ledger\Ledger;

interface AccountRepository
{
    public function saveAccount(Account $account): void;

    /**
     * @throws AccountException if account is not found
     */
    public function getAccountById(Ledger $ledger, string $accountId): Account;

    /**
     * @return iterable<Account>
     */
    public function getAllAccounts(): iterable;

    /**
     * @return iterable<Account>
     */
    public function searchAccounts(AccountSearchCriteria $criteria): iterable;
}

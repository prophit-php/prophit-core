<?php

namespace Prophit\Core\Account;

use Prophit\Core\Exception\AccountNotFoundException;

interface AccountRepository
{
    public function saveAccount(Account $account): void;

    /**
     * @throws AccountNotFoundException if account is not found
     */
    public function getAccountById(string $id): Account;

    /**
     * @return iterable<Account>
     */
    public function getAllAccounts(): iterable;

    /**
     * @return iterable<Account>
     */
    public function searchAccounts(AccountSearchCriteria $criteria): iterable;
}

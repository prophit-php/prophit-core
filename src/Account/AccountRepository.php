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

    public function getAllAccounts(): AccountIterator;

    public function searchAccounts(AccountSearchCriteria $criteria): AccountIterator;
}

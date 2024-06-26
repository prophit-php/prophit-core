<?php

namespace Prophit\Core\Account;

use Prophit\Core\Exception\AccountNotFoundException;

class ArrayAccountRepository implements AccountRepository
{
    /** @var array<string, Account> **/
    private array $accounts;

    public function __construct(Account... $accounts)
    {
        $this->accounts = [];
        foreach ($accounts as $account) {
            $this->saveAccount($account);
        }
    }

    public function saveAccount(Account $account): void
    {
        $this->accounts[$account->getId()] = $account;
    }

    public function getAccountById(string $id): Account
    {
        if (!isset($this->accounts[$id])) {
            throw new AccountNotFoundException($id);
        }
        return $this->accounts[$id];
    }

    public function getAllAccounts(): iterable
    {
        foreach ($this->accounts as $account) {
            yield $account;
        }
    }

    public function searchAccounts(AccountSearchCriteria $criteria): iterable
    {
        $ids = $criteria->getIds();
        $name = $criteria->getName();
        foreach ($this->accounts as $account) {
            if ($this->accountMatches($account, $criteria)) {
                yield $account;
            }
        }
    }

    private function accountMatches(
        Account $account,
        AccountSearchCriteria $criteria,
    ): bool {
        return
            !$criteria->hasCriteria() ||
            $this->idsMatch($account, $criteria) ||
            $this->nameMatches($account, $criteria);
    }

    private function idsMatch(Account $account, AccountSearchCriteria $criteria): bool
    {
        $ids = $criteria->getIds();
        return is_array($ids) && in_array($account->getId(), $ids);
    }

    private function nameMatches(Account $account, AccountSearchCriteria $criteria): bool
    {
        $name = $criteria->getName();
        return is_string($name) && stripos($account->getName(), $name) !== false;
    }
}

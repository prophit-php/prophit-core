<?php

namespace Prophit\Core\Account;

class LocalCacheAccountRepository implements AccountRepository
{
    /** @var array<string, Account> **/
    private array $accounts = [];

    private bool $haveAllAccounts = false;

    public function __construct(
        private AccountRepository $repository,
    ) { }

    public function saveAccount(Account $account): void
    {
        $this->repository->saveAccount($account);
        $this->accounts[$account->getId()] = $account;
    }

    public function getAccountById(string $id): Account
    {
        if (!isset($this->accounts[$id])) {
            $this->accounts[$id] = $this->repository->getAccountById($id);
        }
        return $this->accounts[$id];
    }

    public function getAllAccounts(): AccountIterator
    {
        if (!$this->haveAllAccounts) {
            foreach ($this->repository->getAllAccounts() as $account) {
                $this->accounts[$account->getId()] = $account;
            }
            $this->haveAllAccounts = true;
        }
        return new AccountIterator(...$this->accounts);
    }

    public function searchAccounts(AccountSearchCriteria $criteria): AccountIterator
    {
        $ids = $criteria->getIds();
        if ($ids !== null) {
            $cachedIds = array_filter($ids, fn(string $id): bool => isset($this->accounts[$id]));
            if ($ids === $cachedIds) {
                $accounts = array_map(fn(string $id): Account => $this->accounts[$id], $ids);
                return new AccountIterator(...$accounts);
            }
        }

        if ($this->haveAllAccounts) {
            $name = $criteria->getName();
            if ($name !== null) {
                $accounts = array_filter(
                    $this->accounts,
                    fn(Account $account): bool => $account->getName() === $name,
                );
                return new AccountIterator(...$accounts);
            }

            $parentIds = $criteria->getParentIds();
            if ($parentIds !== null) {
                $accounts = array_filter(
                    $this->accounts,
                    fn(Account $account): bool => in_array($account->getParentId(), $parentIds),
                );
                return new AccountIterator(...$accounts);
            }

            return new AccountIterator(...$this->accounts);
        }

        $accounts = $this->repository->searchAccounts($criteria);
        foreach ($accounts as $account) {
            $this->accounts[$account->getId()] = $account;
        }
        return new AccountIterator(...$accounts);
    }
}

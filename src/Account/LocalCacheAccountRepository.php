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
            return $this->accounts[$id] = $this->repository->getAccountById($id);
        }
        return $this->accounts[$id];
    }

    public function getAllAccounts(): iterable
    {
        if ($this->haveAllAccounts) {
            foreach ($this->accounts as $account) {
                yield $account;
            }
        } else {
            foreach ($this->repository->getAllAccounts() as $account) {
                yield $this->accounts[$account->getId()] = $account;
            }
            $this->haveAllAccounts = true;
        }
    }

    public function searchAccounts(AccountSearchCriteria $criteria): iterable
    {
        foreach ($this->repository->searchAccounts($criteria) as $account) {
            yield $this->accounts[$account->getId()] = $account;
        }
    }
}

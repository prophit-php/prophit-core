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

    public function getAllAccounts(): AccountIterator
    {
        return new AccountIterator(...$this->accounts);
    }

    public function searchAccounts(AccountSearchCriteria $criteria): AccountIterator
    {
        $accounts = array_reduce(
            [
                fn($accounts) => $this->filterAccountsById($accounts, $criteria->getIds()),
                fn($accounts) => $this->filterAccountsByName($accounts, $criteria->getName()),
            ],
            fn(array $accounts, callable $callback) => $callback($accounts),
            $this->accounts,
        );
        return new AccountIterator(...$accounts);
    }

    /**
     * @param Account[] $accounts
     * @param string[]|null $ids
     * @return Account[]
     */
    private function filterAccountsById(array $accounts, ?array $ids): array
    {
        return $ids === null ? $accounts :array_filter(
            $accounts,
            fn(Account $account): bool => in_array($account->getId(), $ids),
        );
    }

    /**
     * @param Account[] $accounts
     * @param string|null $name
     * @return Account[]
     */
    private function filterAccountsByName(array $accounts, ?string $name): array
    {
        return $name === null ? $accounts : array_filter(
            $accounts,
            fn(Account $account): bool => $account->getName() === $name,
        );
    }
}

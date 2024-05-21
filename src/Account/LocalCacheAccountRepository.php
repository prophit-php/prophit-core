<?php

namespace Prophit\Core\Account;

use Prophit\Core\Ledger\Ledger;

class LocalCacheAccountRepository implements AccountRepository
{
    /** @var array<string, array<string, Account>> **/
    private array $accounts = [];

    private bool $haveAllAccounts = false;

    public function __construct(
        private AccountRepository $repository,
    ) { }

    public function saveAccount(Account $account): void
    {
        $this->repository->saveAccount($account);
        $ledgerId = $account->getLedger()->getId();
        $accountId = $account->getId();
        $this->accounts[$ledgerId] ??= [];
        $this->accounts[$ledgerId][$accountId] = $account;
    }

    public function getAccountById(Ledger $ledger, string $accountId): Account
    {
        $ledgerId = $ledger->getId();
        if (!isset($this->accounts[$ledgerId][$accountId])) {
            return $this->accounts[$ledgerId][$accountId] = $this->repository->getAccountById($ledger, $accountId);
        }
        return $this->accounts[$ledgerId][$accountId];
    }

    public function getAllAccounts(): iterable
    {
        if ($this->haveAllAccounts) {
            foreach ($this->accounts as $accounts) {
                foreach ($accounts as $account) {
                    yield $account;
                }
            }
        } else {
            foreach ($this->repository->getAllAccounts() as $account) {
                $ledgerId = $account->getLedger()->getId();
                $accountId = $account->getId();
                $this->accounts[$ledgerId] ??= [];
                yield $this->accounts[$ledgerId][$accountId] = $account;
            }
            $this->haveAllAccounts = true;
        }
    }

    public function searchAccounts(AccountSearchCriteria $criteria): iterable
    {
        foreach ($this->repository->searchAccounts($criteria) as $account) {
            $ledgerId = $account->getLedger()->getId();
            $accountId = $account->getId();
            $this->accounts[$ledgerId] ??= [];
            yield $this->accounts[$ledgerId][$accountId] = $account;
        }
    }
}

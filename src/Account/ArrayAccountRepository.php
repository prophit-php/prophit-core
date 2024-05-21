<?php

namespace Prophit\Core\Account;

use Prophit\Core\Ledger\Ledger;

class ArrayAccountRepository implements AccountRepository
{
    /**
     * Mapping of accounts indexed by ledger and account IDs
     *
     * @var array<string, array<string, Account>>
     */
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
        $ledgerId = $account->getLedger()->getId();
        $accountId = $account->getId();
        $this->accounts[$ledgerId][$accountId] = $account;
    }

    public function getAccountById(Ledger $ledger, string $accountId): Account
    {
        $ledgerId = $ledger->getId();
        if (!isset($this->accounts[$ledgerId][$accountId])) {
            throw AccountException::accountNotFound($ledger, $accountId);
        }
        return $this->accounts[$ledgerId][$accountId];
    }

    public function getAllAccounts(): iterable
    {
        foreach ($this->accounts as $ledgerId => $accounts) {
            foreach ($accounts as $account) {
                yield $account;
            }
        }
    }

    public function searchAccounts(AccountSearchCriteria $criteria): iterable
    {
        $ids = $criteria->getIds();
        $name = $criteria->getName();
        foreach ($this->accounts as $ledgerId => $accounts) {
            foreach ($accounts as $account) {
                if ($this->accountMatches($account, $criteria)) {
                    yield $account;
                }
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

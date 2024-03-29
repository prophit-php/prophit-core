<?php

use Prophit\Core\Account\{
    Account,
    AccountRepository,
    AccountSearchCriteria,
    LocalCacheAccountRepository,
};

use Prophit\Core\Tests\Account\AccountFactory;

class TestAccountRepository implements AccountRepository
{
    public bool $savedAccount = false;
    public bool $gotAccount = false;
    public int $gotAllAccounts = 0;
    public bool $searchedAccounts = false;
    public function __construct(private Account $gottenAccount) { }
    public function saveAccount(Account $account): void {
        $this->savedAccount = true;
    }
    public function getAccountById(string $id): Account {
        $this->gotAccount = true;
        return $this->gottenAccount;
    }
    public function getAllAccounts(): iterable {
        $this->gotAllAccounts++;
        yield $this->gottenAccount;
    }
    public function searchAccounts(AccountSearchCriteria $criteria): iterable {
        $this->searchedAccounts = true;
        yield $this->gottenAccount;
    }
}

beforeEach(function () {
    $this->factory = new AccountFactory;
    $this->account = $this->factory->create(name: 'Test');
    $this->repository = new TestAccountRepository($this->account);
    $this->cache = new LocalCacheAccountRepository($this->repository);
});

it('caches saved accounts', function () {
    $this->cache->saveAccount($this->account);
    $result = $this->cache->getAccountById($this->account->getId());
    expect($result)->toBe($this->account);
    expect($this->repository->savedAccount)->toBeTrue();
    expect($this->repository->gotAccount)->toBeFalse();
});

it('proxies fetches to the repository for uncached accounts', function () {
    $result = $this->cache->getAccountById($this->account->getId());
    expect($result)->toBe($this->account);
    expect($this->repository->gotAccount)->toBeTrue();
});

it('proxies fetching all accounts to the repository', function () {
    $uncached = $this->cache->getAllAccounts();
    expect(iterator_to_array($uncached))->toBe([$this->account]);
    expect($this->repository->gotAllAccounts)->toBe(1);

    $cached = $this->cache->getAllAccounts();
    expect(iterator_to_array($cached))->toBe([$this->account]);
    expect($this->repository->gotAllAccounts)->toBe(1);
});

it('proxies to the repository when searching', function () {
    $criteria = new AccountSearchCriteria([$this->account->getId()]);
    $results = $this->cache->searchAccounts($criteria);
    expect(iterator_to_array($results))->toBe([$this->account]);
    expect($this->repository->searchedAccounts)->toBeTrue();
});

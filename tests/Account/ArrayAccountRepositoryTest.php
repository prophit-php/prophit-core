<?php

use Prophit\Core\{
    Account\Account,
    Account\AccountSearchCriteria,
    Account\ArrayAccountRepository,
    Exception\AccountNotFoundException,
    Tests\Account\AccountFactory,
};

beforeEach(function () {
    $this->factory = new AccountFactory;
});

it('saves account', function () {
    $account = $this->factory->create();
    $repository = new ArrayAccountRepository;
    $repository->saveAccount($account);

    $expectedAccount = $account;
    $actualAccount = $repository->getAccountById($account->getId());
    expect($expectedAccount)->toBe($actualAccount);
});

it('gets existing account by ID', function () {
    [$foundAccount, $notFoundAccount] = $accounts = $this->factory->count(2);
    $repository = new ArrayAccountRepository(...$accounts);

    $expectedAccount = $foundAccount;
    $actualAccount = $repository->getAccountById($expectedAccount->getId());
    expect($expectedAccount)->toBe($actualAccount);
});

it('does not get nonexistent account by ID', function () {
    $notFoundAccount = $this->factory->create();
    $repository = new ArrayAccountRepository($notFoundAccount);
    $repository->getAccountById('-1');
})->throws(AccountNotFoundException::class);

it('gets all accounts', function () {
    $accounts = $this->factory->count(2);
    $repository = new ArrayAccountRepository(...$accounts);

    $expectedAccounts = $accounts;
    $actualAccounts = iterator_to_array($repository->getAllAccounts());
    expect($expectedAccounts)->toBe($actualAccounts);
});

it('searches accounts by IDs', function () {
    $accounts = $this->factory->count(4);
    $repository = new ArrayAccountRepository(...$accounts);

    $expectedAccounts = array_slice($accounts, 0, 2);
    $criteria = new AccountSearchCriteria(
        ids: array_map(
            fn(Account $account): string => $account->getId(),
            $expectedAccounts,
        ),
    );
    $actualAccounts = iterator_to_array(
        $repository->searchAccounts($criteria),
    );
    expect($expectedAccounts)->toBe($actualAccounts);
});

it('searches accounts by name', function () {
    $accounts = [
        $this->factory->create(name: 'Foo'),
        $this->factory->create(name: 'Foobar'),
        $this->factory->create(name: 'Barfoo'),
        $this->factory->create(name: 'Bar'),
    ];
    $repository = new ArrayAccountRepository(...$accounts);

    $expectedAccounts = array_slice($accounts, 0, 3);
    $criteria = new AccountSearchCriteria(
        name: $accounts[0]->getName(),
    );
    $actualAccounts = iterator_to_array(
        $repository->searchAccounts($criteria),
    );
    expect($expectedAccounts)->toBe($actualAccounts);
});

it('searches accounts by multiple criteria', function () {
    $accounts = [
        $this->factory->create(name: 'Foo'),
        $this->factory->create(name: 'Bar'),
        $this->factory->create(name: 'Baz'),
    ];
    $repository = new ArrayAccountRepository(...$accounts);

    $expectedAccounts = array_slice($accounts, 0, 2);
    $criteria = new AccountSearchCriteria(
        ids: [ $accounts[1]->getId() ],
        name: 'Foo',
    );
    $actualAccounts = iterator_to_array(
        $repository->searchAccounts($criteria),
    );
    expect($expectedAccounts)->toBe($actualAccounts);
});

it('searches all accounts', function () {
    $foundAccount = $this->factory->create();
    $repository = new ArrayAccountRepository($foundAccount);

    $expectedAccounts = [$foundAccount];
    $criteria = new AccountSearchCriteria;
    $actualAccounts = iterator_to_array(
        $repository->searchAccounts($criteria),
    );
    expect($expectedAccounts)->toBe($actualAccounts);
});

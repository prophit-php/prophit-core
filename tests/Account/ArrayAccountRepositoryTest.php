<?php

use Prophit\Core\Account\{
    Account,
    AccountSearchCriteria,
    ArrayAccountRepository,
};

use Prophit\Core\Exception\AccountNotFoundException;

test('saves account', function () {
    $account = new Account('1', 'Test');
    $repository = new ArrayAccountRepository;
    $repository->saveAccount($account);

    $expectedAccount = $account;
    $actualAccount = $repository->getAccountById($account->getId());
    expect($expectedAccount)->toBe($actualAccount);
});

test('gets existing account by ID', function () {
    $foundAccount = new Account('1', 'Found');
    $notFoundAccount = new Account('2', 'Not Found');
    $repository = new ArrayAccountRepository(
        $foundAccount,
        $notFoundAccount,
    );

    $expectedAccount = $foundAccount;
    $actualAccount = $repository->getAccountById($expectedAccount->getId());
    expect($expectedAccount)->toBe($actualAccount);
});

test('does not get nonexistent account by ID', function () {
    $notFoundAccount = new Account('1', 'Not Found');
    $repository = new ArrayAccountRepository($notFoundAccount);
    $repository->getAccountById('2');
})->throws(AccountNotFoundException::class);

test('gets all accounts', function () {
    $accounts = [
        new Account('1', 'Account 1'),
        new Account('2', 'Account 2'),
    ];
    $repository = new ArrayAccountRepository(...$accounts);

    $expectedAccounts = $accounts;
    $actualAccounts = iterator_to_array($repository->getAllAccounts());
    expect($expectedAccounts)->toBe($actualAccounts);
});

test('searches accounts by IDs', function () {
    $accounts = [
        new Account('1', 'Found 1'),
        new Account('2', 'Found 2'),
        new Account('3', 'Not Found 1'),
        new Account('4', 'Not Found 2'),
    ];
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

test('searches accounts by name', function () {
    $accounts = [
        new Account('1', 'Foo'),
        new Account('2', 'Foobar'),
        new Account('3', 'Bar'),
    ];
    $repository = new ArrayAccountRepository(...$accounts);

    $expectedAccounts = [ $accounts[0] ];
    $criteria = new AccountSearchCriteria(
        name: $accounts[0]->getName(),
    );
    $actualAccounts = iterator_to_array(
        $repository->searchAccounts($criteria),
    );
    expect($expectedAccounts)->toBe($actualAccounts);
});

test('searches accounts by parent IDs', function () {
    $accounts = [
        new Account('1', 'Parent 1'),
        new Account('2', 'Child 1-1', '1'),
        new Account('3', 'Child 2-1', '1'),
        new Account('4', 'Parent 2'),
        new Account('5', 'Child 2-1', '4'),
    ];
    $repository = new ArrayAccountRepository(...$accounts);

    $expectedAccounts = [
        $accounts[1],
        $accounts[2],
        $accounts[4],
    ];
    $criteria = new AccountSearchCriteria(
        parentIds: [
            $accounts[0]->getId(),
            $accounts[3]->getId(),
        ],
    );
    $actualAccounts = iterator_to_array(
        $repository->searchAccounts($criteria),
    );
    expect($expectedAccounts)->toBe($actualAccounts);
});

test('searches accounts by multiple criteria', function () {
    $accounts = [
        new Account('1', 'Foo'),
        new Account('2', 'Bar'),
    ];
    $repository = new ArrayAccountRepository(...$accounts);

    $expectedAccounts = [
        $accounts[0],
    ];
    $criteria = new AccountSearchCriteria(
        ids: [
            $accounts[0]->getId(),
            $accounts[1]->getId(),
        ],
        name: 'Foo',
    );
    $actualAccounts = iterator_to_array(
        $repository->searchAccounts($criteria),
    );
    expect($expectedAccounts)->toBe($actualAccounts);
});

test('searches all accounts', function () {
    $foundAccount = new Account('1', 'Not Found');
    $repository = new ArrayAccountRepository($foundAccount);

    $expectedAccounts = [$foundAccount];
    $criteria = new AccountSearchCriteria;
    $actualAccounts = iterator_to_array(
        $repository->searchAccounts($criteria),
    );
    expect($expectedAccounts)->toBe($actualAccounts);
});

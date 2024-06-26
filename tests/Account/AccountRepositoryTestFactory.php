<?php

namespace Prophit\Core\Tests\Account;

use Prophit\Core\{
    Account\Account,
    Account\AccountSearchCriteria,
    Account\AccountRepository,
    Exception\AccountNotFoundException,
    Tests\Account\AccountFactory,
};

class AccountRepositoryTestFactory
{
    /**
     * @param class-string $fqcn FQCN of the AccountRepository
     *        implementation to test
     */
    public static function createTests(string $fqcn): void
    {
        if (!class_exists($fqcn)) {
            throw AccountRepositoryTestFactoryException::cannotResolveClass($fqcn);
        }

        if (!in_array(AccountRepository::class, class_implements($fqcn))) {
            throw AccountRepositoryTestFactoryException::classMissingInterface($fqcn);
        }

        $factory = new AccountFactory;

        it('saves account', function () use ($fqcn, $factory) {
            $account = $factory->create();
            /** @var AccountRepository */
            $repository = new $fqcn;
            $repository->saveAccount($account);

            $expectedAccount = $account;
            $actualAccount = $repository->getAccountById($account->getId());
            expect($expectedAccount)->toBe($actualAccount);
        });

        it('gets existing account by ID', function () use ($fqcn, $factory) {
            [$foundAccount, $notFoundAccount] = $accounts = $factory->count(2);
            /** @var AccountRepository */
            $repository = new $fqcn(...$accounts);

            $expectedAccount = $foundAccount;
            $actualAccount = $repository->getAccountById($expectedAccount->getId());
            expect($expectedAccount)->toBe($actualAccount);
        });

        it('does not get nonexistent account by ID', function () use ($fqcn, $factory) {
            $notFoundAccount = $factory->create();
            /** @var AccountRepository */
            $repository = new $fqcn($notFoundAccount);
            $repository->getAccountById('-1');
        })->throws(AccountNotFoundException::class);

        it('gets all accounts', function () use ($fqcn, $factory) {
            $accounts = $factory->count(2);
            /** @var AccountRepository */
            $repository = new $fqcn(...$accounts);

            $expectedAccounts = $accounts;
            $actualAccounts = iterator_to_array($repository->getAllAccounts());
            expect($expectedAccounts)->toBe($actualAccounts);
        });

        it('searches accounts by IDs', function () use ($fqcn, $factory) {
            $accounts = $factory->count(4);
            /** @var AccountRepository */
            $repository = new $fqcn(...$accounts);

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

        it('searches accounts by name', function () use ($fqcn, $factory) {
            $accounts = [
                $factory->create(name: 'Foo'),
                $factory->create(name: 'Foobar'),
                $factory->create(name: 'Barfoo'),
                $factory->create(name: 'Bar'),
            ];
            /** @var AccountRepository */
            $repository = new $fqcn(...$accounts);

            $expectedAccounts = array_slice($accounts, 0, 3);
            $criteria = new AccountSearchCriteria(
                name: $accounts[0]->getName(),
            );
            $actualAccounts = iterator_to_array(
                $repository->searchAccounts($criteria),
            );
            expect($expectedAccounts)->toBe($actualAccounts);
        });

        it('searches accounts by multiple criteria', function () use ($fqcn, $factory) {
            $accounts = [
                $factory->create(name: 'Foo'),
                $factory->create(name: 'Bar'),
                $factory->create(name: 'Baz'),
            ];
            /** @var AccountRepository */
            $repository = new $fqcn(...$accounts);

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

        it('searches all accounts', function () use ($fqcn, $factory) {
            $foundAccount = $factory->create();
            /** @var AccountRepository */
            $repository = new $fqcn($foundAccount);

            $expectedAccounts = [$foundAccount];
            $criteria = new AccountSearchCriteria;
            $actualAccounts = iterator_to_array(
                $repository->searchAccounts($criteria),
            );
            expect($expectedAccounts)->toBe($actualAccounts);
        });
    }
}

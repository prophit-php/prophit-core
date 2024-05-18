<?php

namespace Prophit\Core\Tests\Ledger;

use Prophit\Core\{
    Ledger\Ledger,
    Ledger\LedgerRepository,
    Exception\LedgerNotFoundException,
    Tests\Ledger\LedgerFactory,
};

class LedgerRepositoryTestFactory
{
    /**
     * @param class-string $fqcn FQCN of the LedgerRepository
     *        implementation to test
     */
    public static function createTests(string $fqcn): void
    {
        if (!class_exists($fqcn)) {
            throw LedgerRepositoryTestFactoryException::cannotResolveClass($fqcn);
        }

        if (!in_array(LedgerRepository::class, class_implements($fqcn))) {
            throw LedgerRepositoryTestFactoryException::classMissingInterface($fqcn);
        }

        $factory = new LedgerFactory;

        it('saves ledger', function () use ($fqcn, $factory) {
            $ledger = $factory->create();
            /** @var LedgerRepository */
            $repository = new $fqcn;
            $repository->saveLedger($ledger);

            $expectedLedger = $ledger;
            $actualLedger = $repository->getLedgerById($ledger->getId());
            expect($expectedLedger)->toBe($actualLedger);
        });

        it('gets existing ledger by ID', function () use ($fqcn, $factory) {
            [$foundLedger, $notFoundLedger] = $ledgers = $factory->count(2);
            /** @var LedgerRepository */
            $repository = new $fqcn(...$ledgers);

            $expectedLedger = $foundLedger;
            $actualLedger = $repository->getLedgerById($expectedLedger->getId());
            expect($expectedLedger)->toBe($actualLedger);
        });

        it('does not get nonexistent ledger by ID', function () use ($fqcn, $factory) {
            $notFoundLedger = $factory->create();
            /** @var LedgerRepository */
            $repository = new $fqcn($notFoundLedger);
            $repository->getLedgerById('-1');
        })->throws(LedgerNotFoundException::class);

        it('gets all ledgers', function () use ($fqcn, $factory) {
            $ledgers = $factory->count(2);
            /** @var LedgerRepository */
            $repository = new $fqcn(...$ledgers);

            $expectedLedgers = $ledgers;
            $actualLedgers = iterator_to_array($repository->getAllLedgers());
            expect($expectedLedgers)->toBe($actualLedgers);
        });
    }
}

<?php

namespace Prophit\Core\Tests\Account;

use DateTimeInterface;

use function Pest\Faker\fake;

use Prophit\Core\{
    Account\Account,
    Account\AccountStatus,
    Ledger\Ledger,
    Tests\Ledger\LedgerFactory,
};

class AccountFactory
{
    private LedgerFactory $ledgerFactory;

    private int $lastId;

    public function __construct(?LedgerFactory $ledgerFactory = null)
    {
        $this->ledgerFactory = $ledgerFactory ?? new LedgerFactory;
        $this->lastId = 0;
    }

    public function create(
        ?string $id = null,
        ?Ledger $ledger = null,
        ?string $name = null,
        ?string $currency = null,
        ?AccountStatus $status = null,
    ): Account {
        $id ??= (string) ++$this->lastId;
        $ledger ??= $this->ledgerFactory->create();
        if ($name === null) {
            /** @var string */
            $randomName = fake()->words(rand(1, 3), true);
            $name = ucfirst($randomName);
        }
        $currency ??= fake()->currencyCode();
        $status ??= AccountStatus::Active;
        return new Account($id, $ledger, $name, $currency, $status);
    }

    /**
     * @return Account[]
     */
    public function count(int $count): array
    {
        return array_map(fn() => $this->create(), range(1, $count));
    }
}

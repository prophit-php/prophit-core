<?php

namespace Prophit\Core\Tests\Account;

use DateTimeInterface;

use function Pest\Faker\fake;

use Prophit\Core\Account\{
    Account,
    AccountStatus,
};

class AccountFactory
{
    private int $lastId;

    public function __construct()
    {
        $this->lastId = 0;
    }

    public function create(
        ?string $id = null,
        ?string $name = null,
        ?string $currency = null,
        ?AccountStatus $status = null,
    ): Account {
        $id ??= (string) ++$this->lastId;
        if ($name === null) {
            /** @var string */
            $randomName = fake()->words(rand(1, 3), true);
            $name = ucfirst($randomName);
        }
        $currency ??= fake()->currencyCode();
        $status ??= AccountStatus::Active;
        return new Account($id, $name, $currency, $status);
    }

    /**
     * @return Account[]
     */
    public function count(int $count): array
    {
        return array_map(fn() => $this->create(), range(1, $count));
    }
}

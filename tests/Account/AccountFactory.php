<?php

namespace Prophit\Core\Tests\Account;

use DateTimeInterface;

use Faker\{
    Factory,
    Generator,
};

use Prophit\Core\Account\Account;

class AccountFactory
{
    private Generator $faker;

    private int $lastId;

    public function __construct()
    {
        $this->faker = Factory::create();
        $this->lastId = 0;
    }

    public function create(
        ?string $id = null,
        ?string $name = null,
        ?string $currency = null,
    ): Account {
        $id ??= (string) ++$this->lastId;
        if ($name === null) {
            /** @var string */
            $randomName = $this->faker->words(rand(1, 3), true);
            $name = ucfirst($randomName);
        }
        $currency ??= $this->faker->currencyCode();
        return new Account($id, $name, $currency);
    }

    /**
     * @return Account[]
     */
    public function count(int $count): array
    {
        return array_map(fn() => $this->create(), range(1, $count));
    }
}

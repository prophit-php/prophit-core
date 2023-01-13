<?php

namespace Prophit\Core\Tests\Account;

use DateTimeInterface;

use Faker\{
    Factory,
    Generator,
};

use Prophit\Core\{
    Account\Account,
    Tests\User\UserFactory,
    User\SimpleUser,
    User\User,
};

class AccountFactory
{
    private Generator $faker;

    private UserFactory $userFactory;

    private int $lastId;

    public function __construct()
    {
        $this->faker = Factory::create();
        $this->userFactory = new UserFactory;
        $this->lastId = 0;
    }

    public function create(
        ?string $id = null,
        ?string $name = null,
        ?DateTimeInterface $modifiedDate = null,
        ?User $modifiedUser = null,
    ): Account {
        if ($id === null) {
            $id = (string) ++$this->lastId;
        }

        if ($name === null) {
            /** @var string */
            $randomName = $this->faker->words(rand(1, 3), true);
            $name = ucfirst($randomName);
        }

        if ($modifiedDate === null) {
            $modifiedDate = $this->faker->dateTime();
        }

        if ($modifiedUser === null) {
            $modifiedUser = $this->userFactory->create();
        }

        return new Account($id, $name, $modifiedDate, $modifiedUser);
    }

    /**
     * @return Account[]
     */
    public function count(int $count): array
    {
        return array_map(fn() => $this->create(), range(1, $count));
    }
}

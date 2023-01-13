<?php

namespace Prophit\Core\Tests\Posting;

use DateTime;
use DateTimeInterface;

use Faker\{
    Factory,
    Generator,
};

use Prophit\Core\{
    Account\Account,
    Money\Money,
    Posting\Posting,
    Tests\Account\AccountFactory,
    Tests\User\UserFactory,
    User\User,
};

class PostingFactory
{
    private Generator $faker;

    private AccountFactory $accountFactory;

    private UserFactory $userFactory;

    private int $lastId;

    public function __construct()
    {
        $this->faker = Factory::create();
        $this->accountFactory = new AccountFactory;
        $this->userFactory = new UserFactory;
        $this->lastId = 0;
    }

    public function create(
        ?string $id = null,
        ?Account $account = null,
        ?Money $amount = null,
        ?DateTimeInterface $modifiedDate = null,
        ?User $modifiedUser = null,
        ?DateTimeInterface $clearedDate = null,
    ): Posting {
        if ($id === null) {
            $id = (string) ++$this->lastId;
        }
        if ($account === null) {
            $account = $this->accountFactory->create();
        }
        if ($amount === null) {
            $amount = new Money($this->lastId, 'USD');
        }
        if ($modifiedDate === null) {
            $modifiedDate = $this->faker->dateTime();
        }
        if ($modifiedUser === null) {
            $modifiedUser = $this->userFactory->create();
        }
        return new Posting(
            $id,
            $account,
            $amount,
            $modifiedDate,
            $modifiedUser,
            $clearedDate,
        );
    }

    /**
     * @return Posting[]
     */
    public function count(int $count): array
    {
        return array_map(fn() => $this->create(), range(1, $count));
    }
}

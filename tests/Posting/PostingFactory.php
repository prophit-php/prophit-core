<?php

namespace Prophit\Core\Tests\Posting;

use Brick\Money\Money;

use DateTime;
use DateTimeInterface;

use Faker\{
    Factory,
    Generator,
};

use Prophit\Core\{
    Account\Account,
    Posting\Posting,
    Tests\Account\AccountFactory,
};

class PostingFactory
{
    private Generator $faker;

    private AccountFactory $accountFactory;

    private int $lastId;

    public function __construct()
    {
        $this->faker = Factory::create();
        $this->accountFactory = new AccountFactory;
        $this->lastId = 0;
    }

    public function create(
        ?string $id = null,
        ?Account $account = null,
        ?Money $amount = null,
        ?DateTimeInterface $createdDate = null,
        ?DateTimeInterface $clearedDate = null,
    ): Posting {
        if ($id === null) {
            $id = (string) ++$this->lastId;
        }
        if ($account === null) {
            $account = $this->accountFactory->create();
        }
        if ($amount === null) {
            $amount = Money::of($this->lastId, 'USD');
        }
        if ($createdDate === null) {
            $createdDate = $this->faker->dateTime();
        }
        return new Posting(
            $id,
            $account,
            $amount,
            $createdDate,
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

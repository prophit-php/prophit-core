<?php

namespace Prophit\Core\Tests\Transaction;

use DateTime;
use DateTimeInterface;

use Prophit\Core\{
    Account\Account,
    Money\Money,
    Tests\Account\AccountFactory,
    Transaction\Posting,
};

use function Pest\Faker\fake;

class PostingFactory
{
    private AccountFactory $accountFactory;

    private int $lastId;

    public function __construct()
    {
        $this->accountFactory = new AccountFactory;
        $this->lastId = 0;
    }

    public function create(
        ?string $id = null,
        ?Account $account = null,
        ?Money $amount = null,
        ?DateTimeInterface $clearedDate = null,
    ): Posting {
        $id ??= (string) ++$this->lastId;

        $account ??= $this->accountFactory->create();

        $amount ??= new Money($this->lastId, 'USD');

        $clearedDate ??= new DateTime(
            fake()
                ->dateTimeBetween('-2 months')
                ->format('Y-m-d 00:00:00')
        );

        return new Posting(
            $id,
            $account,
            $amount,
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

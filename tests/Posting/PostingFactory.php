<?php

namespace Prophit\Core\Tests\Posting;

use DateTime;
use DateTimeInterface;

use Prophit\Core\{
    Account\Account,
    Money\Money,
    Posting\Posting,
    Tests\Account\AccountFactory,
};

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
        if ($id === null) {
            $id = (string) ++$this->lastId;
        }
        if ($account === null) {
            $account = $this->accountFactory->create();
        }
        if ($amount === null) {
            $amount = new Money($this->lastId, 'USD');
        }
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

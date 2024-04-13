<?php

namespace Prophit\Core\Tests\Ledger;

use function Pest\Faker\fake;

use Prophit\Core\Ledger\{
    Ledger,
    LedgerStatus,
};

class LedgerFactory
{
    private int $lastId;

    public function __construct()
    {
        $this->lastId = 0;
    }

    public function create(
        ?string $id = null,
        ?string $name = null,
        ?LedgerStatus $status = null,
    ): Ledger {
        $id ??= (string) ++$this->lastId;
        if ($name === null) {
            /** @var string */
            $randomName = fake()->words(rand(1, 3), true);
            $name = ucfirst($randomName);
        }
        $status ??= LedgerStatus::Active;
        return new Ledger($id, $name, $status);
    }

    /**
     * @return Ledger[]
     */
    public function count(int $count): array
    {
        return array_map(fn() => $this->create(), range(1, $count));
    }
}

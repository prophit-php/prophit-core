<?php

namespace Prophit\Core\Account;

use Prophit\Core\Ledger\Ledger;

class AccountSearchCriteria
{
    /**
     * @param string[]|null $ids
     * @param Ledger[]|null $ledgers
     */
    public function __construct(
        private ?array $ids = null,
        private ?string $name = null,
        private ?array $ledgers = null,
    ) { }

    /** @return string[]|null */
    public function getIds(): ?array
    {
        return $this->ids;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return Ledger[]|null
     */
    public function getLedgers(): ?array
    {
        return $this->ledgers;
    }

    public function hasCriteria(): bool
    {
        return !(
            $this->ids === null
            && $this->name === null
            && $this->ledgers === null
        );
    }
}

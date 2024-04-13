<?php

namespace Prophit\Core\Account;

class AccountSearchCriteria
{
    /**
     * @param string[] $ids
     */
    public function __construct(
        private ?array $ids = null,
        private ?string $name = null,
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

    public function hasCriteria(): bool
    {
        return !(
            $this->ids === null
            && $this->name === null
        );
    }
}

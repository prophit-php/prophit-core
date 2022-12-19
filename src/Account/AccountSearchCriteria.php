<?php

namespace Prophit\Core\Account;

class AccountSearchCriteria
{
    /**
     * @param string[] $ids
     * @param string[] $parentIds
     */
    public function __construct(
        private ?array $ids = null,
        private ?string $name = null,
        private ?array $parentIds = null,
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

    /** @return string[]|null */
    public function getParentIds(): ?array
    {
        return $this->parentIds;
    }
}

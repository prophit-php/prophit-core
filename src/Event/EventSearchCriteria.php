<?php

namespace Prophit\Core\Event;

use DateTimeInterface;

use Prophit\Core\{
    Date\DateRange,
    Ledger\Ledger,
    User\User,
};

class EventSearchCriteria
{
    /**
     * @param string[]|null $ids
     * @param Ledger[]|null $ledgers
     * @param EventType[]|null $types
     * @param EventEntityType[]|null $entityTypes
     * @param string[]|null $entityIds
     * @param array<string, mixed>|null $metadata
     * @param User[]|null $createdBy
     */
    public function __construct(
        private ?array $ids = null,
        private ?array $ledgers = null,
        private ?array $types = null,
        private ?array $entityTypes = null,
        private ?array $entityIds = null,
        private ?array $metadata = null,
        private ?array $createdBy = null,
        private DateTimeInterface|DateRange|null $createdAt = null,
    ) { }

    /**
     * @return string[]|null
     */
    public function getIds(): ?array
    {
        return $this->ids;
    }

    /**
     * @return Ledger[]|null
     */
    public function getLedgers(): ?array
    {
        return $this->ledgers;
    }

    /**
     * @return EventType[]|null
     */
    public function getTypes(): ?array
    {
        return $this->types;
    }

    /**
     * @return EventEntityType[]|null
     */
    public function getEntityTypes(): ?array
    {
        return $this->entityTypes;
    }

    /**
     * @return string[]|null
     */
    public function getEntityIds(): ?array
    {
        return $this->entityIds;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    /**
     * @return User[]|null
     */
    public function getCreatedBy(): ?array
    {
        return $this->createdBy;
    }

    public function getCreatedAt(): DateTimeInterface|DateRange|null
    {
        return $this->createdAt;
    }

    public function hasCriteria(): bool
    {
        return !(
            $this->ids === null
            && $this->ledgers === null
            && $this->types === null
            && $this->entityTypes === null
            && $this->entityIds === null
            && $this->metadata === null
            && $this->createdBy === null
            && $this->createdAt === null
        );
    }
}

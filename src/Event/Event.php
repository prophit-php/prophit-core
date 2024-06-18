<?php

namespace Prophit\Core\Event;

use DateTimeInterface;

use Prophit\Core\{
    Ledger\Ledger,
    User\User,
};

class Event
{
    public function __construct(
        private string $id,
        private Ledger $ledger,
        private EventType $type,
        private EventEntityType $entityType,
        private string $entityId,
        private array $metadata,
        private User $createdBy,
        private DateTimeInterface $createdAt,
    ) { }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLedger(): Ledger
    {
        return $this->ledger;
    }

    public function getType(): EventType
    {
        return $this->type;
    }

    public function getEntityId(): string
    {
        return $this->entityId;
    }

    public function getEntityType(): EventEntityType
    {
        return $this->entityType;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
}

<?php

namespace Prophit\Core\Event;

use Prophit\Core\Ledger\Ledger;

class ArrayEventRepository implements EventRepository
{
    /**
     * Mapping of events by ledger ID and event ID
     *
     * @var array<string, array<string, Event>>
     */
    private array $events;

    public function __construct(Event... $events)
    {
        $this->events = [];
        foreach ($events as $event) {
            $this->saveEvent($event);
        }
    }

    public function saveEvent(Event $event): void
    {
        $ledgerId = $event->getLedger()->getId();
        $eventId = $event->getId();

        $this->events[$ledgerId] ??= [];
        $this->events[$ledgerId][$eventId] = $event;
    }

    public function searchEvents(EventSearchCriteria $criteria): iterable
    {
        foreach ($this->events as $ledgerId => $events) {
            foreach ($events as $event) {
                if ($this->eventMatches($event, $criteria)) {
                    yield $event;
                }
            }
        }
    }

    private function eventMatches(Event $event, EventSearchCriteria $criteria): bool
    {
        return
            !$criteria->hasCriteria() ||
            $this->idsMatch($event, $criteria) ||
            $this->ledgersMatch($event, $criteria) ||
            $this->typesMatch($event, $criteria) ||
            $this->entityTypesMatch($event, $criteria) ||
            $this->entityIdsMatch($event, $criteria) ||
            $this->metadataMatch($event, $criteria) ||
            $this->createdByMatches($event, $criteria) ||
            $this->createdAtMatches($event, $criteria);
    }

    private function idsMatch(Event $event, EventSearchCriteria $criteria): bool
    {
        $ids = $criteria->getIds();
        return is_array($ids) && in_array($event->getId(), $ids);
    }

    private function ledgersMatch(Event $event, EventSearchCriteria $criteria): bool
    {
        $eventLedger = $event->getLedger();
        $ledgers = $criteria->getLedgers();
        if (is_array($ledgers) && count($ledgers) > 0) {
            foreach ($ledgers as $ledger) {
                if ($ledger->isSame($eventLedger)) {
                    return true;
                }
            }
        }
        return false;
    }

    private function typesMatch(Event $event, EventSearchCriteria $criteria): bool
    {
        return false;
    }

    private function entityTypesMatch(Event $event, EventSearchCriteria $criteria): bool
    {
        return false;
    }

    private function entityIdsMatch(Event $event, EventSearchCriteria $criteria): bool
    {
        return false;
    }

    private function metadataMatch(Event $event, EventSearchCriteria $criteria): bool
    {
        return false;
    }

    private function createdByMatches(Event $event, EventSearchCriteria $criteria): bool
    {
        return false;
    }

    private function createdAtMatches(Event $event, EventSearchCriteria $criteria): bool
    {
        return false;
    }
}

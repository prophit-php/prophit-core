<?php

namespace Prophit\Core\Event;

use Prophit\Core\Ledger\Ledger;

interface EventRepository
{
    public function saveEvent(Event $event): void;

    /**
     * @return iterable<Event>
     */
    public function searchEvents(EventSearchCriteria $criteria): iterable;
}

<?php

namespace Prophit\Core\Event;

enum EventEntityType: int
{
    case ACCOUNT = 1;
    case LEDGER = 2;
    case TRANSACTION = 3;
}

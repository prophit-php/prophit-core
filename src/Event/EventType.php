<?php

namespace Prophit\Core\Event;

enum EventType: int
{
    case CREATE_ACCOUNT = 1;
    case UPDATE_ACCOUNT = 2;
    case DELETE_ACCOUNT = 3;

    case CREATE_LEDGER = 4;
    case UPDATE_LEDGER = 5;
    case DELETE_LEDGER = 6;

    case CREATE_TRANSACTION = 7;
    case UPDATE_TRANSACTION = 8;
    case DELETE_TRANSACTION = 9;
}

<?php

namespace Prophit\Core\Ledger;

enum LedgerStatus: int
{
    case Deleted = 0;
    case Active = 1;
    case Locked = 2;
}

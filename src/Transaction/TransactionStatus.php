<?php

namespace Prophit\Core\Transaction;

enum TransactionStatus: int
{
    case Deleted = 0;
    case Active = 1;
}

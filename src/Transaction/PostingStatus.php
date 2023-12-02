<?php

namespace Prophit\Core\Transaction;

enum PostingStatus: int
{
    case Deleted = 0;
    case Active = 1;
}

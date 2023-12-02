<?php

namespace Prophit\Core\Account;

enum AccountStatus: int
{
    case Deleted = 0;
    case Active = 1;
}

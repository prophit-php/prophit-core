<?php

namespace Prophit\Core\User;

enum UserStatus: int
{
    case Deleted = 0;
    case Active = 1;
    case Locked = 2;
}

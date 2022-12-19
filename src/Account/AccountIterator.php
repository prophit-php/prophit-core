<?php

namespace Prophit\Core\Account;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/** @implements IteratorAggregate<int, Account> */
class AccountIterator implements IteratorAggregate
{
    /** @var Account[] */
    private array $accounts;

    public function __construct(Account... $accounts)
    {
        $this->accounts = $accounts;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->accounts);
    }
}

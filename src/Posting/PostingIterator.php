<?php

namespace Prophit\Core\Posting;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/** @implements IteratorAggregate<int, Posting> */
class PostingIterator implements IteratorAggregate
{
    /** @var Posting[] */
    private array $postings;

    public function __construct(Posting... $postings)
    {
        $this->postings = $postings;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->postings);
    }
}

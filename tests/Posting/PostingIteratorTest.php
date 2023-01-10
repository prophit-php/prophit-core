<?php

use Brick\Money\Money;

use Prophit\Core\{
    Account\Account,
    Posting\Posting,
    Posting\PostingIterator,
    Tests\Posting\PostingFactory,
};

beforeEach(function () {
    $this->factory = new PostingFactory;
});

it('iterates', function () {
    $posting = $this->factory->create();
    $iterator = new PostingIterator($posting);
    expect(iterator_to_array($iterator))->toBe([$posting]);
});

<?php

use Brick\Money\Money;

use Prophit\Core\{
    Account\Account,
    Account\AccountIterator,
    Date\DateRange,
    Exception\PostingNotFoundException,
    Money\MoneyRange,
    Posting\ArrayPostingRepository,
    Posting\Posting,
    Posting\PostingSearchCriteria,
    Tests\Posting\PostingFactory,
};

beforeEach(function () {
    $this->factory = new PostingFactory;
});

it('saves and gets posting', function () {
    $posting = $this->factory->create();
    $repository = new ArrayPostingRepository;
    $repository->savePosting($posting);
    $result = $repository->getPostingById($posting->getId());
    expect($result)->toBe($posting);
});

it('saves postings passed to the constructor', function () {
    $posting = $this->factory->create();
    $repository = new ArrayPostingRepository($posting);
    $result = $repository->getPostingById($posting->getId());
    expect($result)->toBe($posting);
});

it('does not get nonexistent posting', function () {
    (new ArrayPostingRepository)->getPostingById('1');
})->throws(PostingNotFoundException::class);

it('searches by ID', function () {
    $posting = $this->factory->create();
    $repository = new ArrayPostingRepository($posting);

    $criteria = new PostingSearchCriteria(ids: [$posting->getId()]);
    $results = $repository->searchPostings($criteria);
    expect(iterator_to_array($results))->toBe([$posting]);

    $criteria = new PostingSearchCriteria(ids: ['-1']);
    $results = $repository->searchPostings($criteria);
    expect(iterator_to_array($results))->toBeEmpty();
});

it('searches by account', function () {
    [$posting, $otherPosting] = $this->factory->count(2);
    $repository = new ArrayPostingRepository($posting, $otherPosting);

    $criteria = new PostingSearchCriteria(accounts: new AccountIterator($posting->getAccount()));
    $results = $repository->searchPostings($criteria);
    expect(iterator_to_array($results))->toBe([$posting]);

    $criteria = new PostingSearchCriteria(accounts: new AccountIterator($otherPosting->getAccount()));
    $results = $repository->searchPostings($criteria);
    expect(iterator_to_array($results))->toBe([$otherPosting]);
});

it('searches by single amount', function () {
    $posting = $this->factory->create(amount: Money::of(1, 'USD'));
    $otherPosting = $this->factory->create(amount: Money::of(2, 'USD'));
    $repository = new ArrayPostingRepository($posting, $otherPosting);

    $criteria = new PostingSearchCriteria(amounts: $posting->getAmount());
    $results = $repository->searchPostings($criteria);
    expect(iterator_to_array($results))->toBe([$posting]);
});


it('searches by amount range', function () {
    $posting = $this->factory->create(amount: Money::of(2, 'USD'));
    $otherPosting = $this->factory->create(amount: Money::of(4, 'USD'));
    $repository = new ArrayPostingRepository($posting, $otherPosting);

    $amounts = new MoneyRange(Money::of(1, 'USD'), Money::of(3, 'USD'));
    $criteria = new PostingSearchCriteria(amounts: $amounts);
    $results = $repository->searchPostings($criteria);
    expect(iterator_to_array($results))->toBe([$posting]);
});

it('searches by single modified date', function () {
    $posting = $this->factory->create();
    $otherPosting = $this->factory->create(modifiedDate: new DateTime('-1 day'));
    $repository = new ArrayPostingRepository($posting, $otherPosting);

    $criteria = new PostingSearchCriteria(modifiedDates: $posting->getModifiedDate());
    $results = $repository->searchPostings($criteria);
    expect(iterator_to_array($results))->toBe([$posting]);
});

it('searches by modified date range', function () {
    $min = new DateTime('-2 days');
    $max = new DateTime;

    $posting = $this->factory->create(modifiedDate: new DateTime('-1 day'));
    $otherPosting = $this->factory->create(modifiedDate: new DateTime('-4 days'));
    $repository = new ArrayPostingRepository($posting, $otherPosting);

    $modifiedDates = new DateRange($min, $max);
    $criteria = new PostingSearchCriteria(modifiedDates: $modifiedDates);
    $results = $repository->searchPostings($criteria);
    expect(iterator_to_array($results))->toBe([$posting]);
});

it('searches by single cleared date', function () {
    $posting = $this->factory->create(clearedDate: new DateTime);
    $otherPosting = $this->factory->create(clearedDate: new DateTime('-1 day'));
    $repository = new ArrayPostingRepository($posting, $otherPosting);

    $criteria = new PostingSearchCriteria(clearedDates: $posting->getClearedDate());
    $results = $repository->searchPostings($criteria);
    expect(iterator_to_array($results))->toBe([$posting]);
});

it('searches by cleared date range', function () {
    $min = new DateTime('-2 days');
    $max = new DateTime;

    $posting = $this->factory->create(clearedDate: new DateTime('-1 day'));
    $otherPosting = $this->factory->create(clearedDate: new DateTime('-4 days'));
    $repository = new ArrayPostingRepository($posting, $otherPosting);

    $clearedDates = new DateRange($min, $max);
    $criteria = new PostingSearchCriteria(clearedDates: $clearedDates);
    $results = $repository->searchPostings($criteria);
    expect(iterator_to_array($results))->toBe([$posting]);
});

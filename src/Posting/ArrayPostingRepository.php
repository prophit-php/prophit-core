<?php

namespace Prophit\Core\Posting;

use Brick\Money\Money;

use DateTimeInterface;

use Prophit\Core\{
    Account\AccountIterator,
    Date\DateRange,
    Exception\PostingNotFoundException,
    Money\MoneyRange,
};

class ArrayPostingRepository implements PostingRepository
{
    /** @var array<string, Posting> **/
    private array $postings;

    public function __construct(Posting... $postings)
    {
        $this->postings = [];
        foreach ($postings as $posting) {
            $this->savePosting($posting);
        }
    }

    public function savePosting(Posting $posting): void
    {
        $this->postings[$posting->getId()] = $posting;
    }

    /**
     * @throws PostingNotFoundException if posting is not found
     */
    public function getPostingById(string $id): Posting
    {
        if (!isset($this->postings[$id])) {
            throw new PostingNotFoundException($id);
        }
        return $this->postings[$id];
    }

    public function searchPostings(PostingSearchCriteria $criteria): PostingIterator
    {
        $postings = array_reduce(
            [
                fn($postings) => $this->filterPostingsById($postings, $criteria->getIds()),
                fn($postings) => $this->filterPostingsByAccounts($postings, $criteria->getAccounts()),
                fn($postings) => $this->filterPostingsByAmounts($postings, $criteria->getAmounts()),
                fn($postings) => $this->filterPostingsByModifiedDates($postings, $criteria->getModifiedDates()),
                fn($postings) => $this->filterPostingsByClearedDates($postings, $criteria->getClearedDates()),
            ],
            fn(array $postings, callable $callback) => $callback($postings),
            $this->postings,
        );
        return new PostingIterator(...$postings);
    }

    /**
     * @param Posting[] $postings
     * @param string[]|null $ids
     * @return Posting[]
     */
    private function filterPostingsById(array $postings, ?array $ids): array
    {
        if ($ids === null) {
            return $postings;
        }
        return array_filter(
            $postings,
            fn(Posting $posting): bool => in_array($posting->getId(), $ids),
        );
    }

    /**
     * @param Posting[] $postings
     * @param AccountIterator|null $accounts
     * @return Posting[]
     */
    private function filterPostingsByAccounts(array $postings, ?AccountIterator $accounts): array
    {
        if ($accounts === null) {
            return $postings;
        }
        return array_filter(
            $postings,
            fn(Posting $posting): bool => $accounts->contains($posting->getAccount()),
        );
    }

    /**
     * @param Posting[] $postings
     * @param Money|MoneyRange|null $amounts
     * @return Posting[]
     */
    private function filterPostingsByAmounts(array $postings, Money|MoneyRange|null $amounts): array
    {
        if ($amounts === null) {
            return $postings;
        }
        $callback = $amounts instanceof Money
            ? fn(Posting $posting): bool => $amounts->isEqualTo($posting->getAmount())
            : fn(Posting $posting): bool => $amounts->contains($posting->getAmount());
        return array_filter($postings, $callback);
    }

    /**
     * @param Posting[] $postings
     * @param callable(Posting): ?DateTimeInterface $getPostingDate
     * @param DateTimeInterface|DateRange|null $dates
     * @return Posting[]
     */
    private function filterPostingsByDates(array $postings, callable $getPostingDate, DateTimeInterface|DateRange|null $dates): array
    {
        if ($dates === null) {
            return $postings;
        }
        if ($dates instanceof DateTimeInterface) {
            $formatDate = fn(DateTimeInterface $date): string => $date->format('Y-m-d');
            $formatted = $formatDate($dates);
            $callback = function (Posting $posting) use ($getPostingDate, $formatDate, $formatted): bool {
                $date = $getPostingDate($posting);
                return $date !== null && $formatDate($date) === $formatted;
            };
        } else {
            $callback = function (Posting $posting) use ($getPostingDate, $dates): bool {
                $date = $getPostingDate($posting);
                return $date !== null && $dates->contains($date);
            };
        }
        return array_filter($postings, $callback);
    }

    /**
     * @param Posting[] $postings
     * @param DateTimeInterface|DateRange|null $modifiedDates
     * @return Posting[]
     */
    private function filterPostingsByModifiedDates(array $postings, DateTimeInterface|DateRange|null $modifiedDates): array
    {
        return $this->filterPostingsByDates(
            $postings,
            fn(Posting $posting): DateTimeInterface => $posting->getModifiedDate(),
            $modifiedDates,
        );
    }

    /**
     * @param Posting[] $postings
     * @param DateTimeInterface|DateRange|null $clearedDates
     * @return Posting[]
     */
    private function filterPostingsByClearedDates(array $postings, DateTimeInterface|DateRange|null $clearedDates): array
    {
        return $this->filterPostingsByDates(
            $postings,
            fn(Posting $posting): ?DateTimeInterface => $posting->getClearedDate(),
            $clearedDates,
        );
    }
}

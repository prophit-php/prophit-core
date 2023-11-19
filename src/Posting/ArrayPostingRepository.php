<?php

namespace Prophit\Core\Posting;

use DateTimeInterface;

use Prophit\Core\{
    Date\DateRange,
    Exception\PostingNotFoundException,
    Money\Money,
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

    /**
     * @return iterable<Posting>
     */
    public function searchPostings(PostingSearchCriteria $criteria): iterable
    {
        $ids = $criteria->getIds();
        $accountIds = $criteria->getAccountIds();
        $amounts = $criteria->getAmounts();
        $clearedDates = $criteria->getClearedDates();
        foreach ($this->postings as $posting) {
            if (
                (is_array($ids) && in_array($posting->getId(), $ids)) ||
                (is_array($accountIds) && in_array($posting->getAccount()->getId(), $accountIds)) ||
                ($amounts instanceof Money && $amounts->isEqualTo($posting->getAmount())) ||
                ($amounts instanceof MoneyRange && $amounts->contains($posting->getAmount())) ||
                ($clearedDates instanceof DateTimeInterface &&
                    $clearedDates->format('Y-m-d') === $posting->getClearedDate()?->format('Y-m-d')) ||
                ($clearedDates instanceof DateRange &&
                    $posting->getClearedDate() instanceof DateTimeInterface &&
                    $clearedDates->contains($posting->getClearedDate()))
            ) {
                yield $posting;
            }
        }
    }
}

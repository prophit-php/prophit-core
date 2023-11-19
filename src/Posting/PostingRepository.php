<?php

namespace Prophit\Core\Posting;

use Prophit\Core\Exception\PostingNotFoundException;

interface PostingRepository
{
    public function savePosting(Posting $posting): void;

    /**
     * @throws PostingNotFoundException if posting is not found
     */
    public function getPostingById(string $id): Posting;

    /**
     * @return iterable<Posting>
     */
    public function searchPostings(PostingSearchCriteria $criteria): iterable;
}

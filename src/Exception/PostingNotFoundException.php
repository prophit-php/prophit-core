<?php

namespace Prophit\Core\Exception;

class PostingNotFoundException extends CoreException
{
    public function __construct(string $id)
    {
        parent::__construct('Posting not found: ' . $id);
    }
}

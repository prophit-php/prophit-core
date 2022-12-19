<?php

namespace Prophit\Core\Account;

use loophp\phptree\Traverser\TraverserInterface;
use loophp\phptree\Node\ValueNode;

class AccountTreeNode extends ValueNode
{
    public function __construct(
        Account $account,
        ?TraverserInterface $traverser = null,
        ?AccountTreeNode $parent = null,
    ) {
        parent::__construct($account, 0, $traverser, $parent);
    }
}

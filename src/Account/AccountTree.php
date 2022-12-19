<?php

namespace Prophit\Core\Account;

use loophp\phptree\Traverser\PreOrder;
use IteratorAggregate;
use Traversable;

/** @implements IteratorAggregate<int, AccountTreeNode> **/
class AccountTree implements IteratorAggregate
{
    /** @var AccountTreeNode[]|null **/
    private ?array $rootNodes = null;

    public function __construct(
        private AccountIterator $accounts,
    ) { }

    public function getIterator(): Traversable
    {
        if ($this->rootNodes === null) {
            $this->rootNodes = $this->buildTree();
        }
        foreach ($this->rootNodes as $rootNode) {
            /** @var AccountTreeNode $currentNode **/
            foreach ($rootNode as $currentNode) {
                yield $currentNode;
            }
        }
    }

    /** @return AccountTreeNode[] */
    private function buildTree(): array
    {
        /** @var array<string, Account[]> */
        $parentIdToChildrenAccountsMap = [];

        /** @var Account[] */
        $rootAccounts = [];

        /** @var array<string, AccountTreeNode> */
        $accountIdToTreeNodeMap = [];

        $traverser = new PreOrder;

        foreach ($this->accounts as $account) {
            $accountId = $account->getId();
            $accountIdToTreeNodeMap[$accountId] = new AccountTreeNode($account, $traverser);
            if ($account->hasParent()) {
                $parentId = $account->getParentId();
                if (!isset($parentIdToChildrenAccountsMap[$parentId])) {
                    $parentIdToChildrenAccountsMap[$parentId] = [];
                }
                $parentIdToChildrenAccountsMap[$parentId][] = $account;
            } else {
                $rootAccounts[] = $account;
            }
        }

        $accountToNode = fn(Account $account): AccountTreeNode => $accountIdToTreeNodeMap[$account->getId()];
        $sortByName = fn(Account $a, Account $b): int => $a->getName() <=> $b->getName();

        usort($rootAccounts, $sortByName);
        $rootNodes = array_map($accountToNode, $rootAccounts);

        foreach ($parentIdToChildrenAccountsMap as $parentId => $childAccounts) {
            usort($childAccounts, $sortByName);
            $parentNode = $accountIdToTreeNodeMap[$parentId];
            $childNodes = array_map($accountToNode, $childAccounts);
            $parentNode->add(...$childNodes);
        }

        return $rootNodes;
    }
}

<?php

use Prophit\Core\Transaction\ArrayTransactionRepository;
use Prophit\Core\Tests\Transaction\TransactionRepositoryTestFactory;

TransactionRepositoryTestFactory::createTests(ArrayTransactionRepository::class);

<?php

use Prophit\Core\{
    Account\ArrayAccountRepository,
    Tests\Account\AccountRepositoryTestFactory,
};

AccountRepositoryTestFactory::createTests(ArrayAccountRepository::class);

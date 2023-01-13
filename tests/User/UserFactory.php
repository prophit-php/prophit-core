<?php

namespace Prophit\Core\Tests\User;

use Faker\{
    Factory,
    Generator,
};

use Prophit\Core\User\{
    SimpleUser,
    User,
};

class UserFactory
{
    private Generator $faker;

    private int $lastId;

    public function __construct()
    {
        $this->faker = Factory::create();
        $this->lastId = 0;
    }

    public function create(
        ?string $id = null,
        ?string $displayName = null,
    ): User {
        if ($id === null) {
            $id = (string) ++$this->lastId;
        }

        if ($displayName === null) {
            $displayName = $this->faker->firstName();
        }

        return new SimpleUser($id, $displayName);
    }
}

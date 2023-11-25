<?php

namespace Prophit\Core\Tests\User;

use function Pest\Faker\fake;

use Prophit\Core\User\{
    SimpleUser,
    User,
};

class UserFactory
{
    private int $lastId;

    public function __construct()
    {
        $this->lastId = 0;
    }

    public function create(
        ?string $id = null,
        ?string $displayName = null,
    ): User {
        $id ??= (string) ++$this->lastId;
        $displayName ??= fake()->firstName();
        return new SimpleUser($id, $displayName);
    }
}

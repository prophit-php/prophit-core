<?php

namespace Prophit\Core\Tests\User;

use function Pest\Faker\fake;

use Prophit\Core\User\{
    User,
    UserStatus,
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
        ?UserStatus $status = null,
    ): User {
        $id ??= (string) ++$this->lastId;
        $displayName ??= fake()->firstName();
        $status ??= UserStatus::Active;
        return new User($id, $displayName, $status);
    }
}

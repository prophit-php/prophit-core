<?php

use Prophit\Core\{
    User\SimpleUser,
    Tests\User\UserFactory,
};

beforeEach(function () {
    $this->factory = new UserFactory;
});

it('gets ID', function () {
    $id = '1';
    $user = $this->factory->create(id: $id);
    expect($user->getId())->toBe($id);
});

it('gets display name', function () {
    $displayName = 'Foo';
    $user = $this->factory->create(displayName: $displayName);
    expect($user->getDisplayName())->toBe($displayName);
});

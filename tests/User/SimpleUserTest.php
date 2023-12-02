<?php

use Prophit\Core\{
    User\SimpleUser,
    User\UserStatus,
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

it('gets status', function () {
    $user = $this->factory->create();
    expect($user->getStatus())->toBe(UserStatus::Active);
});

it('is active', function () {
    $user = $this->factory->create();
    expect($user->isActive())->toBe(true);
    expect($user->isDeleted())->toBe(false);
    expect($user->isLocked())->toBe(false);
});

it('is deleted', function () {
    $user = $this->factory->create(status: UserStatus::Deleted);
    expect($user->isActive())->toBe(false);
    expect($user->isDeleted())->toBe(true);
    expect($user->isLocked())->toBe(false);
});

it('is locked', function () {
    $user = $this->factory->create(status: UserStatus::Locked);
    expect($user->isActive())->toBe(false);
    expect($user->isDeleted())->toBe(false);
    expect($user->isLocked())->toBe(true);
});

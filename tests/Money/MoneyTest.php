<?php

use Prophit\Core\Money\Money;

beforeEach(function () {
    $this->less = new Money(1, 'USD');
    $this->mid = new Money(2, 'USD');
    $this->greater = new Money(3, 'USD');
});

it('gets the amount', function () {
    expect($this->mid->getAmount())->toBe(2);
});

it('gets the currency', function () {
    expect($this->mid->getCurrency())->toBe('USD');
});

it('is equal to', function () {
    expect($this->mid->isEqualTo($this->less))->toBe(false);
    expect($this->mid->isEqualTo($this->mid))->toBe(true);
    expect($this->mid->isEqualTo($this->greater))->toBe(false);
});

it('is greater than', function () {
    expect($this->mid->isGreaterThan($this->less))->toBe(true);
    expect($this->mid->isGreaterThan($this->mid))->toBe(false);
    expect($this->mid->isGreaterThan($this->greater))->toBe(false);
});

it('is greater than or equal to', function () {
    expect($this->mid->isGreaterThanOrEqualTo($this->less))->toBe(true);
    expect($this->mid->isGreaterThanOrEqualTo($this->mid))->toBe(true);
    expect($this->mid->isGreaterThanOrEqualTo($this->greater))->toBe(false);
});

it('is less than or equal to', function () {
    expect($this->mid->isLessThanOrEqualTo($this->less))->toBe(false);
    expect($this->mid->isLessThanOrEqualTo($this->mid))->toBe(true);
    expect($this->mid->isLessThanOrEqualTo($this->greater))->toBe(true);
});

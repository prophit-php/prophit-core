<?php

use Brick\Money\Money;
use Prophit\Core\Money\MoneyRange;

it('swaps given values if needed', function () {
    $min = Money::of(1, 'USD');
    $max = Money::of(2, 'USD');

    $ordered = new MoneyRange($min, $max);
    expect($ordered->getMinimumInclusive())->toBe($min);
    expect($ordered->getMaximumInclusive())->toBe($max);

    $swapped = new MoneyRange($max, $min);
    expect($swapped->getMinimumInclusive())->toBe($min);
    expect($swapped->getMaximumInclusive())->toBe($max);
});

it('contains a given value', function () {
    $min = Money::of(1, 'USD');
    $med = Money::of(2, 'USD');
    $max = Money::of(3, 'USD');

    expect((new MoneyRange($min, $max))->contains($med))->toBeTrue();
    expect((new MoneyRange($med, $max))->contains($min))->toBeFalse();
    expect((new MoneyRange($min, $med))->contains($max))->toBeFalse();
});

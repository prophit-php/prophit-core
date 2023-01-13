<?php

use Prophit\Core\Money\{
    Money,
    MoneyRange,
};

it('swaps given values if needed', function () {
    $min = new Money(1, 'USD');
    $max = new Money(2, 'USD');

    $ordered = new MoneyRange($min, $max);
    expect($ordered->getMinimumInclusive())->toBe($min);
    expect($ordered->getMaximumInclusive())->toBe($max);

    $swapped = new MoneyRange($max, $min);
    expect($swapped->getMinimumInclusive())->toBe($min);
    expect($swapped->getMaximumInclusive())->toBe($max);
});

it('contains a given value', function () {
    $min = new Money(1, 'USD');
    $med = new Money(2, 'USD');
    $max = new Money(3, 'USD');

    expect((new MoneyRange($min, $max))->contains($med))->toBeTrue();
    expect((new MoneyRange($med, $max))->contains($min))->toBeFalse();
    expect((new MoneyRange($min, $med))->contains($max))->toBeFalse();
});

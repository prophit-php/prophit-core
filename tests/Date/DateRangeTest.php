<?php

use Prophit\Core\Date\DateRange;

it('swaps given values if needed', function () {
    $yesterday = new DateTime('-1 day');
    $today = new DateTime;

    $ordered = new DateRange($yesterday, $today);
    expect($ordered->getStartDate())->toEqual($yesterday);
    expect($ordered->getEndDate())->toEqual($today);

    $swapped = new DateRange($today, $yesterday);
    expect($swapped->getStartDate())->toEqual($yesterday);
    expect($swapped->getEndDate())->toEqual($today);
});

it('contains a given date', function () {
    $yesterday = new DateTime('-1 day');
    $today = new DateTime;
    $tomorrow = new DateTime('+1 day');

    expect((new DateRange($yesterday, $tomorrow))->contains($today))->toBeTrue();
    expect((new DateRange($today, $tomorrow))->contains($yesterday))->toBeFalse();
    expect((new DateRange($yesterday, $today))->contains($tomorrow))->toBeFalse();
});

<?php

namespace Prophit\Core\Date;

use DateInterval;
use DatePeriod;
use DateTimeInterface;

class DateRange extends DatePeriod
{
    public function __construct(
        DateTimeInterface $start,
        DateTimeInterface $end,
    ) {
        if ($start->getTimestamp() > $end->getTimestamp()) {
            [$start, $end] = [$end, $start];
        }

        parent::__construct(
            $start,
            new DateInterval('P1D'),
            $end,
        );
    }

    public function contains(DateTimeInterface $date): bool
    {
        $timestamp = $date->getTimestamp();
        $isAfterStart = $this->getStartDate()->getTimestamp() <= $timestamp;
        $endDate = $this->getEndDate();
        $isBeforeEnd = $endDate === null || $endDate->getTimestamp() >= $timestamp;
        return $isAfterStart && $isBeforeEnd;
    }
}

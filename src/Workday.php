<?php

declare(strict_types=1);

namespace SpeedyLom\WhenDoYouFinish;

use DateTime;
use DateTimeZone;

final class Workday
{
    private int $dayLengthInSeconds;
    private int $timeWorkedInSeconds;

    public function __construct(int $dayLengthInSeconds, int $timeWorkedInSeconds)
    {
        $this->dayLengthInSeconds = max(1, $dayLengthInSeconds);
        $this->timeWorkedInSeconds = max(0, $timeWorkedInSeconds);
    }

    public function getPercentageWorked(): int
    {
        return intval(min(100, round($this->timeWorkedInSeconds * 100
                                     / $this->dayLengthInSeconds, 2)));
    }

    public function getTimeWorked(): string
    {
        try {
            $date = new DateTime(
                '@' . $this->timeWorkedInSeconds,
                new DateTimeZone('UTC')
            );
        } catch (\Exception $e) {
            return date(
                $this->getTimeWorkedFormat(),
                $this->timeWorkedInSeconds
            );
        }

        return $date->format($this->getTimeWorkedFormat());
    }

    public function getCurrentFinishingTime(?int $timestamp = null): string
    {
        return date('g.ia', $this->currentFinishingTime($timestamp));
    }

    private function secondsLeftToWork(): int
    {
        return intval(max(0, $this->dayLengthInSeconds
                           - $this->timeWorkedInSeconds));
    }

    private function currentFinishingTime(?int $timestamp = null): int
    {
        return strtotime(
            '+' . $this->secondsLeftToWork() . ' seconds',
            $timestamp
        );
    }

    private function getTimeWorkedFormat(): string
    {
        if ($this->timeWorkedInSeconds >= 3600) {
            return 'g \h\o\u\r\s \a\n\d i \m\i\n\u\t\e\s';
        }

        return 'i \m\i\n\u\t\e\s';
    }
}

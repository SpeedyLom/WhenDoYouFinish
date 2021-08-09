<?php

declare(strict_types=1);

namespace SpeedyLom\WhenDoYouFinish;

final class Workday
{
    private int $dayLengthInSeconds;
    private int $timeWorkedInSeconds;
    private int $timeWorkedInMinutes;

    public function __construct(
        int $dayLengthInSeconds,
        int $timeWorkedInSeconds
    ) {
        $this->dayLengthInSeconds = max(1, $dayLengthInSeconds);
        $this->timeWorkedInSeconds = max(0, $timeWorkedInSeconds);
        $this->timeWorkedInMinutes = intval($this->timeWorkedInSeconds / 60);
    }

    public function getPercentageWorked(): int
    {
        return intval(
            min(
                100,
                round(
                    $this->timeWorkedInSeconds * 100
                    / $this->dayLengthInSeconds
                )
            )
        );
    }

    public function getTimeWorked(): string
    {
        if ($this->timeWorkedInMinutes > 60) {
            return $this->formatTimeWorkedIntoHoursAndMinutes();
        }

        return $this->formatTimeWorkedIntoMinutes();
    }

    private function formatTimeWorkedIntoHoursAndMinutes(): string
    {
        $hours = floor($this->timeWorkedInMinutes / 60);
        $minutes = $this->timeWorkedInMinutes % 60;

        if ($hours > 1) {
            return sprintf('%2$d hours and %1$d minutes', $minutes, $hours);
        }

        return sprintf('%2$d hour and %1$d minutes', $minutes, $hours);
    }

    private function formatTimeWorkedIntoMinutes(): string
    {
        return sprintf('%1$d minutes', $this->timeWorkedInMinutes);
    }

    public function getCurrentFinishingTime(
        ?int $timestamp = null
    ): string {
        return date('g.ia', $this->currentFinishingTime($timestamp));
    }

    private function currentFinishingTime(
        ?int $timestamp = null
    ): int {
        return strtotime(
            $this->secondsLeftToWork() . ' seconds',
            $timestamp
        );
    }

    private function secondsLeftToWork(): int
    {
        return intval(
            max(
                0,
                $this->dayLengthInSeconds
                - $this->timeWorkedInSeconds
            )
        );
    }
}

<?php

declare(strict_types=1);

namespace SpeedyLom\WhenDoYouFinish\Toggl;

use SpeedyLom\WhenDoYouFinish\HttpRequest\BasicAuthentication;

final class TrackApi
{
    private const BASE_URL = 'https://api.track.toggl.com/api/v8/';

    private mixed $currentEntry = [];

    public function __construct(
        private BasicAuthentication $curl,
        private string $apiToken
    ) {
    }

    public static function getEndPoint(): string
    {
        return self::BASE_URL . 'time_entries/current';
    }

    public function requestCurrentEntry(): void
    {
        $this->curl->addBasicHttpAuthentication($this->apiToken, 'api_token');
        $result = $this->curl->makeRequest();
        $this->curl->close();

        $this->currentEntry = is_string($result) ? json_decode($result, true) : [];
    }

    public function getCurrentEntryStartTimestamp(): int
    {
        if (! isset($this->currentEntry['data']['duration'])) {
            return -0;
        }

        return $this->currentEntry['data']['duration'] * -1;
    }

    public function getElapsedSecondsForCurrentEntry(int $timestamp): int
    {
        $currentEntryStartTimestamp = $this->getCurrentEntryStartTimestamp();
        if ($currentEntryStartTimestamp <= 0) {
            return 0;
        }

        return $timestamp - $currentEntryStartTimestamp;
    }
}

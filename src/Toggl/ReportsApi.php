<?php

declare(strict_types=1);

namespace SpeedyLom\WhenDoYouFinish\Toggl;

use SpeedyLom\WhenDoYouFinish\HttpRequest\BasicAuthentication;

final class ReportsApi
{
    public const BASE_URL = 'https://toggl.com/reports/api/v2/';

    private mixed $weeklyFigures = [];

    public function __construct(
        private BasicAuthentication $basicAuthentication,
        private string $apiToken
    ) {
    }

    public function requestWeeklyFigures(): void
    {
        $this->basicAuthentication->addBasicHttpAuthentication($this->apiToken, 'api_token');
        $result = $this->basicAuthentication->makeRequest();
        $this->basicAuthentication->close();

        $this->weeklyFigures = is_string($result) ? json_decode($result, true) : [];
    }

    public function getTotalSecondsForToday(): int
    {
        if (! $this->weeklyFigures) {
            return 0;
        }

        $millisecondsTotalForToday = $this->weeklyFigures['week_totals'][6] ?? 0;

        return intval(round($millisecondsTotalForToday / 1000));
    }

    public static function buildWeeklyFiguresUrl(string $userAgent, int $workspaceId): string
    {
        $getArgs = [
            'user_agent' => $userAgent,
            'workspace_id' => $workspaceId,
        ];

        return self::BASE_URL . 'weekly?' . http_build_query($getArgs);
    }
}

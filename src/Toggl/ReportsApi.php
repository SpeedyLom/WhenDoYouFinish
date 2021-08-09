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

    public static function buildWeeklyFiguresUrl(
        string $userAgent,
        int $workspaceId
    ): string {
        $getArgs = [
            'user_agent' => $userAgent,
            'workspace_id' => $workspaceId,
        ];

        return self::BASE_URL . 'weekly?' . http_build_query($getArgs);
    }

    public function requestWeeklyFigures(): void
    {
        $this->basicAuthentication->addBasicHttpAuthentication(
            $this->apiToken,
            'api_token'
        );
        $result = $this->basicAuthentication->makeRequest() ?? '[]';
        $this->basicAuthentication->close();

        $this->weeklyFigures = json_decode($result, true);
    }

    public function getTotalSecondsForToday(): int
    {
        if (!isset($this->weeklyFigures['week_totals'][6])) {
            return 0;
        }

        return intval(round($this->weeklyFigures['week_totals'][6] / 1000));
    }
}

<?php

declare(strict_types=1);

use SpeedyLom\WhenDoYouFinish\HttpRequest\CurlBasicAuthentication;
use SpeedyLom\WhenDoYouFinish\Toggl;
use SpeedyLom\WhenDoYouFinish\Toggl\TrackApi;
use SpeedyLom\WhenDoYouFinish\Workday;

require_once __DIR__ . '/vendor/autoload.php';

$configuration = json_decode(
    file_get_contents('configuration.json'),
    true
);

if (isset($configuration['environment']) && $configuration['environment'] === 'dev') {
    $whoops = new Whoops\Run();
    $whoops->pushHandler(new Whoops\Handler\PrettyPageHandler());
    $whoops->register();
}

$togglReportsApi = new Toggl\ReportsApi(
    new CurlBasicAuthentication(
        Toggl\ReportsApi::buildWeeklyFiguresUrl(
            $configuration['user_agent'],
            $configuration['workspace_id']
        )
    ),
    $configuration['api_token']
);
$togglReportsApi->requestWeeklyFigures();

$secondsWorkedToday = $togglReportsApi->getTotalSecondsForToday();

$togglTrackApi = new TrackApi(
    new CurlBasicAuthentication(
        TrackApi::getEndPoint()
    ),
    $configuration['api_token']
);
$togglTrackApi->requestCurrentEntry();

$secondsWorkedToday += $togglTrackApi->getElapsedSecondsForCurrentEntry(time());

$mustache = new Mustache_Engine(
    ['loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/views'),]
);

if ($secondsWorkedToday > 0) {
    $workday = new Workday(
        $configuration['workday_length_in_minutes'] * 60,
        $secondsWorkedToday
    );

    echo $mustache->render(
        'current_workday',
        [
            'formattedDate' => date('l jS'),
            'formattedCurrentFinishingTime' => $workday->getCurrentFinishingTime(),
            'formattedTimeWorked' => $workday->getTimeWorked(),
            'percentageWorked' => $workday->getPercentageWorked(),
            'title' => 'When Do You Finish?',
        ]
    );
} else {
    echo $mustache->render(
        'nothing_recorded',
        [
            'title' => 'Nothing Recorded',
        ]
    );
}

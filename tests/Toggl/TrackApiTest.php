<?php

declare(strict_types=1);

namespace SpeedyLom\WhenDoYouFinish\Tests\Toggl;

use PHPUnit\Framework\TestCase;
use SpeedyLom\WhenDoYouFinish\HttpRequest\BasicAuthentication;
use SpeedyLom\WhenDoYouFinish\Toggl\TrackApi;

class TrackApiTest extends TestCase
{

    public function testRetrieveTimeEntriesCurrentNotRunning(): void
    {
        $curlStub = $this->createStub(BasicAuthentication::class);
        $curlStub->method('makeRequest')->
        willReturn('{"data": null}');

        $togglTrackApi = new TrackApi($curlStub, '123456ABCDEF');
        $togglTrackApi->requestCurrentEntry();

        $this->assertSame(
            0,
            $togglTrackApi->getCurrentEntryStartTimestamp()
        );
    }

    public function testRetrieveTimeEntriesCurrentRunning(): void
    {
        $curlStub = $this->createStub(BasicAuthentication::class);
        $curlStub->method('makeRequest')->
        willReturn($this->currentEntryExample());

        $togglTrackApi = new TrackApi($curlStub, '123456ABCDEF');
        $togglTrackApi->requestCurrentEntry();

        $this->assertSame(
            1391072884,
            $togglTrackApi->getCurrentEntryStartTimestamp()
        );
    }

    private function currentEntryExample(): string
    {
        return '{
  "data": {
    "id": 436694100,
    "wid": 777,
    "pid": 193791,
    "billable": false,
    "start": "2014-01-30T09:08:04+00:00",
    "duration": -1391072884,
    "description": "Runningtimeentry",
    "at": "2014-01-30T09:08:12+00:00"
  }
}';
    }

    public function testRetrieveTimeEntriesCurrentRunningSeconds(): void
    {
        $curlStub = $this->createStub(BasicAuthentication::class);
        $curlStub->method('makeRequest')->
        willReturn($this->currentEntryExample());

        $togglTrackApi = new TrackApi($curlStub, '123456ABCDEF');
        $togglTrackApi->requestCurrentEntry();

        $this->assertSame(
            142147616,
            $togglTrackApi->getElapsedSecondsForCurrentEntry(1533220500)
        );
    }

    public function testAuthAddedWhenRequestingCurrentEntry(): void
    {
        $curlObserver = $this->createMock(BasicAuthentication::class);

        $curlObserver->expects($this->once())
            ->method('addBasicHttpAuthentication')
            ->with(
                $this->equalTo('123456ABCDEFG'),
                $this->equalTo('api_token'),
            );

        $curlObserver->method('makeRequest')
            ->willReturn($this->currentEntryExample());

        $togglTrackApi = new TrackApi($curlObserver, '123456ABCDEFG');
        $togglTrackApi->requestCurrentEntry();
    }

    public function testBasicAuthClosedAfterRequestingCurrentEntry(): void
    {
        $curlObserver = $this->createMock(BasicAuthentication::class);
        $curlObserver->expects($this->once())->method('close');

        $curlObserver->method('makeRequest')
            ->willReturn($this->currentEntryExample());

        $togglTrackApi = new TrackApi($curlObserver, '123456ABCDEFG');
        $togglTrackApi->requestCurrentEntry();
    }

    public function testElapsedSecondsZeroWhenDurationZero(): void
    {
        $curlStub = $this->createStub(BasicAuthentication::class);
        $curlStub->method('makeRequest')->
        willReturn(
            $this->zeroDurationExample()
        );

        $togglTrackApi = new TrackApi($curlStub, '123456ABCDEF');
        $togglTrackApi->requestCurrentEntry();

        $this->assertSame(
            0,
            $togglTrackApi->getElapsedSecondsForCurrentEntry(1533220500)
        );
    }

    private function zeroDurationExample(): string
    {
        return '{
  "data": {
    "id": 436694100,
    "wid": 777,
    "pid": 193791,
    "billable": false,
    "start": "2014-01-30T09:08:04+00:00",
    "duration": -0,
    "description": "Runningtimeentry",
    "at": "2014-01-30T09:08:12+00:00"
  }
}';
    }

    public function testWeeklyFiguresUrl(): void
    {
        $url = TrackApi::getEndPoint();
        $this->assertSame(
            'https://api.track.toggl.com/api/v8/time_entries/current',
            $url
        );
    }
}

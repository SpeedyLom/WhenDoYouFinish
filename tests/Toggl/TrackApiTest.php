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
        $this->assertSame(0, $togglTrackApi->getCurrentEntryStartTimestamp());
    }

    public function testRetrieveTimeEntriesCurrentRunning(): void
    {
        $curlStub = $this->createStub(BasicAuthentication::class);
        $curlStub->method('makeRequest')->
        willReturn('{"data":{"id":436694100,"wid":777,"pid":193791,"billable":false,"start":"2014-01-30T09:08:04+00:00","duration":-1391072884,"description":"Runningtimeentry","at":"2014-01-30T09:08:12+00:00"}}');

        $togglTrackApi = new TrackApi($curlStub, '123456ABCDEF');
        $togglTrackApi->requestCurrentEntry();
        $this->assertSame(1391072884, $togglTrackApi->getCurrentEntryStartTimestamp());
    }

    public function testRetrieveTimeEntriesCurrentRunningSeconds(): void
    {
        $curlStub = $this->createStub(BasicAuthentication::class);
        $curlStub->method('makeRequest')->
        willReturn('{"data":{"id":436694100,"wid":777,"pid":193791,"billable":false,"start":"2014-01-30T09:08:04+00:00","duration":-1391072884,"description":"Runningtimeentry","at":"2014-01-30T09:08:12+00:00"}}');

        $togglTrackApi = new TrackApi($curlStub, '123456ABCDEF');
        $togglTrackApi->requestCurrentEntry();
        $this->assertSame(142147616, $togglTrackApi->getElapsedSecondsForCurrentEntry(1533220500));
    }

}

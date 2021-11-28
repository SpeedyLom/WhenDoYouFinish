<?php

declare(strict_types=1);

namespace SpeedyLom\WhenDoYouFinish\Tests\Toggl;

use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use SpeedyLom\WhenDoYouFinish\HttpRequest\BasicAuthentication;
use SpeedyLom\WhenDoYouFinish\Toggl\ReportsApi;

class ReportsApiTest extends TestCase
{

    public function testRetrieveWeeklyFigures(): void
    {
        $curlStub = $this->createBasicAuthStubWithExampleData();
        $reportsApi = $this->getReportApi($curlStub);
        $reportsApi->requestWeeklyFigures();

        $this->assertSame(
            27057,
            $reportsApi->getTotalSecondsForToday()
        );
    }

    private function createBasicAuthStubWithExampleData(): Stub|BasicAuthentication
    {
        $curlStub = $this->createStub(BasicAuthentication::class);
        $curlStub->method('makeRequest')->
        willReturn($this->exampleWeeklyResponseJson());

        return $curlStub;
    }

    private function exampleWeeklyResponseJson(): string
    {
        return '{"total_grand":135771000,"total_billable":null,"total_currencies":[{"currency":null,"amount":null}],"data":[{"title":{"client":null,"project":"Development","color":"0","hex_color":"#0b83d9"},"pid":41704093,"totals":[13750000,3311000,null,null,15184000,null,null,32245000],"details":[{"uid":2519446,"title":{"user":"SpeedyLom"},"totals":[13750000,3311000,null,null,15184000,null,null,32245000]}]},{"title":{"client":null,"project":"Discussion","color":"0","hex_color":"#c9806b"},"pid":40738645,"totals":[518000,1007000,null,null,1063000,778000,810000,4176000],"details":[{"uid":2519446,"title":{"user":"SpeedyLom"},"totals":[518000,1007000,null,null,1063000,778000,810000,4176000]}]},{"title":{"client":null,"project":"Fixes","color":"0","hex_color":"#d92b2b"},"pid":41704062,"totals":[4544000,1261000,null,null,null,null,null,5805000],"details":[{"uid":2519446,"title":{"user":"SpeedyLom"},"totals":[4544000,1261000,null,null,null,null,null,5805000]}]},{"title":{"client":null,"project":"ProjectManagement","color":"0","hex_color":"#990099"},"pid":70150843,"totals":[null,null,null,null,null,null,16735000,16735000],"details":[{"uid":2519446,"title":{"user":"SpeedyLom"},"totals":[null,null,null,null,null,null,16735000,16735000]}]},{"title":{"client":null,"project":"Scoping","color":"0","hex_color":"#9e5bd9"},"pid":40536082,"totals":[null,15969000,null,null,2837000,19075000,null,37881000],"details":[{"uid":2519446,"title":{"user":"SpeedyLom"},"totals":[null,15969000,null,null,2837000,19075000,null,37881000]}]},{"title":{"client":null,"project":"SiteMaintenance","color":"0","hex_color":"#566614"},"pid":40536056,"totals":[null,null,null,null,1685000,null,null,1685000],"details":[{"uid":2519446,"title":{"user":"SpeedyLom"},"totals":[null,null,null,null,1685000,null,null,1685000]}]},{"title":{"client":null,"project":"Tickets/SupportCall","color":"0","hex_color":"#e36a00"},"pid":40738208,"totals":[6147000,4755000,null,null,4047000,6038000,2622000,23609000],"details":[{"uid":2519446,"title":{"user":"SpeedyLom"},"totals":[6147000,4755000,null,null,4047000,6038000,2622000,23609000]}]},{"title":{"client":null,"project":"WorkloadManagement","color":"0","hex_color":"#525266"},"pid":34370465,"totals":[2163000,960000,null,null,2469000,1153000,6890000,13635000],"details":[{"uid":2519446,"title":{"user":"SpeedyLom"},"totals":[2163000,960000,null,null,2469000,1153000,6890000,13635000]}]}],"week_totals":[27122000,27263000,null,null,27285000,27044000,27057000,135771000]}';
    }

    private function getReportApi(
        BasicAuthentication $curlStub
    ): ReportsApi {
        return new ReportsApi($curlStub, '123456ABCDEFG');
    }

    public function testAuthAddedWhenRequestingFigures(): void
    {
        $curlObserver = $this->createMock(BasicAuthentication::class);

        $curlObserver->expects($this->once())
            ->method('addBasicHttpAuthentication')
            ->with(
                $this->equalTo('123456ABCDEFG'),
                $this->equalTo('api_token'),
            );

        $curlObserver->method('makeRequest')
            ->willReturn($this->exampleWeeklyResponseJson());

        $reportsApi = new ReportsApi($curlObserver, '123456ABCDEFG');
        $reportsApi->requestWeeklyFigures();
    }

    public function testBasicAuthClosedAfterRequestingFigures(): void
    {
        $curlObserver = $this->createMock(BasicAuthentication::class);

        $curlObserver->expects($this->once())->method('close');

        $curlObserver->method('makeRequest')
            ->willReturn($this->exampleWeeklyResponseJson());

        $reportsApi = new ReportsApi($curlObserver, '123456ABCDEFG');
        $reportsApi->requestWeeklyFigures();
    }

    public function testNoWeeklyFiguresEqualsZeroSeconds(): void
    {
        $curlStub = $this->createBasicAuthStubWithNoData();
        $reportsApi = $this->getReportApi($curlStub);
        $reportsApi->requestWeeklyFigures();

        $this->assertSame(
            0,
            $reportsApi->getTotalSecondsForToday()
        );
    }

    private function createBasicAuthStubWithNoData(): Stub|BasicAuthentication
    {
        $curlStub = $this->createStub(BasicAuthentication::class);
        $curlStub->method('makeRequest')->
        willReturn('null');

        return $curlStub;
    }

    public function testTotalSecondsNotUsingFloor(): void
    {
        $curlStub = $this->createStub(BasicAuthentication::class);
        $curlStub->method('makeRequest')->
        willReturn('{"week_totals":[null,null,null,null,null,null,999,null]}');
        $reportsApi = $this->getReportApi($curlStub);
        $reportsApi->requestWeeklyFigures();

        $this->assertSame(
            1,
            $reportsApi->getTotalSecondsForToday()
        );
    }

    public function testTotalSecondsNotUsingCeil(): void
    {
        $curlStub = $this->createStub(BasicAuthentication::class);
        $curlStub->method('makeRequest')->
        willReturn('{"week_totals":[null,null,null,null,null,null,111,null]}');
        $reportsApi = $this->getReportApi($curlStub);
        $reportsApi->requestWeeklyFigures();

        $this->assertSame(
            0,
            $reportsApi->getTotalSecondsForToday()
        );
    }

    public function testWeeklyFiguresUrl(): void
    {
        $url = ReportsApi::buildWeeklyFiguresUrl('user_agent', 1234567);
        $this->assertSame(
            'https://api.track.toggl.com/reports/api/v2/weekly?user_agent=user_agent&workspace_id=1234567',
            $url
        );
    }
}

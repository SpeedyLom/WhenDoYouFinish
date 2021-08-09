<?php

declare(strict_types=1);

namespace SpeedyLom\WhenDoYouFinish\Tests;

use PHPUnit\Framework\TestCase;
use SpeedyLom\WhenDoYouFinish\Workday;

class WorkdayTest extends TestCase
{
    public function testMinimumDayLengthInSecondsEqualsOne()
    {
        $workday = new Workday(0, 1);
        $this->assertSame(
            100,
            $workday->getPercentageWorked()
        );
    }

    public function testCorrectRoundingIsUsed()
    {
        $workday = new Workday(1000, 45);
        $this->assertSame(
            5,
            $workday->getPercentageWorked()
        );
    }

    public function testFloorRoundingIsNotUsed(): void
    {
        $workday = new Workday(1000, 49);
        $this->assertSame(
            5,
            $workday->getPercentageWorked()
        );
    }

    public function testCeilRoundingIsNotUsed(): void
    {
        $workday = new Workday(1000, 41);
        $this->assertSame(
            4,
            $workday->getPercentageWorked()
        );
    }

    public function testOneHundredPercentWorkday(): void
    {
        $workday = new Workday(3600, 3600);

        $this->assertSame(
            100,
            $workday->getPercentageWorked()
        );
    }

    public function testOverworkedEqualsOneHundredPercentWorkday(): void
    {
        $workday = new Workday(60, 120);

        $this->assertSame(
            100,
            $workday->getPercentageWorked()
        );
    }

    public function testNotStartedEqualsZeroPercentWorkday(): void
    {
        $workday = new Workday(60, 0);

        $this->assertSame(
            0,
            $workday->getPercentageWorked()
        );
    }

    public function testThirtyOnePercentRecurringWorkdayEqualsWholeNumber()
    {
        $workday = new Workday(60, 19);

        $this->assertSame(
            32,
            $workday->getPercentageWorked()
        );
    }

    public function testNegativeTimeWorkedEqualsZeroPercentWorkday(): void
    {
        $workday = new Workday(60, -60);

        $this->assertSame(
            0,
            $workday->getPercentageWorked()
        );
    }

    public function testNegativeDayLengthWhenWorkedIsOneHundredPercentWorkday(): void
    {
        $workday = new Workday(-60, 60);

        $this->assertSame(
            100,
            $workday->getPercentageWorked()
        );
    }

    public function testOneHundredPercentWorkdayEqualsCurrentFinishingTime()
    {
        $workday = new Workday(60, 60);

        $this->assertSame(
            date('g.ia', 0),
            $workday->getCurrentFinishingTime(0)
        );
    }

    public function testUnworkedSixtySecondWorkdayFinishesInSixtySeconds()
    {
        $workday = new Workday(60, 0);

        $this->assertSame(
            date('g.ia', 60),
            $workday->getCurrentFinishingTime(0)
        );
    }

    public function testCurrentFinishingTimeIsNotInThePast()
    {
        $workday = new Workday(60, 90);

        $this->assertSame(
            date('g.ia', 0),
            $workday->getCurrentFinishingTime(0)
        );
    }

    public function testSecondsWorkedIsNotOneWhenZeroAreWorked(): void
    {
        $workday = new Workday(1, 0);

        $this->assertSame(
            date('g.ia', 0),
            $workday->getCurrentFinishingTime(-1)
        );
    }

    public function testOneMinuteTimeWorked(): void
    {
        $workday = new Workday(120, 60);

        $this->assertSame(
            '1 minutes',
            $workday->getTimeWorked()
        );
    }

    public function testOneHourAndOneMinuteTimeWorkedDisplayedCorrectly(): void
    {
        $workday = new Workday(27000, 25200);

        $this->assertSame(
            '7 hours and 0 minutes',
            $workday->getTimeWorked()
        );
    }

    public function testLessThanOneMinuteTimeWorked()
    {
        $workday = new Workday(120, 0);

        $this->assertSame(
            '0 minutes',
            $workday->getTimeWorked()
        );
    }

    public function testOneHourOneMinuteTimeWorkedFormattedCorrectly(): void
    {
        $workday = new Workday(27000, 3660);

        $this->assertSame(
            '1 hour and 1 minutes',
            $workday->getTimeWorked()
        );
    }

    public function testOneHourTimeWorkedFormattedCorrectly(): void
    {
        $workday = new Workday(27000, 3600);

        $this->assertSame(
            '60 minutes',
            $workday->getTimeWorked()
        );
    }
}

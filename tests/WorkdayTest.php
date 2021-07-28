<?php

declare(strict_types=1);

namespace SpeedyLom\WhenDoYouFinish\Tests;

use SpeedyLom\WhenDoYouFinish\Workday;
use PHPUnit\Framework\TestCase;

class WorkdayTest extends TestCase
{
    public function testOneHundredPercentWorkday(): void
    {
        $workday = new Workday(3600, 3600);

        $this->assertSame(100, $workday->getPercentageWorked());
    }

    public function testOverworkedEqualsOneHundredPercentWorkday(): void
    {
        $workday = new Workday(60, 120);

        $this->assertSame(100, $workday->getPercentageWorked());
    }

    public function testNotStartedEqualsZeroPercentWorkday(): void
    {
        $workday = new Workday(60, 0);

        $this->assertSame(0, $workday->getPercentageWorked());
    }

    public function testThirtyOnePercentRecurringWorkdayEqualsWholeNumber()
    {
        $workday = new Workday(60, 19);

        $this->assertSame(31, $workday->getPercentageWorked());
    }

    public function testNegativeTimeWorkedEqualsZeroPercentWorkday(): void
    {
        $workday = new Workday(60, -60);

        $this->assertSame(0, $workday->getPercentageWorked());
    }

    public function testNegativeDayLengthWhenWorkedIsOneHundredPercentWorkday(): void
    {
        $workday = new Workday(-60, 60);

        $this->assertSame(100, $workday->getPercentageWorked());
    }

    public function testOneHundredPercentWorkdayEqualsCurrentFinishingTime()
    {
        $workday = new Workday(60, 60);

        $this->assertSame(date('g.ia', 0), $workday->getCurrentFinishingTime(0));
    }

    public function testUnworkedSixtySecondWorkdayFinishesInSixtySeconds()
    {
        $workday = new Workday(60, 0);

        $this->assertSame(date('g.ia', 60), $workday->getCurrentFinishingTime(0));
    }

}

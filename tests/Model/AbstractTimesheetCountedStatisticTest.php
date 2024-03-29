<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Model;

use App\Model\TimesheetCountedStatistic;
use PHPUnit\Framework\TestCase;

abstract class AbstractTimesheetCountedStatisticTest extends TestCase
{
    protected function assertDefaultValues(TimesheetCountedStatistic $sut)
    {
        self::assertSame(0.0, $sut->getRecordRate());
        self::assertSame(0.0, $sut->getRate());
        self::assertSame(0, $sut->getRecordDuration());
        self::assertSame(0, $sut->getDuration());
        self::assertSame(0, $sut->getCounter());
        self::assertSame(0, $sut->getCounterBillable());
        self::assertSame(0.0, $sut->getRecordInternalRate());
        self::assertSame(0, $sut->getValue());
        self::assertSame(0, $sut->getDurationBillable());
        self::assertSame(0.0, $sut->getRateBillable());
        self::assertSame(0, $sut->getRecordAmountBillable());
        self::assertSame(0.0, $sut->getInternalRateBillable());
        self::assertSame(0, $sut->getDurationBillableExported());
        self::assertSame(0, $sut->getDurationExported());
        self::assertSame(0.0, $sut->getRateExported());
        self::assertSame(0.0, $sut->getInternalRateExported());

        $json = $sut->jsonSerialize();

        $expected = [
            'duration' => 0,
            'duration_billable' => 0,
            'duration_exported' => 0,
            'duration_billable_exported' => 0,
            'rate' => 0.0,
            'rate_billable' => 0.0,
            'rate_exported' => 0.0,
            'rate_internal' => 0.0,
            'amount' => 0,
            'amount_billable' => 0,
            'amount_exported' => 0,
        ];

        foreach ($expected as $key => $value) {
            self::assertArrayHasKey($key, $json);
            self::assertSame($value, $json[$key]);
        }
    }

    protected function assertSetter(TimesheetCountedStatistic $sut)
    {
        self::assertInstanceOf(TimesheetCountedStatistic::class, $sut->setRecordRate(23.97));
        self::assertInstanceOf(TimesheetCountedStatistic::class, $sut->setRecordDuration(21));
        self::assertInstanceOf(TimesheetCountedStatistic::class, $sut->setRecordAmount(5));
        $sut->setRecordAmountBillable(15);
        self::assertInstanceOf(TimesheetCountedStatistic::class, $sut->setRecordInternalRate(99.09));

        $sut->setDurationBillableExported(199);
        $sut->setDurationExported(299);
        $sut->setRateExported(456.48);
        $sut->setInternalRateExported(27.15);

        self::assertSame(23.97, $sut->getRecordRate());
        self::assertSame(21, $sut->getRecordDuration());
        self::assertSame(5, $sut->getRecordAmount());
        self::assertSame(15, $sut->getRecordAmountBillable());
        self::assertSame(99.09, $sut->getRecordInternalRate());

        self::assertSame(199, $sut->getDurationBillableExported());
        self::assertSame(299, $sut->getDurationExported());
        self::assertSame(456.48, $sut->getRateExported());
        self::assertSame(27.15, $sut->getInternalRateExported());

        self::assertSame(21, $sut->getValue());

        $sut->setCounter(524);
        $sut->setCounterBillable(198);
        $sut->setInternalRate(567.09);
        $sut->setRate(323.97);
        $sut->setDuration(22);
        $sut->setRateBillable(123.456);
        $sut->setDurationBillable(1234);
        $sut->setInternalRateBillable(987.12);

        self::assertSame(524, $sut->getCounter());
        self::assertSame(198, $sut->getCounterBillable());
        self::assertSame(567.09, $sut->getInternalRate());
        self::assertSame(22, $sut->getDuration());
        self::assertSame(323.97, $sut->getRate());
        self::assertSame(123.456, $sut->getRateBillable());
        self::assertSame(1234, $sut->getDurationBillable());
        self::assertSame(987.12, $sut->getInternalRateBillable());
    }

    protected function assertJsonSerialize(TimesheetCountedStatistic $sut)
    {
        self::assertInstanceOf(\JsonSerializable::class, $sut);
        $sut->setRecordRate(23.97);
        $sut->setRecordDuration(21);
        $sut->setRecordAmount(5);
        $sut->setRecordInternalRate(99.09);
        $sut->setRateBillable(123.456);
        $sut->setDurationBillable(1234);
        $sut->setRecordAmountBillable(4321);

        $sut->setDurationBillableExported(199);
        $sut->setRateBillableExported(654.23);
        $sut->setDurationExported(299);
        $sut->setRateExported(456.48);
        $sut->setInternalRateExported(27.15);

        $json = $sut->jsonSerialize();
        foreach (['duration', 'duration_billable', 'duration_exported', 'rate', 'rate_billable', 'rate_billable_exported', 'rate_exported', 'rate_internal', 'amount', 'amount_billable', 'amount_exported'] as $key) {
            self::assertArrayHasKey($key, $json);
        }

        self::assertSame(21, $json['duration']);
        self::assertSame(1234, $json['duration_billable']);
        self::assertSame(199, $json['duration_billable_exported']);
        self::assertSame(299, $json['duration_exported']);
        self::assertSame(23.97, $json['rate']);
        self::assertSame(123.456, $json['rate_billable']);
        self::assertSame(654.23, $json['rate_billable_exported']);
        self::assertSame(99.09, $json['rate_internal']);
        self::assertSame(5, $json['amount']);
        self::assertSame(4321, $json['amount_billable']);
    }
}

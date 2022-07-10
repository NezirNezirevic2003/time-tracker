<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Widget\Type;

use App\Repository\TimesheetRepository;
use App\Widget\Type\AbstractAmountPeriod;
use App\Widget\Type\AbstractWidgetType;
use App\Widget\Type\AmountMonth;
use App\Widget\Type\SimpleStatisticChart;
use App\Widget\WidgetInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \App\Widget\Type\AmountMonth
 * @covers \App\Widget\Type\AbstractAmountPeriod
 */
class AmountMonthTest extends AbstractWidgetTypeTest
{
    protected function assertDefaultData(AbstractWidgetType $sut)
    {
        self::assertEquals(0.0, $sut->getData());
    }

    /**
     * @return AbstractAmountPeriod
     */
    public function createSut(): AbstractWidgetType
    {
        $repository = $this->createMock(TimesheetRepository::class);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        return new AmountMonth($repository, $dispatcher);
    }

    public function getDefaultOptions(): array
    {
        return [
            'dataType' => 'money',
            'icon' => 'money',
            'color' => WidgetInterface::COLOR_MONTH,
        ];
    }

    public function testData()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot set data on instances of SimpleStatisticChart');

        $sut = $this->createSut();
        self::assertInstanceOf(SimpleStatisticChart::class, $sut);
        $sut->setData(10);
    }

    public function testSettings()
    {
        $sut = $this->createSut();

        self::assertEquals('widget/widget-counter.html.twig', $sut->getTemplateName());
        self::assertEquals('amountMonth', $sut->getId());
    }
}

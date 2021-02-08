<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Framework;

use OxidEsales\GraphQL\Base\Framework\Timer;
use PHPUnit\Framework\TestCase;
use function usleep;

class TimerTest extends TestCase
{
    public function testTimer(): void
    {
        $timer = new Timer();
        $timer->start();
        usleep(1);
        $timer->stop();
        $this->assertGreaterThan(
            0.0,
            $timer->getDuration()
        );
        $this->assertLessThan(
            1.0,
            $timer->getDuration()
        );
    }

    public function testTimerAt(): void
    {
        $timer = new Timer();
        $timer->startAt(microtime(true));
        usleep(1);
        $timer->stop();
        $this->assertGreaterThan(
            0.0,
            $timer->getDuration()
        );
    }
}

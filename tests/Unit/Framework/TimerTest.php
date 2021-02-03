<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Framework;

use OxidEsales\GraphQL\Base\Framework\Timer;
use PHPUnit\Framework\TestCase;

class TimerTest extends TestCase
{
    public function testTimer(): void
    {
        $timer = new Timer();

        $this->assertGreaterThan(0.0, $timer->start()->stop()->getDurationMs());
        $this->assertEquals($timer->getDuration() * 1000, $timer->getDurationMs());
    }
}

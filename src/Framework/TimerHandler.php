<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

class TimerHandler
{
    /** @var array<Timer> */
    private $timers;

    public function create(string $name): Timer
    {
        $this->timers[$name] = new Timer();

        return $this->timers[$name];
    }

    /** @return array<Timer> */
    public function getTimers(): array
    {
        return $this->timers;
    }
}

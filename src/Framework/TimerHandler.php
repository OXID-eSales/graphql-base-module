<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

/**
 * @internal This class is not covered by the backward compatibility promise
 */
class TimerHandler
{
    /** @var Timer[] */
    private array $timers;

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

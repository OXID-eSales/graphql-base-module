<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

use function header;
use function json_encode;

class ResponseWriter
{
    public function __construct(private readonly TimerHandler $timerHandler)
    {
    }

    /**
     * Return a JSON Object with the graphql results
     *
     * @codeCoverageIgnore
     * @SuppressWarnings(PHPMD.ExitExpression)
     *
     * @param mixed[] $result
     */
    public function renderJsonResponse(array $result): void
    {
        $this->cleanHeaders();

        header('Content-Type: application/json', true, 200);
        header($this->generateServerTimingHeader(), true, 200);

        exit(json_encode($result));
    }

    /**
     * Remove all headers the shop core might have set
     */
    private function cleanHeaders(): bool
    {
        //in case headers have already been sent nothing can be cleaned
        if (!headers_sent()) {
            header_remove();

            return true;
        }

        return false;
    }

    private function generateServerTimingHeader(): string
    {
        $timings = [];

        foreach ($this->timerHandler->getTimers() as $name => $timer) {
            $timings[] = sprintf('%s;dur=%.3f', $name, $timer->getDuration() * 1000);
        }

        return 'Server-Timing: ' . implode(',', $timings);
    }
}

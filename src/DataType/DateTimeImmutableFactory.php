<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use DateTimeImmutable;
use DateTimeZone;

final class DateTimeImmutableFactory
{
    public static function fromString(string $time = 'now', ?DateTimeZone $timezone = null): ?DateTimeImmutable
    {
        if (strlen($time) == 0
            || strpos($time, '0000-00-00') !== false
            || $time === '-'
        ) {
            return null;
        }

        return new DateTimeImmutable($time, $timezone);
    }

    public static function fromTimeStamp(int $timeStamp): ?DateTimeImmutable
    {
        if ($timeStamp <= 0) {
            return null;
        }

        return (new DateTimeImmutable())->setTimestamp($timeStamp);
    }
}

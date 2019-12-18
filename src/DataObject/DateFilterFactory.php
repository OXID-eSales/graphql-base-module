<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataObject;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use TheCodingMachine\GraphQLite\Annotations\Factory;

use function count;

class DateFilterFactory
{
    /**
     * moar moar moar
     *
     * @Factory(name="DateFilterInput")
     * @param string[]|null $between
     */
    public static function createDateFilterInput(
        ?string $equals = null,
        ?array $between = null
    ): DateFilter {
        if (
            $between !== null && (
                count($between) !== 2 ||
                !is_string($between[0]) ||
                !is_string($between[1])
            )
        ) {
            throw new \OutOfBoundsException();
        }
        $zone = new DateTimeZone('UTC');
        if ($equals !== null) {
            $equals = new DateTimeImmutable($equals, $zone);
        }
        if ($between) {
            $between = array_map(
                function ($date) use ($zone) {
                    return new DateTimeImmutable($date, $zone);
                },
                $between
            );
        }
        /** @var array{0: DateTimeInterface, 1: DateTimeInterface} $between */
        return new DateFilter(
            $equals,
            $between
        );
    }
}

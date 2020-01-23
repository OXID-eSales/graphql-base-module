<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use TheCodingMachine\GraphQLite\Annotations\Factory;

use function count;

class IntegerFilterFactory
{
    /**
     * @Factory()
     * @param int[]|null $between
     */
    public static function createIntegerFilter(
        ?int $equals = null,
        ?int $lowerThen = null,
        ?int $greaterThen = null,
        ?array $between = null
    ): IntegerFilter {
        if (
            $between !== null && (
            count($between) !== 2 ||
            !is_int($between[0]) ||
            !is_int($between[1])
            )
        ) {
            throw new \OutOfBoundsException();
        }
        /** @var array{0: int, 1: int} $between */
        return new IntegerFilter(
            $equals,
            $lowerThen,
            $greaterThen,
            $between
        );
    }
}

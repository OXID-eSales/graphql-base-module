<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataObject;

use TheCodingMachine\GraphQLite\Annotations\Factory;

use function count;

class FloatFilterInputFactory
{
    /**
     * @Factory()
     * @param float[]|null $between
     */
    public static function createFloatFilterInput(
        ?float $equals = null,
        ?float $lowerThen = null,
        ?float $greaterThen = null,
        ?array $between = null
    ): FloatFilterInput {
        if (
            $between !== null && (
            count($between) !== 2 ||
            !is_float($between[0]) ||
            !is_float($between[1])
            )
        ) {
            throw new \OutOfBoundsException();
        }
        /** @var array{0: float, 1: float} $between */
        return new FloatFilterInput(
            $equals,
            $lowerThen,
            $greaterThen,
            $between
        );
    }
}

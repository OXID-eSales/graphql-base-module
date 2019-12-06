<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataObject;

use TheCodingMachine\GraphQLite\Annotations\Factory;

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
            is_array($between) && (
            !isset($between[0]) ||
            !isset($between[1]) ||
            !is_float($between[0]) ||
            !is_float($between[1])
            )
        ) {
            throw new \OutOfBoundsException();
        }
        return new FloatFilterInput(
            $equals,
            $lowerThen,
            $greaterThen,
            $between
        );
    }
}

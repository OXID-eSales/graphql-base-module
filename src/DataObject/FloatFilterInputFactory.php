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
     * @param array{0: float, 1: float}|null $between
     */
    public static function createFloatFilterInput(
        ?float $equals = null,
        ?float $lowerThen = null,
        ?float $greaterThen = null,
        ?array $between = null
    ): FloatFilterInput {
        return new FloatFilterInput(
            $equals,
            $lowerThen,
            $greaterThen,
            $between
        );
    }
}

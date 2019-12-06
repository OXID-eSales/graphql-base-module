<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataObject;

use TheCodingMachine\GraphQLite\Annotations\Factory;

class IntegerFilterInputFactory
{
    /**
     * @Factory()
     * @param array{0: int, 1: int}|null $between
     */
    public static function createIntegerFilterInput(
        ?int $equals = null,
        ?int $lowerThen = null,
        ?int $greaterThen = null,
        ?array $between = null
    ): IntegerFilterInput {
        return new IntegerFilterInput(
            $equals,
            $lowerThen,
            $greaterThen,
            $between
        );
    }
}

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
     * @param int[]|null $between
     */
    public static function createIntegerFilterInput(
        ?int $equals = null,
        ?int $lowerThen = null,
        ?int $greaterThen = null,
        ?array $between = null
    ): IntegerFilterInput {
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
        return new IntegerFilterInput(
            $equals,
            $lowerThen,
            $greaterThen,
            $between
        );
    }
}

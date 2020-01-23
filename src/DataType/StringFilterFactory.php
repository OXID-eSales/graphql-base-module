<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use TheCodingMachine\GraphQLite\Annotations\Factory;

class StringFilterFactory
{
    /**
     * @Factory()
     */
    public static function createStringFilter(
        ?string $equals = null,
        ?string $contains = null,
        ?string $beginsWith = null
    ): StringFilter {
        return new StringFilter(
            $equals,
            $contains,
            $beginsWith
        );
    }
}

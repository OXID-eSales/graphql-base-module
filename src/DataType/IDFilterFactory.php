<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use TheCodingMachine\GraphQLite\Annotations\Factory;
use TheCodingMachine\GraphQLite\Types\ID;

class IDFilterFactory
{
    /**
     * @Factory()
     */
    public static function createIDFilter(
        ID $equals
    ): IDFilter {
        return new IDFilter(
            $equals
        );
    }
}

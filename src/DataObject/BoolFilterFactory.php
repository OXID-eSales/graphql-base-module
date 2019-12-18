<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataObject;

use TheCodingMachine\GraphQLite\Annotations\Factory;

class BoolFilterFactory
{
    /**
     * @Factory()
     */
    public static function createBoolFilter(
        bool $equals
    ): BoolFilter {
        return new BoolFilter(
            $equals
        );
    }
}

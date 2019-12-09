<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataObject;

use TheCodingMachine\GraphQLite\Annotations\Factory;

class BoolFilterInputFactory
{
    /**
     * @Factory()
     */
    public static function createBoolFilterInput(
        bool $equals
    ): BoolFilterInput {
        return new BoolFilterInput(
            $equals
        );
    }
}

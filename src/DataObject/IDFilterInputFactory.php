<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataObject;

use TheCodingMachine\GraphQLite\Annotations\Factory;
use TheCodingMachine\GraphQLite\Types\ID;

class IDFilterInputFactory
{
    /**
     * @Factory()
     */
    public static function createIDFilterInput(
        ID $equals
    ): IDFilterInput {
        return new IDFilterInput(
            $equals
        );
    }
}

<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataObject;

use TheCodingMachine\GraphQLite\Annotations\Factory;

class StringFilterInputFactory
{
    /**
     * @Factory()
     */
    public static function createStringFilterInput(
        ?string $equals = null,
        ?string $contains = null,
        ?string $beginsWith = null
    ): StringFilterInput {
        return new StringFilterInput(
            $equals,
            $contains,
            $beginsWith
        );
    }
}

<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Type;

use GraphQL\Type\Definition\ObjectType;

/**
 * @internal
 */
abstract class BaseType extends ObjectType
{
    /**
     * @return array
     */
    abstract public function getQueriesOrMutations();

    /**
     * @return array
     */
    abstract public function getQueryOrMutationHandlers();
}

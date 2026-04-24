<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

use TheCodingMachine\GraphQLite\Schema;

interface SchemaFactoryInterface
{
    /**
     * Returns the composed GraphQL schema, aggregating all registered
     * namespace mappers (queries, mutations, types).
     *
     * Implementations are expected to memoize the result per instance,
     * since schema construction is expensive.
     */
    public function getSchema(): Schema;
}

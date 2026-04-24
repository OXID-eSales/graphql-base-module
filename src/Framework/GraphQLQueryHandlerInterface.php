<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

interface GraphQLQueryHandlerInterface
{
    /**
     * Reads the request, executes the GraphQL query, and writes the JSON response.
     *
     * Note: the current implementation terminates the request (calls `exit`)
     * via `ResponseWriterInterface::renderJsonResponse`. Callers needing a
     * non-terminating in-process invocation should compose `SchemaFactoryInterface`
     * + `GraphQL::executeQuery()` directly until this handler is split into a
     * pure "execute" method.
     */
    public function executeGraphQLQuery(): void;
}

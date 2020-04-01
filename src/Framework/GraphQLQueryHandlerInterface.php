<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Base\Framework;

/**
 * @deprecated use GraphQLQueryHandler
 */
interface GraphQLQueryHandlerInterface
{
    public function executeGraphQLQuery(): void;
}

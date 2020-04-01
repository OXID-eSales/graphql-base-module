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

use Lcobucci\JWT\Token;

/**
 * @deprecated use RequestReader
 */
interface RequestReaderInterface
{
    /**
     * Returns the encoded token from the authorization header
     */
    public function getAuthToken(): ?Token;

    /**
     * Get the Request data
     *
     * @return array{query: string, variables: string[], operationName: string}
     */
    public function getGraphQLRequestData(): array;
}

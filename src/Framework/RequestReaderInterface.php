<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Framework;

use Lcobucci\JWT\Token;

interface RequestReaderInterface
{
    /**
     * Returns the encoded token from the authorization header
     */
    public function getAuthToken(): ?Token;

    /**
     * Get the Request data
     */
    public function getGraphQLRequestData(): array;
}

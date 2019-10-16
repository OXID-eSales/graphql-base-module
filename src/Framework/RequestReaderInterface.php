<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Framework;

use OxidEsales\GraphQL\DataObject\Token;

interface RequestReaderInterface
{
    /**
     * Returns the encoded token from the authorization header
     */
    public function getAuthToken(): ?string;

    /**
     * Get the Request data
     */
    public function getGraphQLRequestData(): array;
}

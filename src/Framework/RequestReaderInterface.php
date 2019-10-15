<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Framework;

use OxidEsales\GraphQL\DataObject\Token;
use OxidEsales\GraphQL\Exception\NoAuthHeaderException;

interface RequestReaderInterface
{
    /**
     * Returns the encoded token from the authorization header
     *
     * @throws NoAuthHeaderException
     */
    public function getAuthToken(): string;

    /**
     * Get the Request data
     *
     * @return array
     */
    public function getGraphQLRequestData();
}

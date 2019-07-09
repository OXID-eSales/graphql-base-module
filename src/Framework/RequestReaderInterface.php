<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Framework;

use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Exception\InvalidTokenException;
use OxidEsales\GraphQl\Exception\NoAuthHeaderException;

interface RequestReaderInterface
{
    /**
     * Returns the encoded token from the authorization header
     *
     * @return string
     * @throws NoAuthHeaderException
     */
    public function getAuthTokenString(): string;

    /**
     *  Get header Authorization
     *
     * @return $aHeaders array
     */
    public function getAuthorizationHeader();

    /**
     * Get the Request data
     *
     * @return array
     */
    public function getGraphQLRequestData();

}
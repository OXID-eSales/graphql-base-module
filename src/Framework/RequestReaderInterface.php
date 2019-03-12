<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Framework;

interface RequestReaderInterface
{

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
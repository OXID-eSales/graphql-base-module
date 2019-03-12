<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Framework;

interface ResponseWriterInterface
{
    /**
     * Return a JSON Object with the graphql results
     *
     * @param $aResult
     */
    public function renderJsonResponse($result, $httpStatus);
}
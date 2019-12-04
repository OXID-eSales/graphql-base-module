<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Base\Framework;

interface ResponseWriterInterface
{
    /**
     * Return a JSON Object with the graphql results
     *
     * @param mixed[] $result
     */
    public function renderJsonResponse(array $result, int $httpStatus): void;
}

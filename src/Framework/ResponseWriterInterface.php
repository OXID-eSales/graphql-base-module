<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

interface ResponseWriterInterface
{
    /**
     * Renders the GraphQL execution result as a JSON HTTP response.
     *
     * @param mixed[] $result
     */
    public function renderJsonResponse(array $result): void;
}

<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

use function header;
use function json_encode;

class ResponseWriter
{
    /**
     * Return a JSON Object with the graphql results
     *
     * @codeCoverageIgnore
     *
     * @param mixed[] $result
     */
    public function renderJsonResponse(array $result, int $httpStatus): void
    {
        $this->cleanHeaders();

        header('Access-Control-Allow-Origin: *', true, $httpStatus);
        header('Content-Type: application/json', true, $httpStatus);

        exit(json_encode($result));
    }

    /**
     * Remove all headers the shop core might have set
     */
    private function cleanHeaders(): bool
    {
        //in case headers have already been sent nothing can be cleaned
        if (!headers_sent()) {
            header_remove();

            return true;
        }

        return false;
    }
}

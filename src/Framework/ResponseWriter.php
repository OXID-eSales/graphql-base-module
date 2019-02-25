<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Framework;

class ResponseWriter implements ResponseWriterInterface
{
    /**
     * Return a JSON Object with the graphql results
     *
     * @param $aResult
     */
    public function renderJsonResponse($result, $httpStatus)
    {
        $_GET['renderPartial'] = 1;
        header('Content-Type: application/json', true, $httpStatus);
        exit(json_encode($result));

    }
}

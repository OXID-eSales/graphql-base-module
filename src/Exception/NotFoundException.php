<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Exception;

class NotFoundException extends \Exception implements HttpErrorInterface
{
    public function getHttpStatus()
    {
        return 400;
    }

}

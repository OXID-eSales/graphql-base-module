<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Exception;

use GraphQL\Error\ClientAware;

class InsufficientData extends \Exception implements HttpErrorInterface, ClientAware
{
    public function getHttpStatus()
    {
        return 400;
    }

    public function isClientSafe()
    {
        return true;
    }

    public function getCategory()
    {
        return ErrorCategories::TOKENERRORS;
    }
}

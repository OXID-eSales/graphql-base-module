<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Exception;

use GraphQL\Error\ClientAware;

class InvalidTokenException extends \Exception implements ClientAware, HttpErrorInterface
{
    public function getHttpStatus()
    {
        return 403;
    }

    public function isClientSafe()
    {
        return true;
    }

    public function getCategory()
    {
        return ErrorCategories::PERMISSIONERRORS;
    }
}

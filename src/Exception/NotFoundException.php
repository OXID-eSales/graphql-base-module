<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Exception;

use GraphQL\Error\ClientAware;

class NotFoundException extends \Exception implements ClientAware, HttpErrorInterface
{
    public function getHttpStatus()
    {
        return 404;
    }

    public function isClientSafe()
    {
        return true;
    }

    public function getCategory()
    {
        return ErrorCategories::REQUESTERROR;
    }
}

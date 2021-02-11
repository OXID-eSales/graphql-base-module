<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Exception;

class InvalidRequest extends Error implements HttpErrorInterface
{
    public function getHttpStatus(): int
    {
        return 500;
    }

    public function getCategory(): string
    {
        return ErrorCategories::REQUESTERROR;
    }
}

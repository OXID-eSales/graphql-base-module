<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Exception;

class NotFound extends Error
{
    protected const NOT_FOUND_MESSAGE = 'Queried data was not found';

    public function getCategory(): string
    {
        return ErrorCategories::REQUESTERROR;
    }

    public static function notFound(): self
    {
        return new self(self::NOT_FOUND_MESSAGE);
    }
}

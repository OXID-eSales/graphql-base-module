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

    public function __construct(string $message = self::NOT_FOUND_MESSAGE, array $extensions = [])
    {
        parent::__construct($message, 0, null, 'Exception', $extensions);
    }

    public function getCategory(): string
    {
        return ErrorCategories::REQUESTERROR;
    }
}

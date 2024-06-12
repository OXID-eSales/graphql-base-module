<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Exception;

class UnableToParseToken extends Error
{
    protected const UNABLE_TO_PARSE_MESSAGE = 'Unable to parse token';

    public function __construct(string $message = self::UNABLE_TO_PARSE_MESSAGE, array $extensions = [])
    {
        parent::__construct(
            message: $message,
            extensions: $extensions
        );
    }

    public function getCategory(): string
    {
        return ErrorCategories::PERMISSIONERRORS;
    }
}

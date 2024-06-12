<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Exception;

class InvalidToken extends Error
{
    protected const INVALID_TOKEN_MESSAGE = 'The token is invalid';

    public function __construct(string $message = self::INVALID_TOKEN_MESSAGE, array $extensions = [])
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

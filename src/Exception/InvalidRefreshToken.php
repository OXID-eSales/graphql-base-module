<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Exception;

class InvalidRefreshToken extends Error
{
    protected const INVALID_REFRESH_TOKEN_MESSAGE = 'The refresh token is invalid';

    public function __construct(string $message = self::INVALID_REFRESH_TOKEN_MESSAGE, array $extensions = [])
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

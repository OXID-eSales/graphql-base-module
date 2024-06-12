<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Exception;

class TokenUserBlocked extends Error
{
    protected const USER_BLOCKED_MESSAGE = 'User is blocked';

    public function __construct(string $message = self::USER_BLOCKED_MESSAGE, array $extensions = [])
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

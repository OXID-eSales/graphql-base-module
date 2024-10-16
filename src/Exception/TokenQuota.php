<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Exception;

class TokenQuota extends Error
{
    protected const QUOTA_EXCEEDED_MESSAGE = 'Token quota exceeded.';

    public function __construct(string $message = self::QUOTA_EXCEEDED_MESSAGE, array $extensions = [])
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

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

    public function getCategory(): string
    {
        return ErrorCategories::PERMISSIONERRORS;
    }

    public static function quotaExceeded(): self
    {
        return new self(self::QUOTA_EXCEEDED_MESSAGE);
    }
}

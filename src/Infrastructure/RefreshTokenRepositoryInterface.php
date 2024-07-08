<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Infrastructure;

use OxidEsales\GraphQL\Base\DataType\RefreshToken as RefreshTokenDataType;

interface RefreshTokenRepositoryInterface
{
    public function getNewRefreshToken(string $userId, string $lifeTime): RefreshTokenDataType;
}

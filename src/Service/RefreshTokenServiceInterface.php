<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\DataType\User as UserDataType;

/**
 * Token data access service
 */
interface RefreshTokenServiceInterface
{
    public function createToken(UserDataType $user): string;
}

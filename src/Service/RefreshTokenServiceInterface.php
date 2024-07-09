<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\DataType\UserInterface;

/**
 * Token data access service
 */
interface RefreshTokenServiceInterface
{
    public function createRefreshTokenForUser(UserInterface $user): string;

    public function refreshToken(string $refreshToken, string $fingerprintHash): string;
}

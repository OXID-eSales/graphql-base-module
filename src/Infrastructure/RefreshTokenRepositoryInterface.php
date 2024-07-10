<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Infrastructure;

use OxidEsales\GraphQL\Base\DataType\RefreshToken as RefreshTokenDataType;
use OxidEsales\GraphQL\Base\DataType\UserInterface;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Exception\TokenQuota;

interface RefreshTokenRepositoryInterface
{
    public function getNewRefreshToken(string $userId, string $lifeTime): RefreshTokenDataType;

    public function removeExpiredTokens(): void;

    /**
     * todo: change exception to InvalidRefreshToken
     * @throws InvalidToken
     */
    public function getTokenUser(string $refreshToken): UserInterface;
}

<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\DataType\Login as LoginDatatype;
use OxidEsales\GraphQL\Base\DataType\User as UserDataType;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;

/**
 * User login service
 */
class Login
{
    public function __construct(
        private readonly Legacy $legacyInfrastructure,
        private readonly Token $accessTokenService,
        private readonly RefreshTokenServiceInterface $refreshTokenService
    ) {
    }

    public function login(?string $username = null, ?string $password = null): LoginDatatype
    {
        /** @var UserDataType $user */
        $user = $this->legacyInfrastructure->login($username, $password);

        $refreshToken = $this->refreshTokenService->createToken($user);
        $accessToken = $this->accessTokenService->refreshToken($refreshToken);

        return new LoginDatatype($refreshToken, $accessToken);
    }
}

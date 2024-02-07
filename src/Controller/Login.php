<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Controller;

use OxidEsales\GraphQL\Base\DataType\Login as DataTypeLogin;
use OxidEsales\GraphQL\Base\Service\RefreshToken as RefreshTokenService;
use TheCodingMachine\GraphQLite\Annotations\Query;

class Login
{
    public function __construct(
        protected RefreshTokenService $tokenService
    ) {
    }

    /**
     * Retrieve a refresh token and access token
     *
     * @Query
     */
    public function login(?string $username = null, ?string $password = null): DataTypeLogin
    {
        return $this->tokenService->login($username, $password);
    }
}

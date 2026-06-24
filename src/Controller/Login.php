<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Controller;

use OxidEsales\GraphQL\Base\DataType\LoginPayloadInterface;
use OxidEsales\GraphQL\Base\DataType\TokenPayloadInterface;
use OxidEsales\GraphQL\Base\Service\LoginServiceInterface;
use OxidEsales\GraphQL\Base\Service\Token;
use TheCodingMachine\GraphQLite\Annotations\Query;

class Login
{
    public function __construct(
        protected Token $tokenService,
        protected LoginServiceInterface $loginService,
    ) {
    }

    /**
     * Query of Base Module.
     * Retrieve a JWT for authentication of further requests
     */
    #[Query(outputType: 'TokenPayload')]
    public function token(?string $username = null, ?string $password = null): TokenPayloadInterface
    {
        return $this->tokenService->createToken($username, $password);
    }

    /**
     * Query of Base Module.
     * Retrieve a refresh token and access token
     */
    #[Query(outputType: 'LoginPayload')]
    public function login(?string $username = null, ?string $password = null): LoginPayloadInterface
    {
        return $this->loginService->login($username, $password);
    }
}

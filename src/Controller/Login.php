<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Controller;

use OxidEsales\GraphQL\Base\Service\Token;
use TheCodingMachine\GraphQLite\Annotations\Query;

class Login
{
    public function __construct(protected Token $tokenService)
    {
    }

    /**
     * Query of Base Module.
     * Retrieve a JWT for authentication of further requests
     *
     * @Query
     */
    public function token(?string $username = null, ?string $password = null): string
    {
        return $this->tokenService->createToken(
            $username,
            $password
        )->toString();
    }
}

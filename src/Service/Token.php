<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use Lcobucci\JWT\UnencryptedToken;

/**
 * Token data access service
 */
class Token
{
    /** @var ?UnencryptedToken */
    private $token;

    public function __construct(
        ?UnencryptedToken $token = null
    ) {
        $this->token = $token;
    }

    public function getTokenClaim(string $claim, $default = null)
    {
        if (!$this->token instanceof UnencryptedToken) {
            return $default;
        }

        return $this->token->claims()->get($claim, $default);
    }

    public function checkTokenHasClaim(string $claim): bool
    {
        if (!$this->token instanceof UnencryptedToken) {
            return false;
        }

        return $this->token->claims()->has($claim);
    }

    public function getToken(): ?UnencryptedToken
    {
        return $this->token;
    }
}

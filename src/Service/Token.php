<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;

/**
 * Token data access service
 */
class Token
{
    /** @var ?UnencryptedToken */
    private $token;

    /** @var JwtConfigurationBuilder */
    private $jwtConfigurationBuilder;

    /** @var Legacy */
    private $legacyInfrastructure;

    public function __construct(
        ?UnencryptedToken $token = null,
        JwtConfigurationBuilder $jwtConfigurationBuilder,
        Legacy $legacyInfrastructure
    ) {
        $this->token = $token;
        $this->jwtConfigurationBuilder = $jwtConfigurationBuilder;
        $this->legacyInfrastructure = $legacyInfrastructure;
    }

    public function getConfiguration()
    {
        return $this->jwtConfigurationBuilder->getConfiguration();
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

    /**
     * Checks if given token is valid:
     * - has valid signature
     * - has valid issuer and audience
     * - has valid shop claim
     *
     * @internal
     */
    public function isTokenValid(UnencryptedToken $token): bool
    {
        $config = $this->jwtConfigurationBuilder->getConfiguration();
        $validator = $config->validator();

        if (!$validator->validate($token, ...$config->validationConstraints())
            || !$this->checkTokenHasClaim(Authentication::CLAIM_SHOPID)
            || $this->getTokenClaim(Authentication::CLAIM_SHOPID) !== $this->legacyInfrastructure->getShopId()) {
            return false;
        }

        return true;
    }
}

<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
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
        ?UnencryptedToken $token,
        JwtConfigurationBuilder $jwtConfigurationBuilder,
        Legacy $legacyInfrastructure
    ) {
        $this->token                   = $token;
        $this->jwtConfigurationBuilder = $jwtConfigurationBuilder;
        $this->legacyInfrastructure    = $legacyInfrastructure;
    }

    public function getConfiguration(): Configuration
    {
        return $this->jwtConfigurationBuilder->getConfiguration();
    }

    /**
     * @param null|mixed $default
     *
     * @return null|mixed
     */
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
     * - token user is not in blocked group
     *
     * @throws InvalidToken
     *
     * @internal
     */
    public function validateToken(UnencryptedToken $token): void
    {
        if (!$this->areConstraintsValid($token)) {
            throw InvalidToken::invalidToken();
        }

        if ($this->isUserBlocked($this->getTokenClaim(Authentication::CLAIM_USERID))) {
            throw InvalidToken::userBlocked();
        }
    }

    protected function areConstraintsValid(UnencryptedToken $token): bool
    {
        $config = $this->jwtConfigurationBuilder->getConfiguration();
        $validator = $config->validator();

        return $validator->validate($token, ...$config->validationConstraints());
    }

    protected function isUserBlocked(?string $userId): bool
    {
        $groups = $this->legacyInfrastructure->getUserGroupIds($userId);

        if (in_array('oxidblocked', $groups)) {
            return true;
        }

        return false;
    }
}

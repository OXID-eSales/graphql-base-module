<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;

/**
 * Token data access service
 */
class TokenValidator
{
    /** @var JwtConfigurationBuilder */
    private $jwtConfigurationBuilder;

    /** @var Legacy */
    private $legacyInfrastructure;

    public function __construct(
        JwtConfigurationBuilder $jwtConfigurationBuilder,
        Legacy $legacyInfrastructure
    ) {
        $this->jwtConfigurationBuilder = $jwtConfigurationBuilder;
        $this->legacyInfrastructure    = $legacyInfrastructure;
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

        if ($this->isUserBlocked($token->claims()->get(Token::CLAIM_USERID))) {
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

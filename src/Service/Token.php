<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Event\BeforeTokenCreation;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Token data access service
 */
class Token
{
    public const CLAIM_SHOPID   = 'shopid';

    public const CLAIM_USERNAME = 'username';

    public const CLAIM_USERID   = 'userid';

    public const CLAIM_USER_ANONYMOUS   = 'useranonymous';

    /** @var ?UnencryptedToken */
    private $token;

    /** @var JwtConfigurationBuilder */
    private $jwtConfigurationBuilder;

    /** @var Legacy */
    private $legacyInfrastructure;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        ?UnencryptedToken $token,
        JwtConfigurationBuilder $jwtConfigurationBuilder,
        Legacy $legacyInfrastructure,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->token                   = $token;
        $this->jwtConfigurationBuilder = $jwtConfigurationBuilder;
        $this->legacyInfrastructure    = $legacyInfrastructure;
        $this->eventDispatcher         = $eventDispatcher;
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

        if ($this->isUserBlocked($this->getTokenClaim(Token::CLAIM_USERID))) {
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

    /**
     * @throws InvalidLogin
     */
    public function createToken(?string $username = null, ?string $password = null): UnencryptedToken
    {
        $user   = $this->legacyInfrastructure->login($username, $password);
        $time   = new DateTimeImmutable('now');
        $expire = new DateTimeImmutable('+8 hours');
        $config = $this->getConfiguration();

        $builder = $config->builder()
            ->issuedBy($this->legacyInfrastructure->getShopUrl())
            ->withHeader('iss', $this->legacyInfrastructure->getShopUrl())
            ->permittedFor($this->legacyInfrastructure->getShopUrl())
            ->issuedAt($time)
            ->canOnlyBeUsedAfter($time)
            ->expiresAt($expire)
            ->withClaim(self::CLAIM_SHOPID, $this->legacyInfrastructure->getShopId())
            ->withClaim(self::CLAIM_USERNAME, $user->email())
            ->withClaim(self::CLAIM_USERID, $user->id()->val())
            ->withClaim(self::CLAIM_USER_ANONYMOUS, $user->isAnonymous());

        $event = new BeforeTokenCreation($builder, $user);
        $this->eventDispatcher->dispatch(
            BeforeTokenCreation::NAME,
            $event
        );

        return $event->getBuilder()->getToken(
            $config->signer(),
            $config->signingKey()
        );
    }
}

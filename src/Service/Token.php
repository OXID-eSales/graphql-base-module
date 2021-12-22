<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use DateTimeImmutable;
use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\GraphQL\Base\DataType\User as UserDataType;
use OxidEsales\GraphQL\Base\Event\BeforeTokenCreation;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Exception\TokenQuota;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Base\Infrastructure\Token as TokenInfrastructure;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * Token data access service
 */
class Token
{
    public const CLAIM_SHOPID   = 'shopid';

    public const CLAIM_USERNAME = 'username';

    public const CLAIM_USERID   = 'userid';

    public const CLAIM_USER_ANONYMOUS   = 'useranonymous';

    public const CLAIM_TOKENID   = 'tokenid';

    /** @var ?UnencryptedToken */
    private $token;

    /** @var JwtConfigurationBuilder */
    private $jwtConfigurationBuilder;

    /** @var Legacy */
    private $legacyInfrastructure;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var ModuleConfiguration */
    private $moduleConfiguration;

    /** @var TokenInfrastructure */
    private $tokenInfrastructure;

    public function __construct(
        ?UnencryptedToken $token,
        JwtConfigurationBuilder $jwtConfigurationBuilder,
        Legacy $legacyInfrastructure,
        EventDispatcherInterface $eventDispatcher,
        ModuleConfiguration $moduleConfiguration,
        TokenInfrastructure $tokenInfrastructure
    ) {
        $this->token                   = $token;
        $this->jwtConfigurationBuilder = $jwtConfigurationBuilder;
        $this->legacyInfrastructure    = $legacyInfrastructure;
        $this->eventDispatcher         = $eventDispatcher;
        $this->moduleConfiguration     = $moduleConfiguration;
        $this->tokenInfrastructure     = $tokenInfrastructure;
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

    public function getToken(): ?UnencryptedToken
    {
        return $this->token;
    }

    /**
     * @throws InvalidLogin
     * @throws TokenQuota
     */
    public function createToken(?string $username = null, ?string $password = null): UnencryptedToken
    {
        /** @var UserDataType $user */
        $user   = $this->legacyInfrastructure->login($username, $password);
        $this->removeExpiredTokens($user);
        $this->canIssueToken($user);

        $time   = new DateTimeImmutable('now');
        $expire = new DateTimeImmutable($this->moduleConfiguration->getTokenLifeTime());
        $config = $this->jwtConfigurationBuilder->getConfiguration();

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
            ->withClaim(self::CLAIM_USER_ANONYMOUS, $user->isAnonymous())
            ->withClaim(self::CLAIM_TOKENID, Legacy::createUniqueIdentifier());

        $event = new BeforeTokenCreation($builder, $user);
        $this->eventDispatcher->dispatch(
            BeforeTokenCreation::NAME,
            $event
        );

        $token = $event->getBuilder()->getToken(
            $config->signer(),
            $config->signingKey()
        );

        $this->registerToken($user, $token, $time, $expire);

        return $token;
    }

    public function deleteToken(ID $tokenId): void
    {
        $tokenId = (string) $tokenId;

        if ($this->tokenInfrastructure->isTokenRegistered($tokenId)) {
            $this->tokenInfrastructure->tokenDelete(null, $tokenId);
        } else {
            throw InvalidToken::unknownToken();
        }
    }

    public function deleteUserToken(UserDataType $user, ID $tokenId): void
    {
        if ($this->tokenInfrastructure->userHasToken($user, (string) $tokenId)) {
            $this->tokenInfrastructure->tokenDelete($user, (string) $tokenId);
        } else {
            throw InvalidToken::unknownToken();
        }
    }

    private function registerToken(UserDataType $user, UnencryptedToken $token, DateTimeImmutable $time, DateTimeImmutable $expire): void
    {
        if (!$user->isAnonymous()) {
            $this->tokenInfrastructure->registerToken($token, $time, $expire);
        }
    }

    private function canIssueToken(UserDataType $user): void
    {
        if (!$user->isAnonymous() &&
            !$this->tokenInfrastructure->canIssueToken($user, $this->moduleConfiguration->getUserTokenQuota())) {
            throw TokenQuota::quotaExceeded();
        }
    }

    private function removeExpiredTokens(UserDataType $user): void
    {
        if (!$user->isAnonymous()) {
            $this->tokenInfrastructure->removeExpiredTokens($user);
        }
    }
}

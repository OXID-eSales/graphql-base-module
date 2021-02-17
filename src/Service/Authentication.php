<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Framework\NullToken;
use OxidEsales\GraphQL\Base\Framework\UserData;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyService;

use TheCodingMachine\GraphQLite\Security\AuthenticationServiceInterface;

class Authentication implements AuthenticationServiceInterface
{
    public const CLAIM_SHOPID   = 'shopid';

    public const CLAIM_USERNAME = 'username';

    public const CLAIM_MODE = 'mode';

    public const CLAIM_USERID   = 'userid';

    public const CLAIM_GROUPS   = 'groups';

    /** @var KeyRegistry */
    private $keyRegistry;

    /** @var LegacyService */
    private $legacyService;

    /** @var ?Token */
    private $token;

    public function __construct(
        KeyRegistry $keyRegistry,
        LegacyService $legacyService,
        Token $token
    ) {
        $this->keyRegistry   = $keyRegistry;
        $this->legacyService = $legacyService;
        $this->token         = $token;
    }

    /**
     * @throws InvalidToken
     */
    public function isLogged(): bool
    {
        if ($this->token === null) {
            return false;
        }

        if ($this->token instanceof NullToken) {
            return false;
        }

        if ($this->isValidToken($this->token)) {
            return true;
        }

        throw new InvalidToken('The token is invalid');
    }

    /**
     * @throws InvalidLogin
     */
    public function createToken(string $username, string $password): Token
    {
        /** @var UserData $userData */
        $userData = $this->legacyService->login($username, $password);
        $time     = new DateTimeImmutable('now');
        $expire   = new DateTimeImmutable('+8 hours');
        $config   = $this->getConfig();
        $groups   = $userData->getUserGroupIds();
        $groups['oxtoken'] = 'oxtoken';

        return $config->builder()
            ->issuedBy($this->legacyService->getShopUrl())
            ->withHeader('iss', $this->legacyService->getShopUrl())
            ->permittedFor($this->legacyService->getShopUrl())
            ->issuedAt($time)
            ->canOnlyBeUsedAfter($time)
            ->expiresAt($expire)
            ->withClaim(self::CLAIM_SHOPID, $this->legacyService->getShopId())
            ->withClaim(self::CLAIM_USERNAME, $username)
            ->withClaim(self::CLAIM_MODE, true)
            ->withClaim(self::CLAIM_USERID, $userData->getUserId())
            ->withClaim(self::CLAIM_GROUPS, $groups)
            ->getToken(
                $config->signer(),
                $config->signingKey()
            );
    }

    public function createAnonToken()
    {
        $anonUserId = $this->generateAnonUserData();
        $time     = new DateTimeImmutable('now');
        $expire   = new DateTimeImmutable('+8 hours');
        $config   = $this->getConfig();

        return $config->builder()
            ->issuedBy($this->legacyService->getShopUrl())
            ->withHeader('iss', $this->legacyService->getShopUrl())
            ->permittedFor($this->legacyService->getShopUrl())
            ->issuedAt($time)
            ->canOnlyBeUsedAfter($time)
            ->expiresAt($expire)
            ->withClaim(self::CLAIM_SHOPID, $this->legacyService->getShopId())
            ->withClaim(self::CLAIM_MODE, false)
            ->withClaim(self::CLAIM_USERID, $anonUserId)
            ->withClaim(self::CLAIM_GROUPS, ['oxtoken' => 'oxtoken'])
            ->getToken(
                $config->signer(),
                $config->signingKey()
            );
    }

    private function generateAnonUserData()
    {
        return md5(uniqid());
    }

    /**
     * @throws InvalidToken
     */
    public function getUserName(): string
    {
        if (!$this->isLogged() || !$this->token) {
            throw new InvalidToken('The token is invalid');
        }

        return (string) $this->token->claims()->get(self::CLAIM_USERNAME);
    }

    /**
     * @throws InvalidToken
     */
    public function getUserId(): string
    {
        if (!$this->isLogged() || !$this->token) {
            throw new InvalidToken('The token is invalid');
        }

        return (string) $this->token->claims()->get(self::CLAIM_USERID);
    }

    /**
     * @throws InvalidToken
     */
    public function getUserMode(): bool
    {
        if (!$this->isLogged() || !$this->token) {
            throw new InvalidToken('The token is invalid');
        }

        return (bool) $this->token->claims()->get(self::CLAIM_MODE);
    }

    public function getConfig(): Configuration
    {
        $config = Configuration::forSymmetricSigner(
            new Sha512(),
            InMemory::plainText($this->keyRegistry->getSignatureKey())
        );

        $issuedBy     = new IssuedBy($this->legacyService->getShopUrl());
        $permittedFor = new PermittedFor($this->legacyService->getShopUrl());
        $config->setValidationConstraints($issuedBy, $permittedFor);

        return $config;
    }

    /**
     * Checks if given token is valid:
     * - has valid signature
     * - has valid issuer and audience
     * - has valid shop claim
     *
     * @internal
     */
    private function isValidToken(Token $token): bool
    {
        $config    = $this->getConfig();
        $validator = $config->validator();

        if (!$validator->validate($token, ...$config->validationConstraints())) {
            return false;
        }

        if (!$token->claims()->has(self::CLAIM_SHOPID)) {
            return false;
        }

        if ($token->claims()->get(self::CLAIM_SHOPID) !== $this->legacyService->getShopId()) {
            return false;
        }

        return true;
    }
}

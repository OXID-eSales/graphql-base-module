<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Service\Legacy as LegacyService;
use TheCodingMachine\GraphQLite\Security\AuthenticationServiceInterface;

use function time;

class AuthenticationService implements AuthenticationServiceInterface
{
    public const CLAIM_SHOPID   = 'shopid';

    public const CLAIM_USERNAME = 'username';

    public const CLAIM_GROUP    = 'group';

    /** @var KeyRegistry */
    private $keyRegistry;

    /** @var LegacyService */
    private $legacyService;

    /** @var ?Token */
    private $token;

    public function __construct(
        KeyRegistry $keyRegistry,
        LegacyService $legacyService
    ) {
        $this->keyRegistry   = $keyRegistry;
        $this->legacyService = $legacyService;
    }

    public function setToken(?Token $token = null): void
    {
        $this->token = $token;
    }

    /**
     * @throws InvalidToken
     */
    public function isLogged(): bool
    {
        if ($this->token === null) {
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
        $this->legacyService->checkCredentials($username, $password);
        $usergroup = $this->legacyService->getUserGroup($username);
        $builder   = $this->getTokenBuilder()->withClaim(self::CLAIM_USERNAME, $username)
            ->withClaim(self::CLAIM_GROUP, $usergroup);

        return $builder->getToken(
            $this->getSigner(),
            $this->getSignatureKey()
        );
    }

    /**
     * @internal
     */
    private function getTokenBuilder(): Builder
    {
        $time = time();

        return (new Builder())
            ->issuedBy($this->legacyService->getShopUrl())
            ->permittedFor($this->legacyService->getShopUrl())
            ->issuedAt($time)
            ->canOnlyBeUsedAfter($time)
            ->expiresAt($time + 3600 * 8)
            ->withClaim(self::CLAIM_SHOPID, $this->legacyService->getShopId());
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
        if (!$token->verify($this->getSigner(), $this->getSignatureKey()->getContent())) {
            return false;
        }
        $validation = new ValidationData();
        $validation->setIssuer($this->legacyService->getShopUrl());
        $validation->setAudience($this->legacyService->getShopUrl());

        if (!$token->validate($validation)) {
            return false;
        }

        if ($token->getClaim(self::CLAIM_SHOPID) !== $this->legacyService->getShopId()) {
            return false;
        }

        return true;
    }

    /**
     * @internal
     */
    private function getSignatureKey(): Key
    {
        return new Key($this->keyRegistry->getSignatureKey());
    }

    /**
     * @internal
     */
    private function getSigner(): Signer
    {
        return new Sha512();
    }
}

<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Service;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use OxidEsales\GraphQL\Exception\InvalidTokenException;
use OxidEsales\GraphQL\Framework\RequestReaderInterface;

class AuthenticationService implements AuthenticationServiceInterface
{
    const CLAIM_SHOPID   = 'shopid';
    const CLAIM_USERNAME = 'username';
    const CLAIM_GROUP    = 'group';

    /** @var KeyRegistryInterface */
    private $keyRegistry = null;

    /** @var LegacyServiceInterface */
    private $legacyService = null;

    /** @var Token */
    private $token = null;

    public function __construct(
        KeyRegistryInterface $keyRegistry,
        LegacyServiceInterface $legacyService
    ) {
        $this->keyRegistry = $keyRegistry;
        $this->legacyService = $legacyService;
    }

    public static function createTokenFromRequest(RequestReaderInterface $requestReader): ?Token
    {
        $token = $requestReader->getAuthToken();
        if ($token === null) {
            return null;
        }
        try {
            $token = (new Parser())->parse($token);
        } catch (\Exception $e) {
            throw new InvalidTokenException('The token is invalid');
        }
        return $token;
    }

    public function setToken(?Token $token = null)
    {
        $this->token = $token;
    }
 
    public function isLogged(): bool
    {
        if ($this->token === null) {
            return false;
        }
        if ($this->isValidToken($this->token)) {
            return true;
        }
        throw new InvalidTokenException('The token is invalid');
    }

    public function createAuthenticatedToken(string $username, string $password): Token
    {
        $this->legacyService->checkCredentials($username, $password);
        return $this->createUnauthenticatedToken($username);
    }

    public function createUnauthenticatedToken(string $username, string $usergroup = null): Token
    {
        if ($usergroup === null) {
            $usergroup = $this->legacyService->getUserGroup($username);
        }

        $builder = $this->getInitializedTokenBuilder()->withClaim(self::CLAIM_USERNAME, $username)
            ->withClaim(self::CLAIM_GROUP, $usergroup);

        return $builder->getToken(
            $this->getSigner(),
            $this->getSignatureKey()
        );
    }

    private function getInitializedTokenBuilder(): Builder
    {
        $time = time();
        $token = (new Builder())
            ->issuedBy($this->legacyService->getShopUrl())
            ->permittedFor($this->legacyService->getShopUrl())
            ->issuedAt($time)
            ->canOnlyBeUsedAfter($time)
            ->expiresAt($time + 3600 * 8)
            ->withClaim(self::CLAIM_SHOPID, $this->legacyService->getShopId());
        return $token;
    }

    /**
     * Checks if given token is valid:
     * - has valid signature
     * - has valid issuer and audience
     * - has valid shop claim
     */
    private function isValidToken(Token $token): bool
    {
        if (!$token->verify($this->getSigner(), $this->getSignatureKey())) {
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

    private function getSignatureKey(): Key
    {
        return new Key($this->keyRegistry->getSignatureKey());
    }

    private function getSigner(): Signer
    {
        return new Sha512();
    }
}

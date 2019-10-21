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
use OxidEsales\EshopCommunity\Application\Model\User;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\GraphQL\Exception\InvalidLoginException;
use OxidEsales\GraphQL\Exception\InvalidTokenException;
use OxidEsales\GraphQL\Framework\RequestReaderInterface;

class AuthenticationService implements AuthenticationServiceInterface
{
    const CLAIM_SHOPID   = 'shopid';
    const CLAIM_USERNAME = 'username';
    const CLAIM_GROUP    = 'group';

    /** @var KeyRegistryInterface */
    private $keyRegistry = null;

    /** @var Token */
    private $token = null;

    public function __construct(
        KeyRegistryInterface $keyRegistry
    ) {
        $this->keyRegistry = $keyRegistry;
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

    public function createToken(string $username, string $password, int $shopid = null): Token
    {
        try {
            oxNew(User::class)->login($username, $password, false);
        } catch (\Exception $e) {
            throw new InvalidLoginException('Username/password combination is invalid');
        }

        // now get the builder and create a token
        $builder = $this->createBasicToken();
        $token = $builder->withClaim(self::CLAIM_USERNAME, $username);
        return $token->getToken(
            $this->getSigner(),
            $this->getSignatureKey()
        );
    }

    private function createBasicToken(): Builder
    {
        $time = time();
        $token = (new Builder())
            ->issuedBy(Registry::getConfig()->getShopUrl())
            ->permittedFor(Registry::getConfig()->getShopUrl())
            ->issuedAt($time)
            ->canOnlyBeUsedAfter($time)
            ->expiresAt($time + 3600 * 8)
            ->withClaim(self::CLAIM_SHOPID, Registry::getConfig()->getShopId());
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
        $validation->setIssuer(Registry::getConfig()->getShopUrl());
        $validation->setAudience(Registry::getConfig()->getShopUrl());
        if (!$token->validate($validation)) {
            return false;
        }
        if ($token->getClaim(self::CLAIM_SHOPID) !== Registry::getConfig()->getShopId()) {
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

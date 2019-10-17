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
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\GraphQL\Dao\UserDaoInterface;
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

    /** @var UserDaoInterface|null */
    private $userDao = null;

    /** @var PasswordServiceBridgeInterface */
    private $passwordService = null;

    public function __construct(
        KeyRegistryInterface $keyRegistry,
        RequestReaderInterface $requestReader,
        UserDaoInterface $userDao,
        PasswordServiceBridgeInterface $passwordService
    ) {
        $this->keyRegistry = $keyRegistry;
        try {
            $this->token = (new Parser())->parse($requestReader->getAuthToken());
        } catch (\Exception $e) {
            $this->token = null;
        }
        $this->userDao = $userDao;
        $this->passwordService = $passwordService;
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

    public function isAllowed(string $right): bool
    {
        return false;
    }

    public function createToken(string $username, string $password, int $shopid = null): Token
    {
        /**
         * this is not working with the "old" md5 passworts from testing library
         * so we could douplicate the code from User::login() or just call it for
         * the time being and live with it ;-)
        $user = $this->userDao->getUserByName($username, $shopid);
        if (!$user ||
            !$this->passwordService->verifyPassword(
                $password,
                $user->getPasswordhash()
            )) {
            throw new InvalidLoginException('Username/password combination is invalid');
        }
         */
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

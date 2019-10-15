<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Service;

# use OxidEsales\GraphQl\DataObject\Token;
# use OxidEsales\GraphQl\DataObject\TokenRequest;
# use OxidEsales\GraphQl\DataObject\User;
# use OxidEsales\GraphQl\Exception\PasswordMismatchException;
# use OxidEsales\GraphQl\Utility\AuthConstants;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\GraphQl\Dao\UserDaoInterface;
use OxidEsales\GraphQl\Exception\NoAuthHeaderException;
use OxidEsales\GraphQl\Framework\RequestReaderInterface;

class AuthenticationService implements AuthenticationServiceInterface
{
    /** @var KeyRegistryInterface */
    protected $keyRegistry;

    /** @var RequestReaderInterface */
    private $requestReader;

    /** @var UserDaoInterface */
    protected $userDao;

    public function __construct(
        KeyRegistryInterface $keyRegistry,
        RequestReaderInterface $requestReader,
        UserDaoInterface $userDao
    ) {
        $this->keyRegistry = $keyRegistry;
        $this->requestReader = $requestReader;
        $this->userDao = $userDao;
    }
 
    public function isLogged(): bool
    {
        try {
            $token = $this->requestReader->getAuthToken();
        } catch (NoAuthHeaderException $e) {
            return false;
        }
        return $this->isValidToken($token);
    }

    public function isAllowed(string $right): bool
    {
        return false;
    }

    public function createToken(string $username = '', string $password = '', string $lang = null, int $shopid = null): Token
    {
        // throws an exception if something goes wrong
        oxNew(User::class)->login($username, $password, false);

        // now get the builder and create a token
        $builder = $this->createBasicToken();
        $token = $builder->withClaim('username', $username);
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
            ->expiresAt($time + 3600)
            ->withClaim('shopid', Registry::getConfig()->getShopId())
            ->withClaim('lang', Registry::getLang()->getBaseLanguage());
        return $token;
    }

    /**
     * Checks if given token is valid:
     * - has valid signature
     * - has valid issuer and audience
     * - has valid shop claim
     */
    private function isValidToken(string $token): bool
    {
        $token = (new Parser())->parse($token);
        if (!$token->verify($this->getSigner(), $this->getSignatureKey())) {
            return false;
        }
        $validation = new ValidationData();
        $validation->setIssuer(Registry::getConfig()->getShopUrl());
        $validation->setAudience(Registry::getConfig()->getShopUrl());
        if (!$token->validate($validation)) {
            return false;
        }
        if ($token->getClaim('shopid') !== Registry::getConfig()->getShopId()) {
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

    /*
    public function getToken(TokenRequest $tokenRequest): Token
    {
        $token = null;
        if ($tokenRequest->getGroup() == AuthConstants::USER_GROUP_DEVELOPER) {
            return $this->getDeveloperToken($tokenRequest);
        }
        if ($tokenRequest->getUsername()) {
            return $this->getUserToken($tokenRequest);
        }
        return $this->getAnonymousToken($tokenRequest);
    }

    private function getDeveloperToken(TokenRequest $tokenRequest)
    {
        $tokenRequest->setUserid('developerid');
        $tokenRequest->setUsername('developer');
        return $this->createToken($tokenRequest);
    }

    private function getAnonymousToken(TokenRequest $tokenRequest)
    {
        $tokenRequest->setUserid('anonymousid');
        $tokenRequest->setUsername('anonymous');
        $tokenRequest->setGroup('anonymous');
        return $this->createToken($tokenRequest);
    }

    private function getUserToken(TokenRequest $tokenRequest)
    {
        if (! $tokenRequest->getPassword() || ! $tokenRequest->getShopid()) {
            throw new PasswordMismatchException();
        }

        # $tokenRequest = $this->userDao->addIdAndUserGroupToTokenRequest($tokenRequest);

        $token = $this->createToken($tokenRequest);

        // Retain token id when the user was already logged in anonymously
        // and the logged in user is a simple customer
        $authToken = $tokenRequest->getCurrentToken();
        if ($authToken != null &&
            $authToken->getUserGroup() == AuthConstants::USER_GROUP_ANONMYOUS &&
            $token->getUserGroup() == AuthConstants::USER_GROUP_CUSTOMER) {
            $token->setKey($authToken->getKey());
        }

        return $token;
    }

    private function createToken(TokenRequest $tokenRequest)
    {
        $token = new Token();
        $token->setSubject($tokenRequest->getUserid());
        $token->setUserGroup($tokenRequest->getGroup());
        $token->setLang($tokenRequest->getLang());
        $token->setShopid($tokenRequest->getShopid());
        $token->setShopUrl($this->environmentService->getShopUrl());
        $token->setUserName($tokenRequest->getUsername());

        return $token;
    }
     */
}

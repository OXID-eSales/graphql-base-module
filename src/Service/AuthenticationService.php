<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Service;

use OxidEsales\GraphQl\Dao\TokenDaoInterface;
use OxidEsales\GraphQl\Dao\UserDaoInterface;
use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\DataObject\TokenRequest;
use OxidEsales\GraphQl\Exception\NoTokenFoundException;

class AuthenticationService implements AuthenticationServiceInterface
{
    /** @var  KeyRegistryInterface $keyRegistry */
    private $keyRegistry;

    /** @var TokenDaoInterface $tokenDao */
    private $tokenDao;

    /** @var  EnvironmentServiceInterface $environmentService */
    private $environmentService;

    /** @var  UserDaoInterface $userDao */
    private $userDao;

    public function __construct(
        KeyRegistryInterface $keyRegistry,
        TokenDaoInterface $tokenDao,
        EnvironmentServiceInterface $environmentService,
        UserDaoInterface $userDao
    )
    {
        $this->keyRegistry = $keyRegistry;
        $this->tokenDao = $tokenDao;
        $this->environmentService = $environmentService;
        $this->userDao = $userDao;
    }

    /**
     * @param TokenRequest $tokenRequest
     *
     * @return string
     */
    public function getToken(TokenRequest $tokenRequest)
    {
        if ($tokenRequest->getUserName() === null) {
            $this->getAnonymousToken($tokenRequest);
        }

        return $this->getUserToken($tokenRequest);
    }

    public function getAnonymousToken(TokenRequest $tokenRequest)
    {
        throw new \Exception("Not yet implemented");
    }

    public function getUserToken(TokenRequest $tokenRequest)
    {
        $this->userDao->verifyPassword($tokenRequest->getUsername(), $tokenRequest->getPassword());

        $token = null;

        try {
            $token = $this->tokenDao->loadToken($tokenRequest->getUsername(), $tokenRequest->getShopid());
        } catch (NoTokenFoundException $e) {
            // pass
        }

        if ($token === null) {
            $token = $this->createNewToken($tokenRequest);
            $this->tokenDao->saveOrUpdateToken($token);
        }
        return $token;

    }

    private function createNewToken(TokenRequest $tokenRequest): Token
    {
        $token = new Token($this->keyRegistry->getSignatureKey());
        $token->setSubject($tokenRequest->getUsername());
        $token->setUserGroup($tokenRequest->getGroup());
        $token->setLang($tokenRequest->getLang());
        $token->setShopid($tokenRequest->getShopid());
        $token->setShopUrl($this->environmentService->getShopUrl());

        return $token;
    }
}

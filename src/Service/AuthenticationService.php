<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Service;

use OxidEsales\GraphQl\Dao\UserDaoInterface;
use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\DataObject\TokenRequest;
use OxidEsales\GraphQl\Exception\PasswordMismatchException;
use OxidEsales\GraphQl\Utility\AuthConstants;

class AuthenticationService implements AuthenticationServiceInterface
{
    /** @var  EnvironmentServiceInterface $environmentService */
    private $environmentService;

    /** @var  UserDaoInterface $userDao */
    private $userDao;

    public function __construct(
        EnvironmentServiceInterface $environmentService,
        UserDaoInterface $userDao
    ) {
        $this->environmentService = $environmentService;
        $this->userDao = $userDao;
    }

    /**
     * @param TokenRequest $tokenRequest
     *
     * @return string
     */
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

        $tokenRequest = $this->userDao->addIdAndUserGroupToTokenRequest($tokenRequest);

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
}

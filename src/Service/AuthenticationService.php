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

class AuthenticationService implements AuthenticationServiceInterface
{
    /** @var  EnvironmentServiceInterface $environmentService */
    private $environmentService;

    /** @var  UserDaoInterface $userDao */
    private $userDao;

    public function __construct(
        EnvironmentServiceInterface $environmentService,
        UserDaoInterface $userDao
    )
    {
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
        if ($tokenRequest->getUserName()) {
            $token = $this->getUserToken($tokenRequest);
        }
        else {
            $token = $this->getAnonymousToken($tokenRequest);
        }
        return $token;
    }

    private function getAnonymousToken(TokenRequest $tokenRequest)
    {
        $tokenRequest->setUsername('anonymous');
        $tokenRequest->setGroup('anonymous');
        return $this->createToken($tokenRequest);
    }

    private function getUserToken(TokenRequest $tokenRequest)
    {
        if (! $tokenRequest->getPassword() || ! $tokenRequest->getShopid()) {
            throw new PasswordMismatchException();
        }

        $tokenRequest->setGroup($this->userDao->fetchUserGroup(
            $tokenRequest->getUsername(),
            $tokenRequest->getPassword(),
            $tokenRequest->getShopid()));

        return $this->createToken($tokenRequest);
    }

    private function createToken(TokenRequest $tokenRequest)
    {
        $token = new Token();
        $token->setSubject($tokenRequest->getUsername());
        $token->setUserGroup($tokenRequest->getGroup());
        $token->setLang($tokenRequest->getLang());
        $token->setShopid($tokenRequest->getShopid());
        $token->setShopUrl($this->environmentService->getShopUrl());

        return $token;

    }
}

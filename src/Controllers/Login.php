<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Controllers;

use OxidEsales\GraphQl\DataObject\TokenRequest;
use OxidEsales\GraphQl\Service\AuthenticationServiceInterface;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;
use TheCodingMachine\GraphQLite\Annotations\Query;
use OxidEsales\GraphQl\Framework\AppContext;

class Login
{
    /** @var AuthenticationServiceInterface */
    protected $authService;

    /** @var  KeyRegistryInterface */
    protected $keyRegistry;

    /** @var AppContext */
    protected $context;

    public function __construct(
        AuthenticationServiceInterface $authService,
        KeyRegistryInterface $keyRegistry,
        AppContext $context
    )
    {
        $this->authService = $authService;
        $this->keyRegistry = $keyRegistry;
        $this->context     = $context;
    }
 
    /**
     * @Query
     */
    public function login(string $username = '', string $password = '', string $lang = null, int $shopid = null): string
    {
        $tokenRequest = new TokenRequest();
        $tokenRequest->setUsername($username);
        $tokenRequest->setPassword($password);
        if ($lang !== null) {
            $tokenRequest->setLang($lang);
        } else {
            $tokenRequest->setLang($this->context->getDefaultShopLanguage());
        }
        if ($shopid !== null) {
            $tokenRequest->setShopid($shopid);
        } else {
            $tokenRequest->setShopid($this->context->getDefaultShopId());
        }
        if ($this->context->hasAuthToken()) {
            $tokenRequest->setCurrentToken($this->context->getAuthToken());
        }
        $token = $this->authService->getToken($tokenRequest);
        $signatureKey = $this->keyRegistry->getSignatureKey();
        return $token->getJwt($signatureKey);
    }

}

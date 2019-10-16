<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Controllers;

use OxidEsales\GraphQL\Framework\AppContext;
use OxidEsales\GraphQL\Service\AuthenticationServiceInterface;
use OxidEsales\GraphQL\Service\KeyRegistryInterface;
use TheCodingMachine\GraphQLite\Annotations\Query;

class Login
{
    /** @var AuthenticationServiceInterface */
    protected $authenticationService;

    /** @var  KeyRegistryInterface */
    protected $keyRegistry;

    /** @var AppContext */
    protected $context;

    public function __construct(
        AuthenticationServiceInterface $authenticationService,
        KeyRegistryInterface $keyRegistry,
        AppContext $context
    ) {
        $this->authenticationService = $authenticationService;
        $this->keyRegistry = $keyRegistry;
        $this->context     = $context;
    }
 
    /**
     * @Query
     */
    public function login(string $username, string $password, int $shopid = null): string
    {
        if ($shopid === null) {
            $shopid = $this->context->getCurrentShopId();
        }
        return (string) $this->authenticationService->createToken(
            $username,
            $password,
            $shopid
        );
    }
}

<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Controller;

use OxidEsales\GraphQL\Service\AuthenticationServiceInterface;
use OxidEsales\GraphQL\Service\EnvironmentServiceInterface;
use OxidEsales\GraphQL\Service\KeyRegistryInterface;
use TheCodingMachine\GraphQLite\Annotations\Query;

class Login
{
    /** @var AuthenticationServiceInterface */
    protected $authentication;

    /** @var  KeyRegistryInterface */
    protected $keyRegistry;

    /** @var EnvironmentServiceInterface */
    protected $environment;

    public function __construct(
        EnvironmentServiceInterface $environment,
        AuthenticationServiceInterface $authentication,
        KeyRegistryInterface $keyRegistry
    ) {
        $this->environment = $environment;
        $this->authentication= $authentication;
        $this->keyRegistry = $keyRegistry;
    }
 
    /**
     * @Query
     */
    public function token(string $username, string $password, int $shopid = null): string
    {
        if ($shopid === null) {
            $shopid = $this->environment->getShopId();
        }
        return (string) $this->authentication->createToken(
            $username,
            $password,
            $shopid
        );
    }
}

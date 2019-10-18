<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Controller;

use OxidEsales\GraphQL\Service\AuthenticationServiceInterface;
use OxidEsales\GraphQL\Service\EnvironmentServiceInterface;
use TheCodingMachine\GraphQLite\Annotations\Query;

class Login
{
    /** @var AuthenticationServiceInterface */
    protected $authentication;

    /** @var EnvironmentServiceInterface */
    protected $environment;

    public function __construct(
        EnvironmentServiceInterface $environment,
        AuthenticationServiceInterface $authentication
    ) {
        $this->environment = $environment;
        $this->authentication= $authentication;
    }
 
    /**
     * @Query
     */
    public function token(string $username, string $password): string
    {
        return (string) $this->authentication->createToken(
            $username,
            $password,
            $this->environment->getShopId()
        );
    }
}

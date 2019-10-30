<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Controller;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\GraphQL\Service\AuthenticationServiceInterface;
use TheCodingMachine\GraphQLite\Annotations\Query;

class Login
{
    /** @var AuthenticationServiceInterface */
    protected $authentication;

    public function __construct(
        AuthenticationServiceInterface $authentication
    ) {
        $this->authentication= $authentication;
    }
 
    /**
     * @Query
     */
    public function token(string $username, string $password): string
    {
        return (string) $this->authentication->createAuthenticatedToken(
            $username,
            $password
        );
    }
}

<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Controller;

use OxidEsales\GraphQL\Base\Service\Authentication;
use TheCodingMachine\GraphQLite\Annotations\Query;

class Login
{
    /** @var Authentication */
    protected $authentication;

    public function __construct(
        Authentication $authentication
    ) {
        $this->authentication = $authentication;
    }

    /**
     * retrieve a JWT for authentication of further requests
     *
     * @Query
     */
    public function token(string $username, string $password): string
    {
        return (string) $this->authentication->createToken(
            $username,
            $password
        );
    }
}

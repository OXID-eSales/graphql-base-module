<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Controller;

use OxidEsales\GraphQL\Base\Service\Authentication;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Right;
use TheCodingMachine\GraphQLite\Annotations\HideIfUnauthorized;
use TheCodingMachine\GraphQLite\Annotations\Security;
use TheCodingMachine\GraphQLite\Annotations\FailWith;
use TheCodingMachine\GraphQLite\Annotations\InjectUser;
use OxidEsales\GraphQL\Base\Annotation\Debug;

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
    public function token(?string $username = null, ?string $password = null): string
    {
        return (string) $this->authentication->createToken(
            $username,
            $password
        );
    }

    /**
     * Query visible in schema, accessible by anyone
     *
     * @Query
     */
    public function notLoggedIn(string $param): string
    {
        return 'Anonymous';
    }

    /**
     * Query visible in schema, accessible when logged in
     *
     * @Query
     * @Logged
     */
    public function loggedIn(string $param): string
    {
        return 'Logged';
    }

    /**
     * Query visible in schema, accessible when logged in and with correct right
     *
     * @Query
     * @Logged
     * @Right("VIEW_USER")
     */
    public function loggedWithRights(string $param): string
    {
        return 'Admin';
    }

    /**
     * Query will be hidden if not logged in and don't have correct rights
     *
     * @Query
     * @Logged
     * @Right("VIEW_USER")
     * @HideIfUnauthorized
     */
    public function hiddenQuery(): string
    {
        return 'hidden';
    }

    /**
     * Query visible in schema, accessible with correct right, will retunr custom message
     *
     * @Query
     * @Security("is_granted('VIEW_USER')", message="Custom error message")
     */
    public function secureQuery(): string
    {
        return 'secure';
    }

    /**
     * Query visible in schema, accessible when logged, user expression matching
     * will return null on fail.
     *
     * @Query
     * @Security("is_logged() && user.access == 'oxidadmin'", failWith=null)
     */
    public function nullQuery(): string
    {
        return 'secure null';
    }

    /**
     * Query contains custom annotation, outcome is whatever we decide.
     *
     * @Query
     * @Debug("VIEW_USER")
     */
    public function debugQuery(): string
    {
        return 'debug';
    }
}

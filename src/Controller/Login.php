<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Controller;

use OxidEsales\GraphQL\Base\Service\Authentication;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Right;
use TheCodingMachine\GraphQLite\Annotations\Security;

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
     * Accessible by anyone
     *
     * @Query()
     */
    public function notLoggedIn(): string
    {
        return __FUNCTION__;
    }

    /**
     * Accessible when logged in
     *
     * @Query()
     * @Logged()
     */
    public function logged(): string
    {
        return __FUNCTION__;
    }

    /**
     * Accessible when logged in and with certain rights
     *
     * @Query()
     * @Logged()
     * @Right("VIEW_USER")
     */
    public function loggedRights(): string
    {
        return __FUNCTION__;
    }

    /**
     * Accessible with certain rights
     *
     * @Query()
     * @Right("VIEW_USER")
     */
    public function rights(): string
    {
        return __FUNCTION__;
    }

    /**
     * Accessible with certain rights, will return custom message
     *
     * @Query()
     * @Security("is_granted('VIEW_USER')", message="Custom error message")
     */
    public function security(): string
    {
        return __FUNCTION__;
    }

    /**
     * Accessible when logged, user expression matching
     * will return null on fail.
     *
     * @Query
     * @Security("is_logged() && user.access == 'oxidadmin'", failWith=null)
     */
    public function securityWithCondition(): string
    {
        return __FUNCTION__;
    }
}

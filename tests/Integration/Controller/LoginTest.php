<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Controller;

use OxidEsales\GraphQL\Base\Tests\Integration\TestCase;

class LoginTest extends TestCase
{
    public function testLoginWithMissingCredentials()
    {
        $this->execQuery('query { token }');

        $this->assertEquals(
            400,
            static::$queryResult['status']
        );
    }

    public function testLoginWithWrongCredentials()
    {
        $this->execQuery('query { token (username: "foo", password: "bar") }');

        $this->assertEquals(
            401,
            static::$queryResult['status']
        );
    }

    public function testLoginWithValidCredentials()
    {
        $this->execQuery('query { token (username: "admin", password: "admin") }');

        $this->assertEquals(
            200,
            static::$queryResult['status']
        );
    }

    public function testLoginWithValidCredentialsInVariables()
    {
        $this->execQuery(
            'query ($username: String!, $password: String!) { token (username: $username, password: $password) }',
            [
                'username' => 'admin',
                'password' => 'admin'
            ]
        );

        $this->assertEquals(
            200,
            static::$queryResult['status']
        );
    }
}

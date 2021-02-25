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
    public function testLoginWithMissingCredentials(): void
    {
        $result = $this->query('query { token }');

        $this->assertNotEmpty($result['body']['errors']);
    }

    public function testLoginWithWrongCredentials(): void
    {
        $result = $this->query('query { token (username: "foo", password: "bar") }');

        $this->assertEquals(
            $result['body']['errors'][0]['message'],
            'Username/password combination is invalid'
        );
    }

    public function testLoginWithValidCredentials(): void
    {
        $result = $this->query('query { token (username: "admin", password: "admin") }');

        $this->assertNotEmpty($result['body']['data']['token']);
    }

    public function testLoginWithValidCredentialsInVariables(): void
    {
        $result = $this->query(
            'query ($username: String!, $password: String!) { token (username: $username, password: $password) }',
            [
                'username' => 'admin',
                'password' => 'admin',
            ]
        );

        $this->assertNotEmpty($result['body']['data']['token']);
    }
}

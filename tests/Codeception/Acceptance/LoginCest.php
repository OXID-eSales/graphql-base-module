<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Codeception\Acceptance;

use OxidEsales\GraphQL\Base\Tests\Codeception\AcceptanceTester;

/**
 * @group oe_graphql_base
 */
class LoginCest
{
    public function testLoginWithMissingCredentials(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query { token }'); // anonymous token
        $result = $I->grabJsonResponseAsArray();

        $I->assertNotEmpty($result['data']['token']);
    }

    public function testLoginWithIncompleteCredentialsPassword(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query { token (username: "foo") }'); // anonymous token
        $result = $I->grabJsonResponseAsArray();

        $I->assertNotEmpty($result['data']['token']);
    }

    public function testLoginWithIncompleteCredentialsUsername(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query { token (password: "foo") }'); // anonymous token
        $result = $I->grabJsonResponseAsArray();

        $I->assertNotEmpty($result['data']['token']);
    }

    public function testLoginWithWrongCredentials(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query { token (username: "foo", password: "bar") }');
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals('Username/password combination is invalid', $result['errors'][0]['message']);
    }

    public function testLoginWithValidCredentials(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query { token (username: "admin", password: "admin") }');
        $result = $I->grabJsonResponseAsArray();

        $I->assertNotEmpty($result['data']['token']);
    }

    public function testLoginWithValidCredentialsInVariables(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query ($username: String!, $password: String!) { token (username: $username, password: $password) }',
            [
                'username' => 'admin',
                'password' => 'admin',
            ]
        );
        $result = $I->grabJsonResponseAsArray();

        $I->assertNotEmpty($result['data']['token']);
    }
}

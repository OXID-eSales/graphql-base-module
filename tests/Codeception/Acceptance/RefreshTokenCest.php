<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Codeception\Acceptance;

use DateTimeImmutable;
use OxidEsales\GraphQL\Base\Tests\Codeception\AcceptanceTester;

/**
 * @group oe_graphql_base
 * @group oe_graphql_base_token
 */
class RefreshTokenCest
{
    private const TEST_USER_ID = 'e7af1c3b786fd02906ccd75698f4e6b9';

    private const ADMIN_LOGIN = 'admin@admin.com';

    private const ADMIN_PASSWORD = 'admin';

    private const USER_LOGIN = 'user@oxid-esales.com';

    public function testRefreshLoginWithMissingCredentials(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query { login
                {
                    accessToken
                    refreshToken
                }
            }'
        ); // anonymous token
        $result = $I->grabJsonResponseAsArray();

        $I->assertNotEmpty($result['data']['login']['accessToken']);
        $I->assertNotEmpty($result['data']['login']['refreshToken']);
    }

    public function testRefreshLoginWithIncompleteCredentialsPassword(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query { login (username: "foo")
                {
                    accessToken
                    refreshToken
                }
            }'
        ); // anonymous token
        $result = $I->grabJsonResponseAsArray();

        $I->assertNotEmpty($result['data']['login']['accessToken']);
        $I->assertNotEmpty($result['data']['login']['refreshToken']);
    }

    public function testRefreshLoginWithIncompleteCredentialsUsername(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query { login (password: "foo")
                {
                    accessToken
                    refreshToken
                }
            }'
        ); // anonymous token
        $result = $I->grabJsonResponseAsArray();

        $I->assertNotEmpty($result['data']['login']['accessToken']);
        $I->assertNotEmpty($result['data']['login']['refreshToken']);
    }

    public function testRefreshLoginWithWrongCredentials(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query { login (username: "foo", password: "bar")
                {
                    accessToken
                    refreshToken
                }
            }'
        );
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals('Username/password combination is invalid', $result['errors'][0]['message']);
    }

    public function testRefreshLoginWithValidCredentials(AcceptanceTester $I): void
    {
        $query = 'query {
            login (username: "' . self::ADMIN_LOGIN . '", password: "' . self::ADMIN_PASSWORD . '") 
            {
                accessToken
                refreshToken
            }
        }';
        $I->sendGQLQuery($query);
        $result = $I->grabJsonResponseAsArray();

        $I->assertNotEmpty($result['data']['login']['accessToken']);
        $I->assertNotEmpty($result['data']['login']['refreshToken']);
    }

    public function testRefreshLoginWithValidCredentialsInVariables(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query ($username: String!, $password: String!) { login (username: $username, password: $password) 
                {
                    accessToken
                    refreshToken
                }
            }',
            [
                'username' => self::ADMIN_LOGIN,
                'password' => self::ADMIN_PASSWORD,
            ]
        );
        $result = $I->grabJsonResponseAsArray();

        $I->assertNotEmpty($result['data']['login']['accessToken']);
        $I->assertNotEmpty($result['data']['login']['refreshToken']);
    }

    public function testRefreshAccessToken(AcceptanceTester $I): void
    {
        $query = 'query {
            login (username: "' . self::ADMIN_LOGIN . '", password: "' . self::ADMIN_PASSWORD . '") 
            {
                accessToken
                refreshToken
            }
        }';

        $I->sendGQLQuery($query);
        $result = $I->grabJsonResponseAsArray();
        $refreshToken = $result['data']['login']['refreshToken'];

        $refreshQuery = 'query {
            refresh (refreshToken: "' . $refreshToken . '", fingerprint: "")
        }';

        $I->sendGQLQuery($refreshQuery);
        $result = $I->grabJsonResponseAsArray();

        $I->assertNotEmpty($result['data']['refresh']);
    }
}

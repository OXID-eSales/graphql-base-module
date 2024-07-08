<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Codeception\Acceptance;

use Codeception\Attribute\Group;
use OxidEsales\GraphQL\Base\Tests\Codeception\AcceptanceTester;

#[Group("oe_graphql_base")]
#[Group("oe_graphql_base_token")]
class RefreshTokenCest
{
    private const ADMIN_LOGIN = 'admin@admin.com';
    private const ADMIN_PASSWORD = 'admin';

    public function testRefreshAccessToken(AcceptanceTester $I): void
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

        //todo: add check if a fingerprint hash claim is in the access token
        //todo: add check if fingerprint is in the cookie
        //todo: check if result jwt is NOT anonymous!

        $refreshToken = $result['data']['login']['refreshToken'];

        $I->sendGQLQuery(
            'query ($refreshToken: String!) {refresh (refreshToken: $refreshToken, fingerprint: "")}',
            [
                'refreshToken' => $refreshToken,
            ]
        );
        $result = $I->grabJsonResponseAsArray();

        $I->assertNotEmpty($result['data']['refresh']);

        //todo: check if result jwt is NOT anonymous!
        //todo: check if fingerprint hash claim changed in access token
        //todo: check if fingerprint changed in the cookie
    }
}

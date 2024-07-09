<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Codeception\Acceptance;

use Codeception\Attribute\Group;
use OxidEsales\GraphQL\Base\Service\FingerprintService;
use OxidEsales\GraphQL\Base\Service\Token;
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

        $accessToken = $I->parseJwt($result['data']['login']['accessToken']);
        $fingerprintHash = $accessToken->claims()->get(FingerprintService::TOKEN_KEY);
        $cookie = $I->grabCookies()->get(FingerprintService::COOKIE_KEY)->getRawValue();

        $I->assertEquals(self::ADMIN_LOGIN, $accessToken->claims()->get(Token::CLAIM_USERNAME));
        $I->assertNotEmpty($fingerprintHash);
        $I->assertEquals(128, strlen($cookie));
        $I->assertFalse($accessToken->claims()->get(Token::CLAIM_USER_ANONYMOUS));

        $refreshToken = $result['data']['login']['refreshToken'];

        $I->sendGQLQuery(
            'query ($refreshToken: String!, $fingerprintHash: String!) {
                refresh (refreshToken: $refreshToken, fingerprintHash: $fingerprintHash)
            }',
            [
                'refreshToken' => $refreshToken,
                'fingerprintHash' => $fingerprintHash
            ]
        );
        $result = $I->grabJsonResponseAsArray();

        $I->assertNotEmpty($result['data']['refresh']);

        $accessToken = $I->parseJwt($result['data']['refresh']);
        $newFingerprint = $accessToken->claims()->get(FingerprintService::TOKEN_KEY);
        $newCookie = $I->grabCookies()->get(FingerprintService::COOKIE_KEY)->getRawValue();

        $I->assertFalse($accessToken->claims()->get(Token::CLAIM_USER_ANONYMOUS));

        $I->assertNotEquals($fingerprintHash, $newFingerprint);

        $I->assertNotEquals($cookie, $newCookie);
    }
}

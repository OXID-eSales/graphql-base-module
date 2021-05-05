<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Codeception\Acceptance;

use OxidEsales\GraphQL\Base\Component\Widget\GraphQL;
use OxidEsales\GraphQL\Base\Tests\Codeception\AcceptanceTester;

/**
 * @group oe_graphql_base
 */
class GraphQLCest
{
    public function testLoginWithInvalidCredentials(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query {token(username:"wrong", password:"wrong")}');
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"category":"permissionerror"}');
        $I->canSeeHttpHeader('Server-Timing');
        $I->seeResponseContains('errors');

        $result = $I->grabJsonResponseAsArray();
        $I->assertEquals('Username/password combination is invalid', $result['errors'][0]['message']);
    }

    public function testLoginWithValidCredentials(AcceptanceTester $I): void
    {
        $I->login('user@oxid-esales.com', 'useruser');
        $I->canSeeHttpHeader('Server-Timing');
    }

    public function testQueryWithInvalidToken(AcceptanceTester $I): void
    {
        $I->amBearerAuthenticated('invalid_token');
        $I->sendGQLQuery('query {token(username:"admin", password:"admin")}');
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseMatchesJsonType([
            'errors' => [
                [
                    'message'    => 'string:=Unable to parse token',
                    'extensions' => [
                        'category' => 'string:=permissionerror',
                    ],
                ],
            ],
        ]);
        $I->canSeeHttpHeader('WWW-Authenticate', 'Bearer');
        $I->cantSeeHttpHeader('Server-Timing');
    }

    public function testQueryWithoutSkipSession(AcceptanceTester $I): void
    {
        $uri = '/widget.php?cl=graphql&lang=0&shp=1';

        $I->getRest()->haveHTTPHeader('Content-Type', 'application/json');
        $I->getRest()->sendPOST($uri, [
            'query'     => 'query {token(username:"admin", password:"admin")}',
            'variables' => [],
        ]);

        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
        $I->seeResponseMatchesJsonType([
            'errors' => [
                [
                    'message'    => 'string:=' . GraphQL::SESSION_ERROR_MESSAGE,
                    'extensions' => [
                        'category' => 'string:=requesterror',
                    ],
                ],
            ],
        ]);
        $I->cantSeeHttpHeader('Server-Timing');
    }

    public function testLoginAnonymousToken(AcceptanceTester $I): void
    {
        $I->login(null, null);
        $I->canSeeHttpHeader('Server-Timing');
    }
}

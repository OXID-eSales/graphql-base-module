<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Codeception\Acceptance;

use OxidEsales\GraphQL\Base\Tests\Codeception\AcceptanceTester;

class GraphQLCest
{
    public function testLoginWithInvalidCredentials(AcceptanceTester $I): void
    {
        $I->haveHTTPHeader('Content-Type', 'application/json');
        $I->sendPOST('/widget.php?cl=graphql', [
            'query' => 'query {token(username:"wrong", password:"wrong")}',
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('{"category":"permissionerror"}');
    }

    public function testLoginWithValidCredentials(AcceptanceTester $I): void
    {
        $I->haveHTTPHeader('Content-Type', 'application/json');
        $I->sendPOST('/widget.php?cl=graphql', [
            'query' => 'query {token(username:"admin", password:"admin")}',
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('data');
        $I->seeResponseContains('token');
    }

    public function testQueryWithInvalidToken(AcceptanceTester $I): void
    {
        $I->haveHTTPHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated('invalid_token');
        $I->sendPOST('/widget.php?cl=graphql', [
            'query' => 'query {token(username:"admin", password:"admin")}',
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
        $I->seeResponseContains('errors');
    }
}

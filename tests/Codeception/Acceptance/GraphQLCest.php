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
        $I->canSeeHttpHeader('WWW-Authenticate', 'Bearer');
    }

    public function testLoginAndQuery(AcceptanceTester $I): void
    {
        $I->haveHTTPHeader('Content-Type', 'application/json');
        $I->sendPOST('/widget.php?cl=graphql', [
            'query' => 'query {token(username:"admin", password:"admin")}',
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('data');
        $I->seeResponseContains('token');

//        $token = $I->grabDataFromResponseByJsonPath('$.data.token');
//
//        $I->amBearerAuthenticated($token[0]);
        $I->sendPOST('/widget.php?cl=graphql', [
            'query' => 'query{
              product(id: "09602cddb5af0aba745293d08ae6bcf6"){
                id
                active
              }
            }',
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('data');
        $I->seeResponseContains('"active":false');
    }
}

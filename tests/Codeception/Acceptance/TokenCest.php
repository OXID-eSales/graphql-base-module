<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Codeception\Acceptance;

use DateTimeImmutable;
use OxidEsales\Facts\Facts;
use OxidEsales\GraphQL\Base\Tests\Codeception\AcceptanceTester;

/**
 * @group oe_graphql_base
 * @group oe_graphql_base_token
 */
class TokenCest
{
    private const TEST_USER_ID = 'e7af1c3b786fd02906ccd75698f4e6b9';

    public function _after(AcceptanceTester $I): void
    {
        $I->logout();
        //TODO: clean up oegraphqltoken table via query
    }

    public function testCannotQueryTokensWithoutToken(AcceptanceTester $I): void
    {
        $I->wantToTest('cannot query tokens without token');

        $result = $this->sendTokenQuery($I);

        $I->assertStringStartsWith('You need to be logged to access this field', $result['errors'][0]['message']);
    }

    public function testCannotQueryTokensWithAnonymousToken(AcceptanceTester $I): void
    {
        $I->wantToTest('cannot query tokens with anonymous token');

        $I->sendGQLQuery('query { token }'); // anonymous token
        $result = $I->grabJsonResponseAsArray();
        $I->amBearerAuthenticated($result['data']['token']);

        $result = $this->sendTokenQuery($I);

        $I->assertStringStartsWith('You need to be logged to access this field', $result['errors'][0]['message']);
    }

    public function testQueryTokensWithUserToken(AcceptanceTester $I): void
    {
        $I->wantToTest('standard customer can only query own tokens');

        $token = $this->generateUserTokens($I, false);
        $I->amBearerAuthenticated($token);

        $result           = $this->sendTokenQuery($I);
        $tokenCountBefore = count($result['data']['tokens']);

        $token = $this->generateUserTokens($I, false);
        $I->amBearerAuthenticated($token);

        $result          = $this->sendTokenQuery($I);
        $tokenCountAfter = count($result['data']['tokens']);

        //we see three more user tokens
        $I->assertEquals($tokenCountBefore + 3, $tokenCountAfter);
    }

    public function testQueryTokensWithAdminToken(AcceptanceTester $I): void
    {
        $I->wantToTest('special rights user will get only own tokens without filter');

        $token = $this->generateUserTokens($I, true);
        $I->amBearerAuthenticated($token);

        $result           = $this->sendTokenQuery($I);
        $tokenCountBefore = count($result['data']['tokens']);

        $token = $this->generateUserTokens($I);
        $I->amBearerAuthenticated($token);

        $result          = $this->sendTokenQuery($I);
        $tokenCountAfter = count($result['data']['tokens']);

        //we see two more user tokens because without explicit filter userid filter will be added by default
        $I->assertEquals($tokenCountBefore + 2, $tokenCountAfter);
        $I->assertNotEquals(self::TEST_USER_ID, $result['data']['tokens'][0]['customerId']); //admin user
    }

    public function testQueryTokensWithAdminTokenAndUserFilterOnNotExistingUserId(AcceptanceTester $I): void
    {
        $I->wantToTest('special rights user will get no tokens for filter on not existing user id');

        $filterPart = '( filter: {
                           customerId: {
                               equals: "not_existing"
                          }
                        })';

        $token = $this->generateUserTokens($I);
        $I->amBearerAuthenticated($token);
        $result = $this->sendTokenQuery($I, $filterPart);

        $I->assertEmpty($result['data']['tokens']);
    }

    public function testQueryTokensWithUserTokenAndUserFilterOnNotOwnId(AcceptanceTester $I): void
    {
        $I->wantToTest('normal user with filter on other user id');

        $filterPart = '( filter: {
                           customerId: {
                               equals: "not_existing_id"
                          }
                        })';

        $token = $this->generateUserTokens($I, false);
        $I->amBearerAuthenticated($token);

        $result = $this->sendTokenQuery($I, $filterPart);

        $I->assertStringStartsWith('Unauthorized', $result['errors'][0]['message']);
    }

    public function testQueryTokensWithUserTokenAndUserFilterOnOwnId(AcceptanceTester $I): void
    {
        $I->wantToTest('normal user with filter on own user id');

        $filterPart = '( filter: {
                           customerId: {
                               equals: "' . self::TEST_USER_ID . '"
                          }
                        })';

        $token = $this->generateUserTokens($I, false);
        $I->amBearerAuthenticated($token);

        $result           = $this->sendTokenQuery($I, $filterPart);
        $tokenCountBefore = count($result['data']['tokens']);

        $token = $this->generateUserTokens($I);
        $I->amBearerAuthenticated($token);

        $result          = $this->sendTokenQuery($I, $filterPart);
        $tokenCountAfter = count($result['data']['tokens']);

        //we see three more user tokens for this customer
        $I->assertEquals(self::TEST_USER_ID, $result['data']['tokens'][0]['customerId']);
        $I->assertEquals($tokenCountBefore + 3, $tokenCountAfter);
    }

    public function testQueryTokensWithAdminTokenAndUserFilter(AcceptanceTester $I): void
    {
        $I->wantToTest('special rights will get other user tokens with filter');

        $filterPart = '( filter: {
                           customerId: {
                               equals: "' . self::TEST_USER_ID . '"
                          }
                        })';

        $token = $this->generateUserTokens($I);
        $I->amBearerAuthenticated($token);

        $result           = $this->sendTokenQuery($I, $filterPart);
        $tokenCountBefore = count($result['data']['tokens']);

        $token = $this->generateUserTokens($I);
        $I->amBearerAuthenticated($token);

        $result          = $this->sendTokenQuery($I, $filterPart);
        $tokenCountAfter = count($result['data']['tokens']);

        //we see three more user tokens because of userid filter
        $I->assertEquals(self::TEST_USER_ID, $result['data']['tokens'][0]['customerId']);
        $I->assertEquals($tokenCountBefore + 3, $tokenCountAfter);
    }

    public function testQueryTokensWithSorting(AcceptanceTester $I): void
    {
        $I->wantToTest('tokens query with sorting');

        $token = $this->generateUserTokens($I, false, true);
        $I->amBearerAuthenticated($token);

        $resultDESC = $this->sendTokenQuery($I, '(sort:{expiresAt: "DESC"})');
        $resultASC  = $this->sendTokenQuery($I, '(sort:{expiresAt: "ASC"})');

        $I->assertEquals(count($resultASC['data']['tokens']), count($resultDESC['data']['tokens']));
        $I->assertNotEquals($resultDESC['data']['tokens'], $resultASC['data']['tokens']);
        $I->assertLessThan($resultDESC['data']['tokens'][0]['expiresAt'], $resultASC['data']['tokens'][0]['expiresAt']);
    }

    public function testQueryTokensWithPagination(AcceptanceTester $I): void
    {
        $I->wantToTest('tokens query with pagination');

        $this->generateUserTokens($I);
        $token = $this->generateUserTokens($I);
        $I->amBearerAuthenticated($token);

        $resultFirst   = $this->sendTokenQuery($I, "(pagination:{offset: 0 \n limit: 3})");
        $resultSecond  = $this->sendTokenQuery($I, "(pagination:{offset: 1 \n limit: 3})");

        $I->assertEquals(count($resultFirst['data']['tokens']), count($resultSecond['data']['tokens']));
        $I->assertNotEquals($resultFirst['data']['tokens'], $resultSecond['data']['tokens']);
        $I->assertEquals($resultFirst['data']['tokens'][1]['id'], $resultSecond['data']['tokens'][0]['id']);
    }

    public function testQueryTokensWithShopIdFilter(AcceptanceTester $I): void
    {
        $I->wantToTest('tokens query with shopid filter');

        $token = $this->generateUserTokens($I);
        $I->amBearerAuthenticated($token);

        $result = $this->sendTokenQuery($I, '(filter:{shopId:{equals: "1"}})');
        $I->assertNotEmpty($result['data']['tokens']);

        $result = $this->sendTokenQuery($I, '(filter:{shopId:{equals: "666"}})');
        $I->assertEmpty($result['data']['tokens']);
    }

    public function testQueryTokensWithDateFilter(AcceptanceTester $I): void
    {
        $I->wantToTest('tokens query with date filter');

        $token = $this->generateUserTokens($I, false);
        $I->amBearerAuthenticated($token);

        $result = $this->sendTokenQuery($I, '(filter:{expiresAt:{between: ["2020-12-01 12:12:12", "2021-12-01 12:12:12"]}})');
        $I->assertEmpty($result['data']['tokens']);

        $filterPart = '(filter:{expiresAt:{between: ["2020-12-01 12:12:12", "' .
            (new DateTimeImmutable('+48 hours'))->format('Y-m-d H:i:s') . '"]}})';
        $result = $this->sendTokenQuery($I, $filterPart);
        $I->assertNotEmpty($result['data']['tokens']);
    }

    private function sendTokenQuery(AcceptanceTester $I, string $filterPart = ''): array
    {
        $query = ' query {
               tokens ' . $filterPart . ' {
                 id
                 customerId
                 expiresAt
                 shopId
              }
            }
        ';

        $I->sendGQLQuery($query);

        return $I->grabJsonResponseAsArray();
    }

    private function generateUserTokens(AcceptanceTester $I, bool $adminToken = true, bool $delay = false): string
    {
        $I->logout();

        //four anonymous
        $I->sendGQLQuery('query { token }');
        $I->sendGQLQuery('query { token }');
        $I->sendGQLQuery('query { token }');
        $I->sendGQLQuery('query { token }');

        //two for admin
        $I->sendGQLQuery('query { token (username: "admin", password: "admin") }');
        $I->sendGQLQuery('query { token (username: "admin", password: "admin") }');

        $token = $I->grabJsonResponseAsArray()['data']['token'];

        //three for demo user
        $password = 'user';
        $facts    = new Facts();

        if ($facts->isEnterprise()) {
            $password = 'useruser';
        }
        $I->sendGQLQuery('query { token (username: "user@oxid-esales.com", password: "' . $password . '") }');
        $I->sendGQLQuery('query { token (username: "user@oxid-esales.com", password: "' . $password . '") }');
        !$delay ?? $I->wait(1);
        $I->sendGQLQuery('query { token (username: "user@oxid-esales.com", password: "' . $password . '") }');
        $I->logout();

        return $adminToken ? $token : $I->grabJsonResponseAsArray()['data']['token'];
    }
}

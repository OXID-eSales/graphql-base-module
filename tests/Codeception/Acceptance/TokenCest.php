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

    private const ADMIN_LOGIN = 'admin';

    private const ADMIN_PASSWORD = 'admin';

    private const USER_LOGIN = 'user@oxid-esales.com';

    public function _after(AcceptanceTester $I): void
    {
        $this->adminDeletesAllUserTokens($I, self::TEST_USER_ID);
        $this->adminDeletesAllUserTokens($I);

        $I->logout();
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

        $this->generateUserTokens($I, false, true);
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

    public function testCustomerTokensDeleteWithoutToken(AcceptanceTester $I): void
    {
        $I->wantToTest('calling customerTokenDelete without token');

        $result = $this->sendCustomerTokenDeleteMutation($I);

        $I->assertStringStartsWith('You need to be logged to access this field', $result['errors'][0]['message']);
    }

    public function testCustomerTokensDeleteWithAnonymousToken(AcceptanceTester $I): void
    {
        $I->wantToTest('calling customerTokenDelete with anonymous token');

        $I->sendGQLQuery('query { token }');
        $token = $I->grabJsonResponseAsArray()['data']['token'];
        $I->amBearerAuthenticated($token);

        $result = $this->sendCustomerTokenDeleteMutation($I);

        $I->assertStringStartsWith('You need to be logged to access this field', $result['errors'][0]['message']);
    }

    public function testCustomerTokensDeleteDefault(AcceptanceTester $I): void
    {
        $I->wantToTest('calling customerTokenDelete as normal user without customer id');

        $token = $this->generateUserTokens($I, false);
        $I->amBearerAuthenticated($token);

        $result = $this->sendCustomerTokenDeleteMutation($I);

        $I->assertEquals(3, $result['data']['customerTokensDelete']);
    }

    public function testCustomerTokensDeleteOwnId(AcceptanceTester $I): void
    {
        $I->wantToTest('calling customerTokenDelete as normal user with own id');

        $token = $this->generateUserTokens($I, false);
        $I->amBearerAuthenticated($token);

        $result = $this->sendCustomerTokenDeleteMutation($I, self::TEST_USER_ID);

        $I->assertEquals(3, $result['data']['customerTokensDelete']);
    }

    public function testCustomerTokensDeleteOtherUserFails(AcceptanceTester $I): void
    {
        $I->wantToTest('calling customerTokenDelete as normal user with other customer id');

        $token = $this->generateUserTokens($I, false);
        $I->amBearerAuthenticated($token);

        $result = $this->sendCustomerTokenDeleteMutation($I, '_other_user');

        $I->assertStringStartsWith('Unauthorized', $result['errors'][0]['message']);
    }

    public function testCustomerTokensDeleteOtherUserAdmin(AcceptanceTester $I): void
    {
        $I->wantToTest('calling customerTokenDelete as special rights user with other customer id');

        $token = $this->generateUserTokens($I);
        $I->amBearerAuthenticated($token);

        $result = $this->sendCustomerTokenDeleteMutation($I, self::TEST_USER_ID);

        $I->assertEquals(3, $result['data']['customerTokensDelete']);
    }

    public function testCustomerTokensDeleteAdminDeletesOwnTokens(AcceptanceTester $I): void
    {
        $I->wantToTest('calling customerTokenDelete as special rights user without customer id');

        $token = $this->generateUserTokens($I);
        $I->amBearerAuthenticated($token);

        $result = $this->sendCustomerTokenDeleteMutation($I);

        $I->assertEquals(2, $result['data']['customerTokensDelete']);
    }

    public function testCustomerTokensDeleteNotExistingOtherUserAdmin(AcceptanceTester $I): void
    {
        $I->wantToTest('calling customerTokenDelete as special rights user for not existing customer');

        $token = $this->generateUserTokens($I);
        $I->amBearerAuthenticated($token);

        $result = $this->sendCustomerTokenDeleteMutation($I, 'unknown_user');

        $I->assertStringStartsWith('User was not found by id:', $result['errors'][0]['message']);
    }

    public function testNotLoggedUserCannotDeleteToken(AcceptanceTester $I): void
    {
        $response = $this->sendTokenDeleteMutation($I, 'not_existing_token');
        $I->assertEquals('You need to be logged to access this field', $response['errors'][0]['message']);
    }

    public function testAnonymousUserCannotDeleteToken(AcceptanceTester $I): void
    {
        $I->login();
        $response = $this->sendTokenDeleteMutation($I, 'not_existing_token');
        $I->assertEquals('You need to be logged to access this field', $response['errors'][0]['message']);
    }

    public function testShopAdminCannotDeleteNotExistingToken(AcceptanceTester $I): void
    {
        $I->login(self::ADMIN_LOGIN, self::ADMIN_PASSWORD);
        $response = $this->sendTokenDeleteMutation($I, 'not_existing_token');
        $I->assertEquals('The token is not registered', $response['errors'][0]['message']);
    }

    public function testAdminCanDeleteToken(AcceptanceTester $I): void
    {
        $token = $this->generateUserTokens($I);
        $I->amBearerAuthenticated($token);

        $filterPart = '( filter: {
                           customerId: {
                               equals: "' . self::TEST_USER_ID . '"
                          }
                        })';

        // Get one of user tokens
        $response = $this->sendTokenQuery($I, $filterPart);
        $tokenId  = $response['data']['tokens'][0]['id'];

        // Delete it
        $response = $this->sendTokenDeleteMutation($I, $tokenId);
        $I->assertTrue($response['data']['tokenDelete']);

        // It's not there anymore
        $response = $this->sendTokenQuery($I, $filterPart);
        $ids      = array_map(function ($tokenRow) {
            return $tokenRow['id'];
        }, $response['data']['tokens']);
        $I->assertNotContains($tokenId, $ids);
    }

    public function testUserCannotDeleteNotExistingToken(AcceptanceTester $I): void
    {
        $I->login(self::USER_LOGIN, $this->getUserPassword());
        $response = $this->sendTokenDeleteMutation($I, 'not_existing_token');
        $I->assertEquals('The token is not registered', $response['errors'][0]['message']);
    }

    public function testUserCanDeleteHisToken(AcceptanceTester $I): void
    {
        // Generate two tokens
        $I->login(self::USER_LOGIN, $this->getUserPassword());
        sleep(1);
        $I->login(self::USER_LOGIN, $this->getUserPassword());

        $response = $this->sendTokenQuery($I, '(sort:{expiresAt: "ASC"})');
        $tokenId  = $response['data']['tokens'][0]['id'];

        // Delete the older one
        $response = $this->sendTokenDeleteMutation($I, $tokenId);
        $I->assertTrue($response['data']['tokenDelete']);

        $response = $this->sendTokenQuery($I);
        $I->assertNotEquals($tokenId, $response['data']['tokens'][0]['id']);
    }

    public function testUserCannotDeleteNotHisToken(AcceptanceTester $I): void
    {
        $I->login(self::ADMIN_LOGIN, self::ADMIN_PASSWORD);
        $response = $this->sendTokenQuery($I);
        $tokenId  = $response['data']['tokens'][0]['id'];

        $I->login(self::USER_LOGIN, $this->getUserPassword());
        $response = $this->sendTokenDeleteMutation($I, $tokenId);
        $I->assertEquals('The token is not registered', $response['errors'][0]['message']);
    }

    public function testShopTokensDeleteWithoutToken(AcceptanceTester $I): void
    {
        $I->wantToTest('calling shopTokenDelete without token');

        $result = $this->sendShopTokensDeleteMutation($I);

        $I->assertStringStartsWith('You need to be logged to access this field', $result['errors'][0]['message']);
    }

    public function testShopTokensDeleteWithAnonymousToken(AcceptanceTester $I): void
    {
        $I->wantToTest('calling shopTokenDelete with anonymous token');

        $I->sendGQLQuery('query { token }');
        $token = $I->grabJsonResponseAsArray()['data']['token'];
        $I->amBearerAuthenticated($token);

        $result = $this->sendShopTokensDeleteMutation($I);

        $I->assertStringStartsWith('You need to be logged to access this field', $result['errors'][0]['message']);
    }

    public function testShopTokensDeleteWithUserToken(AcceptanceTester $I): void
    {
        $I->wantToTest('calling shopTokenDelete with user token');

        $token = $this->generateUserTokens($I, false);
        $I->amBearerAuthenticated($token);

        $result = $this->sendShopTokensDeleteMutation($I);

        $I->assertStringStartsWith('You do not have sufficient rights to access this field', $result['errors'][0]['message']);
    }

    public function testShopTokensDeleteWithAdminToken(AcceptanceTester $I): void
    {
        $I->wantToTest('calling shopTokenDelete with special rights token');

        $token = $this->generateUserTokens($I, );
        $I->amBearerAuthenticated($token);

        $result = $this->sendShopTokensDeleteMutation($I);

        $I->assertEquals(5, $result['data']['shopTokensDelete']);
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

    private function sendCustomerTokenDeleteMutation(AcceptanceTester $I, ?string $userId = null): array
    {
        $query = ' mutation {
               customerTokensDelete ';
        !$userId ?: $query .= '(customerId: "' . $userId . '")';
        $query .= '}';

        $I->sendGQLQuery($query);

        return $I->grabJsonResponseAsArray();
    }

    private function sendShopTokensDeleteMutation(AcceptanceTester $I): array
    {
        $query = ' mutation {
                       shopTokensDelete
                   }';

        $I->sendGQLQuery($query);

        return $I->grabJsonResponseAsArray();
    }

    private function adminDeletesAllUserTokens(AcceptanceTester $I, ?string $userId = null): array
    {
        $I->logout();

        $I->sendGQLQuery('query { token (username: "admin", password: "admin") }');
        $token = $I->grabJsonResponseAsArray()['data']['token'];
        $I->amBearerAuthenticated($token);

        $this->sendCustomerTokenDeleteMutation($I, $userId);

        return $I->grabJsonResponseAsArray();
    }

    private function generateUserTokens(AcceptanceTester $I, bool $adminToken = true, bool $delay = false): string
    {
        $I->logout();

        //four anonymous
        $this->generateToken($I);
        $this->generateToken($I);
        $this->generateToken($I);
        $this->generateToken($I);

        //two for admin
        $this->generateToken($I, self::ADMIN_LOGIN, self::ADMIN_PASSWORD);
        $token = $this->generateToken($I, self::ADMIN_LOGIN, self::ADMIN_PASSWORD);

        //three for demo user
        $this->generateToken($I, self::USER_LOGIN, $this->getUserPassword());
        $this->generateToken($I, self::USER_LOGIN, $this->getUserPassword());
        !$delay ?? $I->wait(1);
        $userToken = $this->generateToken($I, self::USER_LOGIN, $this->getUserPassword());
        $I->logout();

        return $adminToken ? $token : $userToken;
    }

    private function sendTokenDeleteMutation(AcceptanceTester $I, string $tokenId): array
    {
        $mutation = 'mutation ($tokenId: ID!) {
            tokenDelete(tokenId: $tokenId)
        }';

        $I->sendGQLQuery($mutation, ['tokenId' => $tokenId]);

        return $I->grabJsonResponseAsArray();
    }

    private function generateToken(AcceptanceTester $I, $username = null, $password = null): string
    {
        $query = 'query ($username: String, $password: String) {
            token (username: $username, password: $password)
        }';

        $I->sendGQLQuery($query, [
            'username' => $username,
            'password' => $password,
        ]);

        return $I->grabJsonResponseAsArray()['data']['token'];
    }

    private function getUserPassword(): string
    {
        $facts = new Facts();

        return $facts->isEnterprise() ? 'useruser' : 'user';
    }
}

<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Acceptance;

use OxidEsales\EshopCommunity\Internal\Authentication\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQl\Dao\UserDaoInterface;
use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\DataObject\User;
use OxidEsales\GraphQl\Exception\NoAuthHeaderException;
use OxidEsales\GraphQl\Exception\ObjectNotFoundException;
use OxidEsales\GraphQl\Framework\GraphQlQueryHandlerInterface;
use OxidEsales\GraphQl\Utility\AuthConstants;

/**
 * Class LoginAcceptanceTest
 *
 * @package OxidEsales\GraphQl\Tests\Acceptance
 */
class LoginAcceptanceTest extends BaseGraphQlAcceptanceTestCase
{

    /**
     * Testing login without username and password
     */
    public function testAnonymousLogin()
    {
        $query = "query LoginTest {login {token} }";

        $this->executeQuery($query);

        $this->assertEquals(200, $this->httpStatus);
        $token = new Token();
        $token->setJwt($this->queryResult['data']['login']['token'], $this->signatureKey);
        $this->assertEquals('anonymous', $token->getUserGroup());
        print($this->logResult);
        $this->assertTrue(0 == strlen($this->logResult));
    }

    /**
     * Testing with existing username and correct password
     */
    public function testUserLogin()
    {
        $containerFactory = new TestContainerFactory();
        $container = $containerFactory->create();
        $container->compile();
        $userDao = $container->get(UserDaoInterface::class);
        /** @var PasswordServiceBridgeInterface $passwordService */
        $passwordService = $container->get(PasswordServiceBridgeInterface::class);

        /** User $testUser */
        try {
            $testUser = $userDao->getUserByName('test', 1);
        }
        catch (ObjectNotFoundException $e) {
            $testUser = new User();
        }
        $testUser->setUsername('test');
        $testUser->setPasswordhash($passwordService->hash('test'));
        $testUser->setUsergroup(AuthConstants::USER_GROUP_CUSTOMER);
        $testUser->setShopid(1);
        $userDao->saveOrUpdateUser($testUser);

        $query = 'query LoginTest {login (username: "test", password: "test") {token} }';

        $this->executeQueryWithoutAuthHeader($query);

        $this->assertEquals(200, $this->httpStatus);
        $token = new Token();
        $token->setJwt($this->queryResult['data']['login']['token'], $this->signatureKey);
        $this->assertEquals(AuthConstants::USER_GROUP_CUSTOMER, $token->getUserGroup());
        $this->assertTrue(0 == strlen($this->logResult));
    }

    /**
     * Testing with existing username and wrong password
     */
    public function testWrongPassword()
    {
        $query = 'query LoginTest {login (username: "admin", password: "wrong") {token} }';

        $this->executeQueryWithoutAuthHeader($query);

        $this->assertEquals(401, $this->httpStatus);
        $this->assertErrorMessage('User/password combination is not valid.');
        $this->assertTrue(0 === strpos($this->logResult, 'User/password combination is not valid.'));
    }

    /** Change the language in the token */
    public function testChangeLanguage()
    {
        $query = 'query LoginTest {setlanguage (lang: "fr") {token} }';

        $token = $this->createToken('anonymous');
        $token->setLang('de');

        $this->executeQueryWithToken($query, $token);

        $this->assertEquals(200, $this->httpStatus);
        $newToken = new Token();
        $newToken->setJwt($this->queryResult['data']['setlanguage']['token'], $this->signatureKey);
        $this->assertEquals('fr', $newToken->getLang());
    }

    /**
     * @param string $query
     */
    public function executeQueryWithoutAuthHeader(string $query)
    {
        $this->requestReader->method('getGraphQLRequestData')->willReturn(['query' => $query]);
        $this->requestReader->method('getAuthTokenString')
            ->willThrowException(new NoAuthHeaderException());

        $queryHandler = $this->container->get(GraphQlQueryHandlerInterface::class);
        $queryHandler->executeGraphQlQuery();
    }
}

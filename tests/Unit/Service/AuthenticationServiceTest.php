<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Unit\Service;

use Firebase\JWT\JWT;
use OxidEsales\GraphQl\Dao\UserDaoInterface;
use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\DataObject\TokenRequest;
use OxidEsales\GraphQl\Exception\InsufficientData;
use OxidEsales\GraphQl\Exception\PasswordMismatchException;
use OxidEsales\GraphQl\Service\AuthenticationService;
use OxidEsales\GraphQl\Service\EnvironmentServiceInterface;
use OxidEsales\GraphQl\Utility\AuthConstants;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AuthenticationServiceTest extends \PHPUnit_Framework_TestCase
{

    const TESTKEY = '1234567890123456';
    /**
     * @var AuthenticationService $authService
     */
    private $authService;

    /** @var  \PHPUnit_Framework_MockObject_MockObject|UserDaoInterface $userDao */
    private $userDao;

    private $testUserId = 'adminid';
    private $testUserGroup = AuthConstants::USER_GROUP_ADMIN;

    public function setUp()
    {
        /** @var EnvironmentServiceInterface|\PHPUnit_Framework_MockObject_MockObject $environmentService */
        $environmentService = $this->getMockBuilder(EnvironmentServiceInterface::class)->getMock();
        $environmentService->method('getShopUrl')->willReturn('https://myshop.de');
        /** @var UserDaoInterface|\PHPUnit_Framework_MockObject_MockObject $userDao */
        $this->userDao = $this->getMockBuilder(UserDaoInterface::class)->getMock();
        $this->authService = new AuthenticationService($environmentService, $this->userDao);
    }

    public function testAnonymousToken()
    {
        $tokenRequest = new TokenRequest();
        $tokenRequest->setLang('de');
        $tokenRequest->setShopid(1);
        $tokenRequest->setGroup('');

        $token = $this->authService->getToken($tokenRequest);
        $this->assertEquals('anonymousid', $token->getSubject());
        $this->assertEquals('anonymous', $token->getUserGroup());
        $this->assertEquals('de', $token->getLang());
        $this->assertEquals(1, $token->getShopid());

        $tokenString = $token->getJwt($this::TESTKEY);
        $tokenObject = JWT::decode($tokenString, $this::TESTKEY, [$token::ALGORITHM]);
        $this->assertNotEmpty($tokenObject->jti);
    }

    public function testAnonymousTokenNonDefaultLangAndShopId()
    {
        $tokenRequest = new TokenRequest();
        $tokenRequest->setLang('lt');
        $tokenRequest->setShopid(25);
        $tokenRequest->setGroup('');

        $token = $this->authService->getToken($tokenRequest);
        $this->assertEquals('anonymousid', $token->getSubject());
        $this->assertEquals('anonymous', $token->getUserGroup());
        $this->assertEquals('lt', $token->getLang());
        $this->assertEquals(25, $token->getShopid());

        $tokenString = $token->getJwt($this::TESTKEY);
        $tokenObject = JWT::decode($tokenString, $this::TESTKEY, [$token::ALGORITHM]);
        $this->assertNotEmpty($tokenObject->jti);
    }

    public function addIdAndUserGroupToTokenRequest(TokenRequest $tokenRequest)
    {
        $tokenRequest->setUserid($this->testUserId);
        $tokenRequest->setGroup($this->testUserGroup);
        return $tokenRequest;
    }

    public function testUserToken()
    {
        $tokenRequest = new TokenRequest();
        $tokenRequest->setLang('de');
        $tokenRequest->setShopid(1);
        $tokenRequest->setUsername('admin');
        $tokenRequest->setPassword('somepassword');

        $this->userDao->method('addIdAndUserGroupToTokenRequest')
            ->willReturnCallback([$this, 'addIdAndUserGroupToTokenRequest']);


        $token = $this->authService->getToken($tokenRequest);

        $this->assertEquals('adminid', $token->getSubject());
        $this->assertEquals('admin', $token->getUserGroup());

        $tokenString = $token->getJwt($this::TESTKEY);
        $tokenObject = JWT::decode($tokenString, $this::TESTKEY, [$token::ALGORITHM]);
        $this->assertNotEmpty($tokenObject->jti);
        $this->assertEquals(24, strlen($tokenObject->jti));
    }

    public function testUserTokenWithAnonymouyLogin()
    {
        $tokenRequest = new TokenRequest();
        $tokenRequest->setLang('de');
        $tokenRequest->setShopid(1);
        $tokenRequest->setUsername('simplecustomer');
        $tokenRequest->setPassword('somepassword');

        $authToken = new Token();
        $authToken->setKey('anonymouskey');
        $authToken->setUserGroup(AuthConstants::USER_GROUP_ANONMYOUS);
        $tokenRequest->setCurrentToken($authToken);

        $this->testUserId = 'simplecustomerid';
        $this->testUserGroup = AuthConstants::USER_GROUP_CUSTOMER;
        $this->userDao->method('addIdAndUserGroupToTokenRequest')
            ->willReturnCallback([$this, 'addIdAndUserGroupToTokenRequest']);


        $token = $this->authService->getToken($tokenRequest);

        $this->assertEquals($this->testUserId, $token->getSubject());
        $this->assertEquals(AuthConstants::USER_GROUP_CUSTOMER, $token->getUserGroup());

        $tokenString = $token->getJwt($this::TESTKEY);
        $tokenObject = JWT::decode($tokenString, $this::TESTKEY, [$token::ALGORITHM]);
        $this->assertEquals('anonymouskey', $tokenObject->jti);
    }

    public function testUserTokenWithAdminLogin()
    {
        $tokenRequest = new TokenRequest();
        $tokenRequest->setLang('de');
        $tokenRequest->setShopid(1);
        $tokenRequest->setGroup('');
        $tokenRequest->setUsername('admin');
        $tokenRequest->setPassword('somepassword');
        $tokenRequest->setGroup('admin');

        $authToken = new Token();
        $authToken->setKey('anonymouskey');
        $authToken->setUserGroup(AuthConstants::USER_GROUP_ADMIN);
        $tokenRequest->setCurrentToken($authToken);

        $this->userDao->method('addIdAndUserGroupToTokenRequest')
            ->willReturnCallback([$this, 'addIdAndUserGroupToTokenRequest']);


        $token = $this->authService->getToken($tokenRequest);

        $this->assertEquals('adminid', $token->getSubject());
        $this->assertEquals('admin', $token->getUserGroup());

        $tokenString = $token->getJwt($this::TESTKEY);
        $tokenObject = JWT::decode($tokenString, $this::TESTKEY, [$token::ALGORITHM]);
        $this->assertEquals(24, strlen($tokenObject->jti));

    }

    public function testMissingLanguage()
    {
        $this->setExpectedException(InsufficientData::class);

        $tokenRequest = new TokenRequest();
        $tokenRequest->setShopid(1);
        $tokenRequest->setUsername('admin');
        $tokenRequest->setPassword('somepassword');
        $tokenRequest->setGroup('admin');

        $this->userDao->method('addIdAndUserGroupToTokenRequest')->willReturn($tokenRequest);

        $this->authService->getToken($tokenRequest)->getJwt($this::TESTKEY);
    }

    public function testMissingShopId()
    {
        $this->setExpectedException(PasswordMismatchException::class);

        $tokenRequest = new TokenRequest();
        $tokenRequest->setLang('de');
        $tokenRequest->setUsername('admin');
        $tokenRequest->setPassword('somepassword');
        $tokenRequest->setGroup('admin');

        $this->authService->getToken($tokenRequest)->getJwt($this::TESTKEY);

    }

    public function testMissingGroup()
    {
        $this->setExpectedException(InsufficientData::class);

        $tokenRequest = new TokenRequest();
        $tokenRequest->setLang('de');
        $tokenRequest->setShopid(1);
        $tokenRequest->setUsername('admin');
        $tokenRequest->setPassword('somepassword');

        $this->userDao->method('addIdAndUserGroupToTokenRequest')->willReturn($tokenRequest);

        $this->authService->getToken($tokenRequest)->getJwt($this::TESTKEY);

    }

}

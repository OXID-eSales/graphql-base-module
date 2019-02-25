<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Integration\Service;

use Firebase\JWT\JWT;
use OxidEsales\GraphQl\Dao\UserDaoInterface;
use OxidEsales\GraphQl\DataObject\TokenRequest;
use OxidEsales\GraphQl\Exception\InsufficientTokenData;
use OxidEsales\GraphQl\Exception\PasswordMismatchException;
use OxidEsales\GraphQl\Service\AuthenticationService;
use OxidEsales\GraphQl\Service\EnvironmentServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AuthenticationServiceTest extends TestCase
{

    const TESTKEY = '1234567890123456';
    /**
     * @var AuthenticationService $authService
     */
    private $authService;

    /** @var  MockObject|UserDaoInterface $userDao */
    private $userDao;

    public function setUp()
    {
        /** @var EnvironmentServiceInterface|MockObject $environmentService */
        $environmentService = $this->getMockBuilder(EnvironmentServiceInterface::class)->getMock();
        $environmentService->method('getShopUrl')->willReturn('https://myshop.de');
        /** @var UserDaoInterface|MockObject $userDao */
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
        $this->assertEquals('anonymous', $token->getSubject());
        $this->assertEquals('anonymous', $token->getUserGroup());

        $tokenString = $token->getJwt($this::TESTKEY);
        $tokenObject = JWT::decode($tokenString, $this::TESTKEY, [$token::ALGORITHM]);
        $this->assertNotEmpty($tokenObject->jti);
    }

    public function testUserToken()
    {
        $tokenRequest = new TokenRequest();
        $tokenRequest->setLang('de');
        $tokenRequest->setShopid(1);
        $tokenRequest->setGroup('');
        $tokenRequest->setUsername('admin');
        $tokenRequest->setPassword('somepassword');
        $tokenRequest->setGroup('admin');

        $this->userDao->method('fetchUserGroup')->willReturn('admin');


        $token = $this->authService->getToken($tokenRequest);
        $this->assertEquals('admin', $token->getSubject());
        $this->assertEquals('admin', $token->getUserGroup());

        $tokenString = $token->getJwt($this::TESTKEY);
        $tokenObject = JWT::decode($tokenString, $this::TESTKEY, [$token::ALGORITHM]);
        $this->assertNotEmpty($tokenObject->jti);
    }

    public function testMissingLanguage()
    {
        $this->expectException(InsufficientTokenData::class);

        $tokenRequest = new TokenRequest();
        $tokenRequest->setShopid(1);
        $tokenRequest->setUsername('admin');
        $tokenRequest->setPassword('somepassword');
        $tokenRequest->setGroup('admin');

        $this->authService->getToken($tokenRequest)->getJwt($this::TESTKEY);
    }

    public function testMissingShopId()
    {
        $this->expectException(PasswordMismatchException::class);

        $tokenRequest = new TokenRequest();
        $tokenRequest->setLang('de');
        $tokenRequest->setUsername('admin');
        $tokenRequest->setPassword('somepassword');
        $tokenRequest->setGroup('admin');

        $this->authService->getToken($tokenRequest)->getJwt($this::TESTKEY);

    }

    public function testMissingGroup()
    {
        $this->expectException(InsufficientTokenData::class);

        $tokenRequest = new TokenRequest();
        $tokenRequest->setLang('de');
        $tokenRequest->setShopid(1);
        $tokenRequest->setUsername('admin');
        $tokenRequest->setPassword('somepassword');

        $this->authService->getToken($tokenRequest)->getJwt($this::TESTKEY);

    }

}

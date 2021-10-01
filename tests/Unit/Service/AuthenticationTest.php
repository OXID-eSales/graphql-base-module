<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use Lcobucci\JWT\Token;
use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\GraphQL\Base\DataType\User;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyService;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\JwtConfigurationBuilder;
use OxidEsales\GraphQL\Base\Service\KeyRegistry;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;
use OxidEsales\GraphQL\Base\Tests\Unit\BaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcher;

class AuthenticationTest extends BaseTestCase
{
    protected static $token;

    protected static $anonymousToken;

    // phpcs:disable
    protected static $invalidToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5';

    // phpcs:enable

    /** @var KeyRegistry|MockObject */
    private $keyRegistry;

    /** @var LegacyService|MockObject */
    private $legacyService;

    /** @var JwtConfigurationBuilder */
    private $jwtConfigurationBuilder;

    public function setUp(): void
    {
        $this->keyRegistry = $this->getMockBuilder(KeyRegistry::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();
        $this->keyRegistry->method('getSignatureKey')
             ->willReturn('5wi3e0INwNhKe3kqvlH0m4FHYMo6hKef3SzweEjZ8EiPV7I2AC6ASZMpkCaVDTVRg2jbb52aUUXafxXI9/7Cgg==');
        $this->legacyService         = $this->getMockBuilder(LegacyService::class)
                                            ->disableOriginalConstructor()
                                            ->getMock();

        $this->jwtConfigurationBuilder = new JwtConfigurationBuilder(
            $this->keyRegistry,
            $this->legacyService
        );
    }

    protected function getSut(?UnencryptedToken $token = null): Authentication
    {
        return new Authentication(
            $this->legacyService,
            new TokenService(
                $token,
                $this->jwtConfigurationBuilder,
                $this->legacyService
            ),
            new EventDispatcher()
        );
    }

    public function testCreateTokenWithInvalidCredentials(): void
    {
        $this->expectException(InvalidLogin::class);
        $this->legacyService
             ->method('login')
             ->willThrowException(new InvalidLogin('Username/password combination is invalid'));

        $authenticationService = $this->getSut();
        $authenticationService->createToken('foo', 'bar');
    }

    public function testIsLoggedWithoutToken(): void
    {
        $authenticationService = $this->getSut();
        $this->assertFalse($authenticationService->isLogged());
    }

    public function testIsLoggedWithNullToken(): void
    {
        $authenticationService = $this->getSut();
        $this->assertFalse($authenticationService->isLogged());
    }

    public function testCreateTokenWithValidCredentials(): void
    {
        $this->legacyService
             ->method('login');
        $this->legacyService
             ->method('getShopUrl')
             ->willReturn('https://whatever.com');
        $this->legacyService
             ->method('getShopId')
             ->willReturn(1);

        $authenticationService = $this->getSut();

        self::$token = $authenticationService->createToken('admin', 'admin');

        $this->assertInstanceOf(
            Token::class,
            self::$token
        );
    }

    /**
     * @depends testCreateTokenWithValidCredentials
     */
    public function testIsLoggedWithValidToken(): void
    {
        $this->legacyService
             ->method('getShopUrl')
             ->willReturn('https://whatever.com');
        $this->legacyService
             ->method('getShopId')
             ->willReturn(1);

        $authenticationService = $this->getSut(self::$token);

        $this->assertTrue($authenticationService->isLogged());
    }

    /**
     * @depends testCreateTokenWithValidCredentials
     */
    public function testIsLoggedWithValidForAnotherShopIdToken(): void
    {
        $this->expectException(InvalidToken::class);
        $this->legacyService
             ->method('getShopUrl')
             ->willReturn('https://whatever.com');
        $this->legacyService
             ->method('getShopId')
             ->willReturn(-1);

        $authenticationService = $this->getSut(self::$token);
        $authenticationService->isLogged();
    }

    /**
     * @depends testCreateTokenWithValidCredentials
     *
     * can not use expectException due to needed cleanup in registry config
     */
    public function testGetUserNameWithValidForAnotherShopUrlToken(): void
    {
        $this->expectException(InvalidToken::class);

        $this->legacyService
             ->method('getShopUrl')
             ->willReturn('https:/other.com');
        $this->legacyService
             ->method('getShopId')
             ->willReturn(1);

        $authenticationService = $this->getSut(self::$token);
        $authenticationService->isLogged();
    }

    public function testCreateTokenWithValidCredentialsForBlockedUser(): void
    {
        $this->legacyService
             ->method('login');
        $this->legacyService
             ->method('getShopUrl')
             ->willReturn('https://whatever.com');
        $this->legacyService
             ->method('getShopId')
             ->willReturn(1);
        $this->legacyService
             ->method('getUserGroupIds')
             ->willReturn(['foo', 'oxidblocked', 'bar']);

        $authenticationService = $this->getSut();

        self::$token = $authenticationService->createToken('admin', 'admin');

        $this->assertInstanceOf(
            Token::class,
            self::$token
        );
    }

    /**
     * @depends testCreateTokenWithValidCredentials
     */
    public function testIsLoggedWithValidCredentialsForBlockedUser(): void
    {
        $this->legacyService
            ->method('login');
        $this->legacyService
            ->method('getShopUrl')
            ->willReturn('https://whatever.com');
        $this->legacyService
            ->method('getShopId')
            ->willReturn(1);
        $this->legacyService
            ->method('getUserGroupIds')
            ->willReturn(['foo', 'oxidblocked', 'bar']);

        $authenticationService = $this->getSut(self::$token);

        $this->expectException(InvalidToken::class);
        $authenticationService->isLogged();
    }

    public function providerGetUserName()
    {
        return [
            'admin' => [
                'username' => 'admin',
                'password' => 'admin',
            ],
            'user'  => [
                'username' => 'user@oxid-esales.com',
                'password' => 'useruser',
            ],
            'not_existing'  => [
                'username' => 'notauser@oxid-esales.com',
                'password' => 'notauseruser',
            ],
        ];
    }

    /**
     * @dataProvider providerGetUserName
     *
     * @param mixed $username
     * @param mixed $password
     */
    public function testGetUserName($username, $password): void
    {
        $authenticationService = $this->getAuthenticationService();
        $token                 = $authenticationService->createToken($username, $password);

        $authenticationService = $this->getSut($token);

        $this->assertSame($username, $authenticationService->getUserName());
    }

    public function testGetUserNameForNullToken(): void
    {
        $authenticationService = $this->getAuthenticationService();

        $this->expectException(InvalidToken::class);
        $authenticationService->getUserName();
    }

    public function testGetUserId(): void
    {
        $userModel = $this->getUserModelStub('the_admin_oxid');

        $this->legacyService->method('login')->willReturn(new User($userModel));
        $this->legacyService->method('getUserModel')->with('the_admin_oxid')->willReturn($userModel);

        $authenticationService = $this->getAuthenticationService();
        $token                 = $authenticationService->createToken('admin', 'admin');

        $authenticationService = $this->getSut($token);

        $this->assertSame('the_admin_oxid', $authenticationService->getUser()->getUserId());
        $this->assertNotNull($authenticationService->getUserName());
    }

    public function testGetUserIdForNullToken(): void
    {
        $userModel = $this->getUserModelStub();
        $this->legacyService->method('getUserModel')->with('')->willReturn($userModel);

        $authenticationService = $this->getAuthenticationService();

        $user = $authenticationService->getUser();

        $this->assertNotNull($user->getUserId());
        $this->assertTrue($user->isAnonymous());
    }

    public function testGetUserIdForAnonymousToken(): void
    {
        $this->legacyService->method('login')->willReturn(
            new User($this->getUserModelStub(), true)
        );

        $userModel = $this->getUserModelStub();
        $this->legacyService->method('getUserModel')->willReturn($userModel);

        $authenticationService = $this->getAuthenticationService();
        $anonymousToken        = $authenticationService->createToken();

        $authenticationService = $this->getSut($anonymousToken);

        $this->assertNotEmpty($authenticationService->getUser()->getUserId());
    }

    public function testCreateAnonymousToken(): void
    {
        $this->legacyService->method('login')->willReturn(
            new User($this->getUserModelStub(), true)
        );

        $this->legacyService
            ->method('getShopUrl')
            ->willReturn('https://whatever.com');
        $this->legacyService
            ->method('getShopId')
            ->willReturn(1);

        $authenticationService = $this->getSut();

        self::$anonymousToken = $authenticationService->createToken();

        $this->assertInstanceOf(
            Token::class,
            self::$anonymousToken
        );

        $this->assertNull(
            self::$anonymousToken->claims()->get(Authentication::CLAIM_USERNAME)
        );
    }

    /**
     * @depends testCreateAnonymousToken
     */
    public function testIsLoggedWithAnonymousToken(): void
    {
        $this->legacyService
            ->method('getShopUrl')
            ->willReturn('https://whatever.com');
        $this->legacyService
            ->method('getShopId')
            ->willReturn(1);
        $this->legacyService
            ->method('getUserGroupIds')
            ->willReturn(['oxidanonymous']);

        $authenticationService = $this->getSut(self::$anonymousToken);

        $this->assertFalse($authenticationService->isLogged());
    }

    /**
     * @depends testCreateAnonymousToken
     */
    public function testIsUserAnonymous(): void
    {
        $this->legacyService
            ->method('getShopUrl')
            ->willReturn('https://whatever.com');
        $this->legacyService
            ->method('getShopId')
            ->willReturn(1);
        $this->legacyService
            ->method('getUserGroupIds')
            ->willReturn(['oxidanonymous']);

        $authenticationService = $this->getSut(self::$anonymousToken);

        $this->assertTrue($authenticationService->getUser()->isAnonymous());
    }

    /**
     * @depends testCreateTokenWithValidCredentials
     */
    public function testLoggedUserIsNotAnonymous(): void
    {
        $this->legacyService
            ->method('getShopUrl')
            ->willReturn('https://whatever.com');
        $this->legacyService
            ->method('getShopId')
            ->willReturn(1);

        $authenticationService = $this->getSut(self::$token);

        $this->assertFalse($authenticationService->getUser()->isAnonymous());
    }

    public function testIsAnonymousWithNullToken(): void
    {
        $authenticationService = $this->getSut();

        $this->assertTrue($authenticationService->getUser()->isAnonymous());
    }

    /**
     * @depends testCreateAnonymousToken
     */
    public function testGetUserNameForAnonymousToken(): void
    {
        $this->legacyService
            ->method('getShopUrl')
            ->willReturn('https://whatever.com');
        $this->legacyService
            ->method('getShopId')
            ->willReturn(1);
        $this->legacyService
            ->method('getUserGroupIds')
            ->willReturn(['oxidanonymous']);

        $authenticationService = $this->getSut(self::$anonymousToken);

        $this->expectException(InvalidToken::class);

        $authenticationService->getUserName();
    }

    public function testLoggedUserInAnonymousGroup(): void
    {
        $this->legacyService->method('login')->willReturn(
            new User($this->getUserModelStub(), true)
        );

        $this->legacyService->method('getUserGroupIds')
            ->willReturn(['oxidanonymous']);

        $authenticationService = $this->getAuthenticationService();
        $token                 = $authenticationService->createToken('admin', 'admin');

        $authenticationService = $this->getSut($token);

        $this->assertTrue($authenticationService->getUser()->isAnonymous());
    }

    private function getAuthenticationService(): Authentication
    {
        $this->legacyService
            ->method('getShopUrl')
            ->willReturn('https://whatever.com');
        $this->legacyService
            ->method('getShopId')
            ->willReturn(1);

        return $this->getSut();
    }
}

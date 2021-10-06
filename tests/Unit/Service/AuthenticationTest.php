<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use Lcobucci\JWT\Token;
use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\Eshop\Application\Model\User as UserModel;
use OxidEsales\GraphQL\Base\DataType\User;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyService;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\JwtConfigurationBuilder;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;
use OxidEsales\GraphQL\Base\Tests\Unit\BaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcher;

class AuthenticationTest extends BaseTestCase
{
    // phpcs:enable

    /** @var LegacyService|MockObject */
    private $legacy;

    /** @var JwtConfigurationBuilder */
    private $jwtConfigurationBuilder;

    /** @var TokenService */
    private $tokenService;

    public function setUp(): void
    {
        $this->legacy = $this->getMockBuilder(LegacyService::class)
                            ->disableOriginalConstructor()
                            ->getMock();

        $this->jwtConfigurationBuilder = new JwtConfigurationBuilder(
            $this->getKeyRegistryMock(),
            $this->legacy
        );

        $this->tokenService = new TokenService(
            null,
            $this->jwtConfigurationBuilder,
            $this->legacy,
            new EventDispatcher()
        );
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

    /**
     * @dataProvider providerValidCredentials
     */
    public function testIsLoggedWithValidToken(string $username, string $password): void
    {
        $token = $this->createToken($username, $password);
        $authenticationService = $this->getSut($token);

        $this->assertTrue($authenticationService->isLogged());
    }

    /**
     * @dataProvider providerValidCredentials
     */
    public function testIsLoggedWithValidForAnotherShopIdToken(string $username, string $password): void
    {
        $this->expectException(InvalidToken::class);
        $token = $this->createToken($username, $password);

        $legacy = $this->getMockBuilder(LegacyService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $legacy
            ->method('getShopId')
            ->willReturn(-1);

        $jwtConfigurationBuilder = new JwtConfigurationBuilder(
            $this->getKeyRegistryMock(),
            $legacy
        );

        $authenticationService = new Authentication(
            $legacy,
            new TokenService(
                $token,
                $jwtConfigurationBuilder,
                $legacy,
                new EventDispatcher()
            )
        );

        $authenticationService->isLogged();
    }

    /**
     * @dataProvider providerValidCredentials
     *
     * can not use expectException due to needed cleanup in registry config
     */
    public function testGetUserNameWithValidForAnotherShopUrlToken(string $username, string $password): void
    {
        $this->expectException(InvalidToken::class);
        $token = $this->createToken($username, $password);

        $legacy = $this->getMockBuilder(LegacyService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $legacy
            ->method('getShopUrl')
            ->willReturn('https:/other.com');
        $legacy
            ->method('getShopId')
            ->willReturn(1);

        $jwtConfigurationBuilder = new JwtConfigurationBuilder(
            $this->getKeyRegistryMock(),
            $legacy
        );

        $authenticationService = new Authentication(
            $legacy,
            new TokenService(
                $token,
                $jwtConfigurationBuilder,
                $legacy,
                new EventDispatcher()
            )
        );

        $authenticationService->isLogged();
    }

    public function testIsLoggedWithValidCredentialsForBlockedUser(): void
    {
        $this->legacy
            ->method('login')
            ->willReturn(new User($this->getUserModelStub('the_admin_oxid')));
        $this->legacy
            ->method('getShopUrl')
            ->willReturn('https://whatever.com');
        $this->legacy
            ->method('getShopId')
            ->willReturn(1);
        $this->legacy
            ->method('getUserGroupIds')
            ->willReturn(['foo', 'oxidblocked', 'bar']);

        $token = $this->tokenService->createToken('admin', 'admin');
        $authenticationService = $this->getSut($token);

        $this->expectException(InvalidToken::class);
        $authenticationService->isLogged();
    }

    public function providerValidCredentials(): array
    {
        return [
            'admin' => [
                'username' => 'admin',
                'password' => 'admin',
            ],
        ];
    }

    public function providerInvalidCredentials(): array
    {
        return [
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
     * @dataProvider providerValidCredentials
     *
     * @param mixed $username
     * @param mixed $password
     */
    public function testGetUser($username, $password): void
    {
        $token                 = $this->createToken($username, $password);
        $authenticationService = $this->getSut($token);

        $this->assertInstanceOf(User::class, $authenticationService->getUser());
    }

    /**
     * @dataProvider providerInvalidCredentials
     *
     * @param mixed $username
     * @param mixed $password
     */
    public function testGetUserNameWithInvalidCredentials($username, $password): void
    {
        $this->expectException(InvalidLogin::class);

        $token                 = $this->createToken($username, $password);
        $authenticationService = $this->getSut($token);

        $this->assertEmpty($authenticationService->getUser()->email());
    }

    /**
     * @dataProvider providerValidCredentials
     *
     * @param mixed $username
     * @param mixed $password
     */
    public function testGetUserNameWithValidCredentials($username, $password): void
    {
        $token                 = $this->createToken($username, $password);
        $authenticationService = $this->getSut($token);

        $this->assertSame($username, $authenticationService->getUser()->email());
    }

    public function testGetUserNameForNullToken(): void
    {
        $authenticationService = $this->getAuthenticationService();

        $this->assertEmpty($authenticationService->getUser()->email());
    }

    public function testGetUserId(): void
    {
        $userModel = $this->getUserModelStub('the_admin_oxid');

        $this->legacy->method('login')->willReturn(new User($userModel));
        $this->legacy->method('getUserModel')->with('the_admin_oxid')->willReturn($userModel);

        $token                 = $this->tokenService->createToken('admin', 'admin');
        $authenticationService = $this->getSut($token);

        $this->assertSame('the_admin_oxid', $authenticationService->getUser()->id()->val());
        $this->assertNotNull($authenticationService->getUser()->email());
    }

    public function testGetUserIdForNullToken(): void
    {
        $userModel = $this->getUserModelStub();
        $this->legacy->method('getUserModel')->with('')->willReturn($userModel);

        $authenticationService = $this->getAuthenticationService();

        $user = $authenticationService->getUser();

        $this->assertEmpty($user->id()->val());
    }

    public function testGetUserIdForAnonymousToken(): void
    {
        $someRandomModelStub = $this->getUserModelStub('someRandomId');

        $this->legacy->method('login')->willReturn(
            new User($someRandomModelStub, true)
        );

        $this->legacy->method('getUserModel')->willReturn($someRandomModelStub);

        $anonymousToken        = $this->tokenService->createToken();
        $authenticationService = $this->getSut($anonymousToken);

        $this->assertNotEmpty($authenticationService->getUser()->id()->val());
    }

    public function testIsLoggedWithAnonymousToken(): void
    {
        $this->legacy
            ->method('login')
            ->willReturn(
                new User($this->getUserModelStub(), true)
            );
        $this->legacy
            ->method('getShopUrl')
            ->willReturn('https://whatever.com');
        $this->legacy
            ->method('getShopId')
            ->willReturn(1);
        $this->legacy
            ->method('getUserGroupIds')
            ->willReturn(['oxidanonymous']);

        $anonymousToken        = $this->tokenService->createToken();
        $authenticationService = $this->getSut($anonymousToken);

        $this->assertFalse($authenticationService->isLogged());
    }

    public function testIsUserAnonymous(): void
    {
        $this->legacy
            ->method('login')
            ->willReturn(
                new User($this->getUserModelStub(), true)
            );
        $this->legacy
            ->method('getShopUrl')
            ->willReturn('https://whatever.com');
        $this->legacy
            ->method('getShopId')
            ->willReturn(1);
        $this->legacy
            ->method('getUserGroupIds')
            ->willReturn(['oxidanonymous']);

        $anonymousToken        = $this->tokenService->createToken();
        $authenticationService = $this->getSut($anonymousToken);

        $this->assertTrue($authenticationService->getUser()->isAnonymous());
    }

    /**
     * @dataProvider providerValidCredentials
     */
    public function testLoggedUserIsNotAnonymous(string $username, string $password): void
    {
        $token                 = $this->createToken($username, $password);
        $authenticationService = $this->getSut($token);

        $this->assertFalse($authenticationService->getUser()->isAnonymous());
    }

    public function testIsAnonymousWithNullToken(): void
    {
        $authenticationService = $this->getSut();

        $this->assertFalse($authenticationService->getUser()->isAnonymous());
    }

    /**
     * @dataProvider providerInvalidCredentials
     */
    public function testIsAnonymousWithWrongCredentials(string $username, string $password): void
    {
        $this->expectException(InvalidLogin::class);

        $token                 = $this->createToken($username, $password);
        $authenticationService = $this->getSut($token);

        $this->assertFalse($authenticationService->getUser()->isAnonymous());
    }

    public function testGetUserNameForAnonymousToken(): void
    {
        $this->legacy
            ->method('login')
            ->willReturn(
            new User($this->getUserModelStub(), true)
        );
        $this->legacy
            ->method('getShopUrl')
            ->willReturn('https://whatever.com');
        $this->legacy
            ->method('getShopId')
            ->willReturn(1);
        $this->legacy
            ->method('getUserGroupIds')
            ->willReturn(['oxidanonymous']);

        $anonymousToken        = $this->tokenService->createToken();
        $authenticationService = $this->getSut($anonymousToken);

        $this->assertEmpty($authenticationService->getUser()->email());
    }

    public function testLoggedUserInAnonymousGroup(): void
    {
        $this->legacy->method('login')->willReturn(
            new User($this->getUserModelStub(), true)
        );

        $this->legacy->method('getUserGroupIds')
            ->willReturn(['oxidanonymous']);

        $token = $this->tokenService->createToken('admin', 'admin');

        $authenticationService = $this->getSut($token);

        $this->assertTrue($authenticationService->getUser()->isAnonymous());
    }

    protected function getSut(?UnencryptedToken $token = null): Authentication
    {
        return new Authentication(
            $this->legacy,
            new TokenService(
                $token,
                $this->jwtConfigurationBuilder,
                $this->legacy,
                new EventDispatcher()
            )
        );
    }

    private function getAuthenticationService(): Authentication
    {
        $this->legacy
            ->method('getShopUrl')
            ->willReturn('https://whatever.com');
        $this->legacy
            ->method('getShopId')
            ->willReturn(1);

        return $this->getSut();
    }

    private function createToken(string $username, string $password): UnencryptedToken
    {
        $userModel = oxNew(UserModel::class);
        $userId = $userModel->getIdByUserName($username);

        if ($userId) {
            $userModel->load($userId);
            $user = new User($userModel);
            $this->legacy
                ->method('login')
                ->willReturn($user);
        } else {
            $this->legacy
                ->method('login')
                ->willThrowException(new InvalidLogin('Username/password combination is invalid'));
        }

        $this->legacy
            ->method('getShopUrl')
            ->willReturn('https://whatever.com');
        $this->legacy
            ->method('getShopId')
            ->willReturn(1);
        $this->legacy
            ->method('getUserModel')
            ->willReturn($userModel);

        return $this->tokenService->createToken($username, $password);
    }
}

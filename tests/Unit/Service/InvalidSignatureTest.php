<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Service;

use Lcobucci\JWT\Token;
use OxidEsales\GraphQL\Base\DataType\User;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Framework\RequestReader;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyService;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\JwtConfigurationBuilder;
use OxidEsales\GraphQL\Base\Service\KeyRegistry;
use OxidEsales\GraphQL\Base\Service\Token as TokenService;
use OxidEsales\GraphQL\Base\Tests\Unit\BaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcher;

class InvalidSignatureTest extends BaseTestCase
{
    protected static $token;

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

    public function tearDown(): void
    {
        unset($_SERVER['HTTP_AUTHORIZATION']);
    }

    public function testCreateTokenWithValidCredentials(): void
    {
        $userModel = $this->getUserModelStub('the_admin_oxid');
        $this->legacyService
            ->method('login')
            ->willReturn(new User($userModel));
        $this->legacyService
             ->method('getShopUrl')
             ->willReturn('https://whatever.com');
        $this->legacyService
             ->method('getShopId')
             ->willReturn(1);

        $tokenService = new TokenService(
            null,
            $this->jwtConfigurationBuilder,
            $this->legacyService,
            new EventDispatcher()
        );

        self::$token = $tokenService->createToken('admin', 'admin');

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

        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . substr(self::$token->toString(), 0, -10);

        $requestReader = new RequestReader($this->legacyService, $this->getJwtConfigurationBuilder());
        $token         = $requestReader->getAuthToken();

        $authenticationService = new Authentication(
            $this->legacyService,
            new TokenService(
                $token,
                $this->jwtConfigurationBuilder,
                $this->legacyService,
                new EventDispatcher()
            )
        );

        $this->expectException(InvalidToken::class);

        $authenticationService->isLogged();
    }

    protected function getJwtConfigurationBuilder(): JwtConfigurationBuilder
    {
        return new JwtConfigurationBuilder($this->getKeyRegistryMock(), $this->legacyService);
    }
}

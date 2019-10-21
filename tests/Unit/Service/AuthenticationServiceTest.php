<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Tests\Unit\Service;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\GraphQL\Exception\InvalidLoginException;
use OxidEsales\GraphQL\Exception\InvalidTokenException;
use OxidEsales\GraphQL\Framework\RequestReaderInterface;
use OxidEsales\GraphQL\Service\AuthenticationService;
use OxidEsales\GraphQL\Service\KeyRegistryInterface;
# use PHPUnit\Framework\TestCase;
use OxidEsales\TestingLibrary\UnitTestCase as TestCase;

class AuthenticationServiceTest extends TestCase
{
    protected static $token = null;

    protected static $invalidToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5';

    private function getKeyRegistryMock(): KeyRegistryInterface
    {
        $keyRegistry = $this->getMockBuilder(KeyRegistryInterface::class)->getMock();
        $keyRegistry->method('getSignatureKey')
                    ->willReturn('5wi3e0INwNhKe3kqvlH0m4FHYMo6hKef3SzweEjZ8EiPV7I2AC6ASZMpkCaVDTVRg2jbb52aUUXafxXI9/7Cgg==');
        return $keyRegistry;
    }

    private function getAuthenticationService(): AuthenticationService
    {
        return new AuthenticationService(
            $this->getKeyRegistryMock()
        );
    }

    public function testCreateTokenFromRequest()
    {
        $requestReader = $this->getMockBuilder(RequestReaderInterface::class)->getMock();
        $requestReader->method('getAuthToken')
                      ->will($this->onConsecutiveCalls(
                          null,
                          'invalid',
                          self::$invalidToken
                      ));
        $this->assertNull(AuthenticationService::createTokenFromRequest($requestReader));
        $e = null;
        try {
            AuthenticationService::createTokenFromRequest($requestReader);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            InvalidTokenException::class,
            $e
        );
        $this->assertInstanceOf(
            Token::class,
            AuthenticationService::createTokenFromRequest($requestReader)
        );
    }

    public function testCreateTokenWithInvalidCredentials()
    {
        $auth = $this->getAuthenticationService();
        $this->expectException(InvalidLoginException::class);
        $auth->createToken('foo', 'bar', 999);
    }

    public function testIsLoggedWithoutToken()
    {
        $auth = $this->getAuthenticationService();
        $auth->setToken(null);
        $this->assertFalse($auth->isLogged());
    }

    public function testIsLoggedWithFormallyCorrectButInvalidToken()
    {
        $this->expectException(InvalidTokenException::class);
        $auth = $this->getAuthenticationService();
        $auth->setToken(
            (new Parser())->parse(self::$invalidToken)
        );
        $auth->isLogged();
    }

    public function testCreateTokenWithValidCredentials()
    {
        $auth = $this->getAuthenticationService();
        self::$token = $auth->createToken('admin', 'admin', 1);
        $this->assertInstanceOf(
            \Lcobucci\JWT\Token::class,
            self::$token
        );
    }

    /**
     * @depends testCreateTokenWithValidCredentials
     */
    public function testIsLoggedWithValidToken()
    {
        $auth = $this->getAuthenticationService();
        $auth->setToken(
            self::$token
        );
        $this->assertTrue($auth->isLogged());
    }

    /**
     * @depends testCreateTokenWithValidCredentials
     *
     * can not use expectException due to needed cleanup in registry config
     */
    public function testIsLoggedWithValidForAnotherShopIdToken()
    {
        $oldConfig = Registry::getConfig();
        $config = $this->getMockBuilder(Config::class)->getMock();
        $config->method('getShopId')
               ->willReturn(-1);
        Registry::set(Config::class, $config);

        $auth = $this->getAuthenticationService();
        $auth->setToken(
            self::$token
        );
        try {
            $auth->isLogged();
        } catch (InvalidTokenException $e) {
        }
        $this->assertInstanceOf(
            InvalidTokenException::class,
            $e
        );
        Registry::set(Config::class, $oldConfig);
    }

    /**
     * @depends testCreateTokenWithValidCredentials
     *
     * can not use expectException due to needed cleanup in registry config
     */
    public function testIsLoggedWithValidForAnotherShopUrlToken()
    {
        $oldConfig = Registry::getConfig();
        $config = $this->getMockBuilder(Config::class)->getMock();
        $config->method('getShopUrl')
               ->willReturn('invalid');
        Registry::set(Config::class, $config);

        $auth = $this->getAuthenticationService();
        $auth->setToken(
            self::$token
        );
        try {
            $auth->isLogged();
        } catch (InvalidTokenException $e) {
        }
        $this->assertInstanceOf(
            InvalidTokenException::class,
            $e
        );
        Registry::set(Config::class, $oldConfig);
    }
}

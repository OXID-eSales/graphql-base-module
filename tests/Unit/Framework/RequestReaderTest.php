<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Framework;

use Exception;
use Lcobucci\JWT\Token;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Framework\NullToken;
use OxidEsales\GraphQL\Base\Framework\RequestReader;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Base\Service\JwtConfigurationBuilder;
use OxidEsales\GraphQL\Base\Tests\Unit\BaseTestCase;

class RequestReaderTest extends BaseTestCase
{
    // phpcs:disable
    protected static $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.cThIIoDvwdueQB468K5xDc5633seEFoqwxjF_xSJyQQ';

    public function testGetAuthTokenWithoutToken(): void
    {
        $requestReader = new RequestReader($this->getLegacyMock(), $this->getJwtConfigurationBuilder());
        $this->assertNull($requestReader->getAuthToken());
    }

    public function testGetAuthTokenWithWrongFormattedHeader(): void
    {
        $requestReader = new RequestReader($this->getLegacyMock(), $this->getJwtConfigurationBuilder());
        $headers       = [
            'HTTP_AUTHORIZATION',
            'REDIRECT_HTTP_AUTHORIZATION',
        ];

        foreach ($headers as $header) {
            $_SERVER[$header] = 'authtoken';
            $this->assertNull($requestReader->getAuthToken());

            unset($_SERVER[$header]);
        }
    }

    public function testGetAuthTokenWithCorrectFormattedHeaderButInvalidJWT(): void
    {
        $requestReader = new RequestReader($this->getLegacyMock(), $this->getJwtConfigurationBuilder());
        $headers       = [
            'HTTP_AUTHORIZATION',
            'REDIRECT_HTTP_AUTHORIZATION',
        ];

        foreach ($headers as $header) {
            $e                = null;
            $_SERVER[$header] = 'Bearer invalidjwt';

            try {
                $requestReader->getAuthToken();
            } catch (Exception $e) {
            }
            $this->assertInstanceOf(
                InvalidToken::class,
                $e
            );
            unset($_SERVER[$header]);
        }
    }

    public function testGetAuthTokenWithCorrectFormat(): void
    {
        $requestReader = new RequestReader($this->getLegacyMock(), $this->getJwtConfigurationBuilder());
        $headers       = [
            'HTTP_AUTHORIZATION',
            'REDIRECT_HTTP_AUTHORIZATION',
        ];

        foreach ($headers as $header) {
            // add also a whitespace to the beginning if the header
            // to test the trim() call
            $_SERVER[$header] = ' Bearer ' . self::$token;
            $token            = $requestReader->getAuthToken();
            $this->assertInstanceOf(
                Token::class,
                $token
            );
            unset($_SERVER[$header]);
        }
    }

    public function testGetAuthTokenWithCorrectFormatButBlockedUser(): void
    {
        $legacy = $this->getMockBuilder(Legacy::class)
            ->disableOriginalConstructor()
            ->getMock();
        $legacy->method('getUserGroupIds')
               ->willReturn(['group', 'oxidblocked', 'anothergroup']);

        $requestReader = new RequestReader($legacy, $this->getJwtConfigurationBuilder());

        $_SERVER['HTTP_AUTHORIZATION'] = ' Bearer ' . self::$token;
        $e                             = null;

        try {
            $token            = $requestReader->getAuthToken();
        } catch (Exception $e) {
        }
        unset($_SERVER['HTTP_AUTHORIZATION']);
        $this->assertInstanceOf(InvalidToken::class, $e);
        $this->assertNotNull($e);
    }

    public function testGetGraphQLRequestDataWithEmptyRequest(): void
    {
        $requestReader = new RequestReader($this->getLegacyMock(), $this->getJwtConfigurationBuilder());
        $this->assertEquals(
            [
                'query'         => null,
                'variables'     => null,
                'operationName' => null,
            ],
            $requestReader->getGraphQLRequestData()
        );
    }

    public function testGetGraphQLRequestDataWithInputRequest(): void
    {
        $requestReader           = new RequestReader($this->getLegacyMock(), $this->getJwtConfigurationBuilder());
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $this->assertEquals(
            [
                'query'         => 'query {token}',
                'variables'     => null,
                'operationName' => null,
            ],
            $requestReader->getGraphQLRequestData(__DIR__ . '/fixtures/simpleRequest.json')
        );
        unset($_SERVER['CONTENT_TYPE']);
    }

    public function testGetGraphQLRequestDataWithInputRequestWithoutJson(): void
    {
        $requestReader             = new RequestReader($this->getLegacyMock(), $this->getJwtConfigurationBuilder());
        $_SERVER['CONTENT_TYPE']   = 'text/plain';
        $_REQUEST['query']         = 'query {token_}';
        $_REQUEST['variables']     = '{"foo":"bar"}';
        $_REQUEST['operationName'] = 'operation_name';
        $this->assertSame(
            [
                'query'         => 'query {token_}',
                'variables'     => ['foo' => 'bar'],
                'operationName' => 'operation_name',
            ],
            $requestReader->getGraphQLRequestData()
        );
        unset($_SERVER['CONTENT_TYPE'], $_REQUEST['query'], $_REQUEST['variables'], $_REQUEST['operationName']);
    }

    // phpcs:enable

    private function getLegacyMock(): Legacy
    {
        $mock = $this->getMockBuilder(Legacy::class)
             ->disableOriginalConstructor()
             ->getMock();

        $mock->expects($this->any())
            ->method('getShopUrl')
            ->willReturn('www.myoxidshop.com');

        return $mock;
    }

    private function getJwtConfigurationBuilder(): JwtConfigurationBuilder
    {
        return new JwtConfigurationBuilder($this->getKeyRegistryMock(), $this->getLegacyMock());
    }
}

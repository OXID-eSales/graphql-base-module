<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Framework;

use Exception;
use GraphQL\Error\InvariantViolation;
use Lcobucci\JWT\Token;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Framework\NullToken;
use OxidEsales\GraphQL\Base\Framework\RequestReader;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use PHPUnit\Framework\TestCase;

class RequestReaderTest extends TestCase
{
    // phpcs:disable
    protected static $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiIsImlzcyI6Imh0dHA6Ly93d3cub3hpZC1lc2hvcC5sb2NhbC8ifQ.eyJpc3MiOiJodHRwOi8vd3d3Lm94aWQtZXNob3AubG9jYWwvIiwiYXVkIjoiaHR0cDovL3d3dy5veGlkLWVzaG9wLmxvY2FsLyIsImlhdCI6MTYzMDkyNTMxOC43MTU2MzMsIm5iZiI6MTYzMDkyNTMxOC43MTU2MzMsImV4cCI6MTYzMDk1NDExOC43MTU2NCwic2hvcGlkIjoxLCJ1c2VybmFtZSI6InVzZXJAb3hpZC1lc2FsZXMuY29tIiwidXNlcmlkIjoiNzJlZDg3OTRkNDI3M2I5Yzc1Y2VjMjMyZTc0OTljN2QifQ.Xj5zRXKr3dPhXyGgbeGrFAn7UzXbhqrvS2b-oVrqgz0-Dmmx6LAup4tkeFo3guqa6uGa-5QDT6YheUU902pzYA';

    public function testGetAuthTokenWithoutToken(): void
    {
        $requestReader = new RequestReader($this->getLegacyMock());
        $this->assertInstanceOf(
            NullToken::class,
            $requestReader->getAuthToken()
        );
    }

    public function testGetAuthTokenWithWrongFormattedHeader(): void
    {
        $requestReader = new RequestReader($this->getLegacyMock());
        $headers       = [
            'HTTP_AUTHORIZATION',
            'REDIRECT_HTTP_AUTHORIZATION',
        ];

        foreach ($headers as $header) {
            $_SERVER[$header] = 'authtoken';
            $this->assertInstanceOf(
                NullToken::class,
                $requestReader->getAuthToken()
            );
            unset($_SERVER[$header]);
        }
    }

    public function testGetAuthTokenWithCorrectFormattedHeaderButInvalidJWT(): void
    {
        $requestReader = new RequestReader($this->getLegacyMock());
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
        $requestReader = new RequestReader($this->getLegacyMock());
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
            $this->assertNotInstanceOf(
                NullToken::class,
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

        $requestReader = new RequestReader($legacy);

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
        $requestReader = new RequestReader($this->getLegacyMock());
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
        $requestReader           = new RequestReader($this->getLegacyMock());
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
        $requestReader             = new RequestReader($this->getLegacyMock());
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

    public function testGetGraphQLRequestDataWithInvalidFileInput(): void
    {
        $this->expectException(InvariantViolation::class);
        $requestReader           = new RequestReader($this->getLegacyMock());
        $_SERVER['CONTENT_TYPE'] = 'multipart/form-data; boundary=----WebKitFormBoundaryoaY0xvjC2DBjmPRZ';

        $requestReader->getGraphQLRequestData();

        unset($_SERVER['CONTENT_TYPE']);
    }

    public function testGetGraphQLRequestDataWithFileInput(): void
    {
        $requestReader           = new RequestReader($this->getLegacyMock());
        $_SERVER['CONTENT_TYPE'] = 'multipart/form-data; boundary=----WebKitFormBoundaryoaY0xvjC2DBjmPRZ';
        $_POST['map']            = '{}';
        $_POST['operations']     = '{"query":"query anonymous {token}", "variables":{"file":null}, "operationName":"anonymous"}';

        $this->assertSame(
            [
                'query'         => 'query anonymous {token}',
                'variables'     => ['file' => null],
                'operationName' => 'anonymous',
            ],
            $requestReader->getGraphQLRequestData()
        );
        unset($_SERVER['CONTENT_TYPE'], $_POST['map'], $_POST['operations']);
    }

    // phpcs:enable

    private function getLegacyMock(): Legacy
    {
        return $this->getMockBuilder(Legacy::class)
             ->disableOriginalConstructor()
             ->getMock();
    }
}

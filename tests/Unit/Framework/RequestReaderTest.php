<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Framework;

use Lcobucci\JWT\Token;
use OxidEsales\GraphQL\Base\Exception\InvalidTokenException;
use OxidEsales\GraphQL\Base\Framework\RequestReader;
use PHPUnit\Framework\TestCase;

class RequestReaderTest extends TestCase
{
    // phpcs:disable
    protected static $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5';
    // phpcs:enable

    public function testGetAuthTokenWithoutToken()
    {
        $requestReader = new RequestReader();
        $this->assertEquals(
            null,
            $requestReader->getAuthToken()
        );
    }

    public function testGetAuthTokenWithWrongFormattedHeader()
    {
        $requestReader = new RequestReader();
        $headers = [
            'HTTP_AUTHORIZATION',
            'REDIRECT_HTTP_AUTHORIZATION'
        ];
        foreach ($headers as $header) {
            $_SERVER[$header] = 'authtoken';
            $this->assertEquals(
                null,
                $requestReader->getAuthToken()
            );
            unset($_SERVER[$header]);
        }
    }

    public function testGetAuthTokenWithCorrectFormattedHeaderButInvalidJWT()
    {
        $requestReader = new RequestReader();
        $headers = [
            'HTTP_AUTHORIZATION',
            'REDIRECT_HTTP_AUTHORIZATION'
        ];
        foreach ($headers as $header) {
            $e = null;
            $_SERVER[$header] = 'Bearer invalidjwt';
            try {
                $requestReader->getAuthToken();
            } catch (\Exception $e) {
            }
            $this->assertInstanceOf(
                InvalidTokenException::class,
                $e
            );
            unset($_SERVER[$header]);
        }
    }

    public function testGetAuthTokenWithCorrectFormat()
    {
        $requestReader = new RequestReader();
        $headers = [
            'HTTP_AUTHORIZATION',
            'REDIRECT_HTTP_AUTHORIZATION'
        ];
        foreach ($headers as $header) {
            $_SERVER[$header] = 'Bearer ' . self::$token;
            $this->assertInstanceOf(
                Token::class,
                $requestReader->getAuthToken()
            );
            unset($_SERVER[$header]);
        }
    }

    public function testGetGraphQLRequestDataWithEmptyRequest()
    {
        $requestReader = new RequestReader();
        $this->assertEquals(
            [
                'query' => null,
                'variables' => null,
                'operationName' => null
            ],
            $requestReader->getGraphQLRequestData()
        );
    }

    public function testGetGraphQLRequestDataWithInputRequest()
    {
        $requestReader = new RequestReader();
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $this->assertEquals(
            [
                'query' => 'query {token}',
                'variables' => null,
                'operationName' => null
            ],
            $requestReader->getGraphQLRequestData(__DIR__ . '/fixtures/simpleRequest.json')
        );
        unset($_SERVER['CONTENT_TYPE']);
    }
}

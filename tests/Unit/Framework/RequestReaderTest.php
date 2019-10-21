<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Tests\Unit\Framework;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQL\Framework\RequestReaderInterface;
# use PHPUnit\Framework\TestCase;
use OxidEsales\TestingLibrary\UnitTestCase as TestCase;

class RequestReaderTest extends TestCase
{
    protected static $container = null;

    protected static $requestReader = null;

    /**
     * this empty methods prevents phpunit from resetting
     * invocation mocker and therefore we can use the same
     * mocks for all tests and do not need to reinitialize
     * the container for every test in this file which
     * makes the whole thing pretty fast :-)
     */
    protected function verifyMockObjects()
    {
    }

    public function setUp(): void
    {
        if (self::$container !== null) {
            return;
        }

        $containerFactory = new TestContainerFactory();
        self::$container = $containerFactory->create();

        self::$container->compile();

        self::$requestReader = self::$container->get(RequestReaderInterface::class);
    }

    public function tearDown(): void
    {
        Registry::set(Config::class, null);
    }

    public function testGetAuthTokenWithoutToken()
    {
        $this->assertEquals(
            null,
            self::$requestReader->getAuthToken()
        );
    }

    public function testGetAuthTokenWithWrongFormattedHeader()
    {
        $headers = [
            'AUTHORIZATION',
            'HTTP_AUTHORIZATION'
        ];
        foreach ($headers as $header) {
            $_SERVER[$header] = 'authtoken';
            $this->assertEquals(
                null,
                self::$requestReader->getAuthToken()
            );
            unset($_SERVER[$header]);
        }
    }

    public function testGetAuthTokenWithCorrectFormat()
    {
        $headers = [
            'HTTP_AUTHORIZATION',
            'REDIRECT_HTTP_AUTHORIZATION'
        ];
        foreach ($headers as $header) {
            $_SERVER[$header] = 'Bearer authtoken';
            $this->assertEquals(
                'authtoken',
                self::$requestReader->getAuthToken()
            );
            unset($_SERVER[$header]);
        }
    }

    public function testGetGraphQLRequestDataWithEmptyRequest()
    {
        $this->assertEquals(
            [
                'query' => null,
                'variables' => null,
                'operationName' => null
            ],
            self::$requestReader->getGraphQLRequestData()
        );
    }

    public function testGetGraphQLRequestDataWithInputRequest()
    {
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $this->assertEquals(
            [
                'query' => 'query {token}',
                'variables' => null,
                'operationName' => null
            ],
            self::$requestReader->getGraphQLRequestData(__DIR__.'/fixtures/simpleRequest.json')
        );
        unset($_SERVER['CONTENT_TYPE']);
    }
}

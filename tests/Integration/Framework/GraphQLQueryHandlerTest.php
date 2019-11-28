<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Framework;

use OxidEsales\GraphQL\Base\Tests\Integration\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class GraphQLQueryHandlerTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        static::$container = null;
    }

    public static function tearDownAfterClass(): void
    {
        static::$container = null;
    }

    protected static function beforeContainerCompile()
    {
        $loader = new YamlFileLoader(static::$container, new FileLocator());
        $serviceFile = __DIR__ . DIRECTORY_SEPARATOR . 'services.yml';
        $loader->load($serviceFile);
    }

    public function testClientAwareException()
    {
        $result = $this->query('query { clientAwareExceptionQuery(foo: "bar") }');
        $this->assertEquals(
            403,
            $result['status']
        );
        $this->assertEquals(
            'invalid token message',
            $result['body']['errors'][0]['message']
        );
    }

    public function testNotFoundExceptionQuery()
    {
        $result = $this->query('query { notFoundExceptionQuery(foo: "bar") }');
        $this->assertEquals(
            404,
            $result['status']
        );
        $this->assertEquals(
            'Foo does not exist',
            $result['body']['errors'][0]['message']
        );
    }

    public function testExceptionInQuery()
    {
        $result = $this->query('query { exceptionQuery(foo: "bar") }');
        $this->assertEquals(
            400,
            $result['status']
        );
        $this->assertEquals(
            'Internal server error',
            $result['body']['errors'][0]['message']
        );
    }

    public function testQueryWithOperationName()
    {
        $result = $this->query(
            'query fooBar { testQuery(foo: "bar") }',
            null,
            'fooBar'
        );

        $this->assertEquals(
            [
                'status' => 200,
                'body'   => [
                    'data' => [
                        'testQuery' => 'bar'
                    ]
                ]
            ],
            $result
        );
    }

    public function testQueryWithWrongOperationName()
    {
        $result = $this->query(
            'query fooBar { testQuery(foo: "bar") }',
            null,
            'noOp'
        );

        $this->assertEquals(
            400,
            $result['status']
        );
    }

    public function testNonExistantQuery()
    {
        $result = $this->query('query { nonExistant }');
        $this->assertEquals(
            400,
            $result['status']
        );
    }

    public function testInvalidQuery()
    {
        $result = $this->query('FOOBAR');
        $this->assertEquals(
            400,
            $result['status']
        );
    }

    public function testLoggedQuery()
    {
        $result = $this->query('query { token (username: "admin", password: "admin") }');
        $this->setAuthToken($result['body']['data']['token']);

        static::$container = null;
        $this->setUp();

        $result = $this->query('query { testLoggedQuery(foo: "bar") }');
        $this->assertEquals(
            [
                'status' => 200,
                'body'   => [
                    'data' => [
                        'testLoggedQuery' => 'bar'
                    ]
                ]
            ],
            $result
        );
        static::$container = null;
    }

    public function testLoggedRightQuery()
    {
        $result = $this->query('query { token (username: "admin", password: "admin") }');
        $this->setAuthToken($result['body']['data']['token']);

        static::$container = null;
        $this->setUp();

        $result = $this->query('query { testLoggedRightQuery(foo: "bar") }');
        $this->assertEquals(
            [
                'status' => 200,
                'body'   => [
                    'data' => [
                        'testLoggedRightQuery' => 'bar'
                    ]
                ]
            ],
            $result
        );
        static::$container = null;
    }
}

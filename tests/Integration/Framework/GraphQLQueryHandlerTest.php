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

    public function testClientAwareException(): void
    {
        $result = $this->query('query { clientAwareExceptionQuery(foo: "bar") }');

        $this->assertEquals(
            'invalid token message',
            $result['body']['errors'][0]['message']
        );
    }

    public function testNotFoundExceptionQuery(): void
    {
        $result = $this->query('query { notFoundExceptionQuery(foo: "bar") }');

        $this->assertEquals(
            'Foo does not exist',
            $result['body']['errors'][0]['message']
        );
    }

    public function testExceptionInQuery(): void
    {
        $result = $this->query('query { exceptionQuery(foo: "bar") }');

        $this->assertEquals(
            'Internal server error',
            $result['body']['errors'][0]['message']
        );
    }

    public function testQueryWithOperationName(): void
    {
        $result = $this->query(
            'query fooBar { testQuery(foo: "bar") }',
            null,
            'fooBar'
        );

        $this->assertEquals(
            [
                'body'   => [
                    'data' => [
                        'testQuery' => 'bar',
                    ],
                ],
            ],
            $result
        );
    }

    public function testQueryWithWrongOperationName(): void
    {
        $result = $this->query(
            'query fooBar { testQuery(foo: "bar") }',
            null,
            'noOp'
        );

        $this->assertNotEmpty($result['body']['errors']);
    }

    public function testNonExistantQuery(): void
    {
        $result = $this->query('query { nonExistant }');

        $this->assertNotEmpty($result['body']['errors']);
    }

    public function testInvalidQuery(): void
    {
        $result = $this->query('FOOBAR');

        $this->assertNotEmpty($result['body']['errors']);
    }

    public function testLoggedQuery(): void
    {
        $result = $this->query('query { token (username: "admin", password: "admin") }');
        $this->setAuthToken($result['body']['data']['token']);

        static::$container = null;
        $this->setUp();

        $result = $this->query('query { testLoggedQuery(foo: "bar") }');
        $this->assertEquals(
            [
                'body'   => [
                    'data' => [
                        'testLoggedQuery' => 'bar',
                    ],
                ],
            ],
            $result
        );
        static::$container = null;
    }

    public function testLoggedRightQuery(): void
    {
        $result = $this->query('query { token (username: "admin", password: "admin") }');
        $this->setAuthToken($result['body']['data']['token']);

        static::$container = null;
        $this->setUp();

        $result = $this->query('query { testLoggedRightQuery(foo: "bar") }');
        $this->assertEquals(
            [
                'body'   => [
                    'data' => [
                        'testLoggedRightQuery' => 'bar',
                    ],
                ],
            ],
            $result
        );
        static::$container = null;
    }

    public function testLoggedButNoRightQuery(): void
    {
        $result = $this->query('query { token (username: "admin", password: "admin") }');
        $this->setAuthToken($result['body']['data']['token']);

        static::$container = null;
        $this->setUp();

        $result = $this->query('query { testLoggedButNoRightQuery(foo: "bar") }');
        $this->assertNotEmpty($result['body']['errors']);
        static::$container = null;
    }

    public function testRightOnlyQueryWithoutToken(): void
    {
        static::$container = null;
        $this->setUp();

        $result = $this->query('query { testOnlyRightQuery(foo: "bar") }');
        $this->assertNotEmpty($result['body']['errors']);
        static::$container = null;
    }

    public function testRightOnlyQueryWithUserToken(): void
    {
        $result = $this->query('query { token (username: "admin", password: "admin") }');
        $this->setAuthToken($result['body']['data']['token']);

        static::$container = null;
        $this->setUp();

        $result = $this->query('query { testOnlyRightQuery(foo: "bar") }');
        $this->assertNotEmpty($result['body']['errors']);
        static::$container = null;
    }

    public function testRightOnlyQueryWithAnonymousToken(): void
    {
        $result = $this->query('query { token }');
        $this->setAuthToken($result['body']['data']['token']);

        static::$container = null;
        $this->setUp();

        $result = $this->query('query { testOnlyRightQuery(foo: "bar") }');

        $this->assertEquals(
            [
                'body'   => [
                    'data' => [
                        'testOnlyRightQuery' => 'bar',
                    ],
                ],
            ],
            $result
        );
        static::$container = null;
    }

    public function testBasicInputFilterQuery(): void
    {
        $result = $this->query('
            query {
                basicInputFilterQuery(filter: {
                    active: {
                        equals: true
                    }
                    price: {
                        lessThan: 19.99
                    }
                    stock: {
                        greaterThan: 10
                    }
                    title: {
                        contains: "foo"
                    }
                })
            }
        ');

        $this->assertNotEmpty($result['body']['data']['basicInputFilterQuery']);
    }

    public function testBasicSortingQuery(): void
    {
        $result = $this->query('
            query {
                basicSortingQuery (sort: {
                    title: "ASC"
                    price: "ASC"
                })
            }
        ');

        $this->assertNotEmpty($result['body']['data']['basicSortingQuery']);
    }

    public function testResultWithError(): void
    {
        $result = $this->query('
            query {
                resultWithError
            }
        ');
        $this->assertEquals(
            [
                'body'   => [
                    'data' => [
                        'resultWithError' => true,
                    ],
                    'errors' => [
                        [
                            'message'    => 'error message',
                            'extensions' => [
                                'category' => 'graphql',
                            ],
                        ],
                    ],
                ],
            ],
            $result
        );
    }

    protected static function beforeContainerCompile(): void
    {
        $loader      = new YamlFileLoader(static::$container, new FileLocator());
        $serviceFile = __DIR__ . DIRECTORY_SEPARATOR . 'services.yml';
        $loader->load($serviceFile);
    }
}

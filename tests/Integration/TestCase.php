<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Tests\Integration;

use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQL\Framework\GraphQLQueryHandlerInterface;
use OxidEsales\GraphQL\Framework\RequestReader;
use OxidEsales\GraphQL\Framework\RequestReaderInterface;
use OxidEsales\GraphQL\Framework\ResponseWriter;
use OxidEsales\GraphQL\Framework\ResponseWriterInterface;
use OxidEsales\GraphQL\Service\KeyRegistry;
use OxidEsales\GraphQL\Service\KeyRegistryInterface;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Log\LoggerInterface;

abstract class TestCase extends PHPUnitTestCase
{
    protected static $queryResult = null;
    protected static $logResult = null;
    protected static $container = null;
    protected static $token = null;
    protected static $query = null;

    public static function responseCallback($body, $status)
    {
        self::$queryResult = [
            'status' => $status,
            'body' => $body
        ];
    }

    public static function loggerCallback(string $message)
    {
        self::$logResult .= $message;
    }

    public static function getAuthToken(): ?string
    {
        if (self::$token === null) {
            return null;
        }
        return (string) self::$token;
    }

    public static function getGraphQLRequestData(): array
    {
        if (self::$query === null) {
            return [];
        }
        return self::$query;
    }

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

    protected function tearDown(): void
    {
        static::$queryResult = null;
        static::$logResult = null;
        static::$query = null;
        static::$token = null;
    }

    protected function setUp(): void
    {
        if (self::$container !== null) {
            return;
        }
        $containerFactory = new TestContainerFactory();
        self::$container = $containerFactory->create();

        $responseWriter = $this->getMockBuilder(ResponseWriterInterface::class)->getMock();
        $responseWriter->method('renderJsonResponse')
                       ->willReturnCallback([
                           TestCase::class,
                           'responseCallback'
                       ]);
        self::$container->set(
            ResponseWriterInterface::class,
            $responseWriter
        );
        self::$container->autowire(
            ResponseWriterInterface::class,
            ResponseWriter::class
        );

        $requestReader = $this->getMockBuilder(RequestReaderInterface::class)->getMock();
        $requestReader->method('getAuthToken')
                      ->willReturnCallback([
                           TestCase::class,
                          'getAuthToken'
                      ]);
        $requestReader->method('getGraphQLRequestData')
                      ->willReturnCallback([
                           TestCase::class,
                          'getGraphQLRequestData'
                      ]);
        self::$container->set(
            RequestReaderInterface::class,
            $requestReader
        );
        self::$container->autowire(
            RequestReaderInterface::class,
            RequestReader::class
        );

        $keyRegistry = $this->getMockBuilder(KeyRegistryInterface::class)->getMock();
        $keyRegistry->method('getSignatureKey')
                    ->willReturn(base64_encode(random_bytes(64)));
        self::$container->set(
            KeyRegistryInterface::class,
            $keyRegistry
        );
        self::$container->autowire(
            KeyRegistryInterface::class,
            KeyRegistry::class
        );

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger->method('error')
               ->willReturnCallback([
                   TestCase::class,
                   'loggerCallback'
               ]);
        self::$container->set(
            LoggerInterface::class,
            $logger
        );
        self::$container->autowire(
            LoggerInterface::class,
            get_class($logger)
        );

        self::$container->compile();
    }

    protected function execQuery(string $query, array $variables = null, string $operationName = null)
    {
        self::$query = [
            'query' => $query,
            'variables' => $variables,
            'operationName' => $operationName
        ];
        self::$container->get(GraphQLQueryHandlerInterface::class)
                        ->executeGraphQLQuery();
    }
}

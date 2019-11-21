<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration;

use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQL\Base\Framework\GraphQLQueryHandlerInterface;
use OxidEsales\GraphQL\Base\Framework\RequestReader;
use OxidEsales\GraphQL\Base\Framework\RequestReaderInterface;
use OxidEsales\GraphQL\Base\Framework\ResponseWriter;
use OxidEsales\GraphQL\Base\Framework\ResponseWriterInterface;
use OxidEsales\GraphQL\Base\Service\AuthenticationServiceInterface;
use OxidEsales\GraphQL\Base\Service\AuthorizationServiceInterface;
use OxidEsales\GraphQL\Base\Service\KeyRegistry;
use OxidEsales\GraphQL\Base\Service\KeyRegistryInterface;
#use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use OxidEsales\TestingLibrary\UnitTestCase as PHPUnitTestCase;
use Psr\Log\LoggerInterface;

abstract class TestCase extends PHPUnitTestCase
{
    protected static $queryResult = null;
    protected static $logResult = null;
    protected static $container = null;
    protected static $query = null;

    public static function responseCallback($body, $status)
    {
        static::$queryResult = [
            'status' => $status,
            'body' => $body
        ];
    }

    public static function loggerCallback(string $message)
    {
        static::$logResult .= $message;
    }

    public static function getGraphQLRequestData(): array
    {
        if (static::$query === null) {
            return [];
        }
        return static::$query;
    }

    protected function tearDown(): void
    {
        static::$queryResult = null;
        static::$logResult = null;
        static::$query = null;
        unset($_SERVER['HTTP_AUTHORIZATION']);
    }

    protected function setUp(): void
    {
        $containerFactory = new TestContainerFactory();
        static::$container = $containerFactory->create();

        $responseWriter = $this->getMockBuilder(ResponseWriterInterface::class)
                               ->getMock();
        $responseWriter->method('renderJsonResponse')
                       ->willReturnCallback([
                           TestCase::class,
                           'responseCallback'
                       ]);
        static::$container->set(
            ResponseWriterInterface::class,
            $responseWriter
        );
        static::$container->autowire(
            ResponseWriterInterface::class,
            ResponseWriter::class
        );

        $requestReader = $this->getMockBuilder(RequestReader::class)
                              ->setMethods(['getGraphQLRequestData'])
                              ->getMock();
        $requestReader->method('getGraphQLRequestData')
                      ->willReturnCallback([
                           TestCase::class,
                          'getGraphQLRequestData'
                      ]);
        static::$container->set(
            RequestReaderInterface::class,
            $requestReader
        );
        static::$container->autowire(
            RequestReaderInterface::class,
            RequestReader::class
        );

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger->method('error')
               ->willReturnCallback([
                   TestCase::class,
                   'loggerCallback'
               ]);
        static::$container->set(
            LoggerInterface::class,
            $logger
        );
        static::$container->autowire(
            LoggerInterface::class,
            get_class($logger)
        );

        static::beforeContainerCompile();

        static::$container->compile();
    }

    protected static function beforeContainerCompile()
    {
    }

    protected function setAuthToken(string $token)
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;
        $token = static::$container->get(RequestReaderInterface::class)
                                   ->getAuthToken();
        static::$container->get(AuthenticationServiceInterface::class)
                          ->setToken($token);
        static::$container->get(AuthorizationServiceInterface::class)
                          ->setToken($token);
    }

    protected function execQuery(string $query, array $variables = null, string $operationName = null)
    {
        static::$query = [
            'query' => $query,
            'variables' => $variables,
            'operationName' => $operationName
        ];
        static::$container->get(GraphQLQueryHandlerInterface::class)
                          ->executeGraphQLQuery();
    }
}

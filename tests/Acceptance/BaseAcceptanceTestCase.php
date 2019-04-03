<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Acceptance;

use GraphQL\Executor\ExecutionResult;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Framework\GraphQlQueryHandlerInterface;
use OxidEsales\GraphQl\Framework\RequestReader;
use OxidEsales\GraphQl\Framework\RequestReaderInterface;
use OxidEsales\GraphQl\Framework\ResponseWriter;
use OxidEsales\GraphQl\Framework\ResponseWriterInterface;
use OxidEsales\GraphQl\Service\EnvironmentServiceInterface;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;
use OxidEsales\TestingLibrary\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\DependencyInjection\Container;

class BaseAcceptanceTestCase extends UnitTestCase
{

    /** @var  RequestReaderInterface|MockObject */
    private $requestReader;

    /** @var  ResponseWriterInterface|MockObject */
    private $responseWriter;

    /** @var  ExecutionResult */
    protected $queryResult;

    /** @var int */
    protected $httpStatus;

    /** @var Container */
    private $container;

    public function responseCallback($result, $httpStatus)
    {
        $this->queryResult = $result;
        $this->httpStatus = $httpStatus;
    }

    public function setUp()
    {
        $containerFactory = new TestContainerFactory();
        $this->container = $containerFactory->create();
        $this->requestReader = $this->getMockBuilder(RequestReaderInterface::class)->getMock();
        $this->responseWriter = $this->getMockBuilder(ResponseWriterInterface::class)->getMock();
        $this->container->set(RequestReaderInterface::class, $this->requestReader);
        $this->container->autowire(RequestReaderInterface::class, RequestReader::class);
        $this->container->set(ResponseWriterInterface::class, $this->responseWriter);
        $this->container->autowire(ResponseWriterInterface::class, ResponseWriter::class);
        $this->container->compile();
    }

    public function executeQuery($query, $userGroup='anonymous')
    {
        /** @var KeyRegistryInterface $keyRegistry */
        $keyRegistry = $this->container->get(KeyRegistryInterface::class);
        /** @var EnvironmentServiceInterface $environmentService */
        $environmentService = $this->container->get(EnvironmentServiceInterface::class);
        $token = new Token();
        $token->setUserGroup($userGroup);
        $token->setKey('somekey');
        $token->setSubject($environmentService->getShopUrl());
        $token->setShopUrl($environmentService->getShopUrl());
        $token->setLang($environmentService->getDefaultLanguage());
        $token->setShopid($environmentService->getDefaultShopId());

        $authHeader = 'Bearer ' . $token->getJwt($keyRegistry->getSignatureKey());

        $this->requestReader->method('getAuthorizationHeader')->willReturn($authHeader);
        $this->requestReader->method('getGraphQLRequestData')->willReturn(['query' => $query]);
        $this->responseWriter->method('renderJsonResponse')->willReturnCallback([$this, 'responseCallback']);

        $queryHandler = $this->container->get(GraphQlQueryHandlerInterface::class);
        $queryHandler->executeGraphQlQuery();

    }
}

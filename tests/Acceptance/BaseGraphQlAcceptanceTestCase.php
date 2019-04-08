<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Tests\Acceptance;

use GraphQL\Executor\ExecutionResult;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Exception\NoAuthHeaderException;
use OxidEsales\GraphQl\Framework\GraphQlQueryHandlerInterface;
use OxidEsales\GraphQl\Framework\RequestReader;
use OxidEsales\GraphQl\Framework\RequestReaderInterface;
use OxidEsales\GraphQl\Framework\ResponseWriter;
use OxidEsales\GraphQl\Framework\ResponseWriterInterface;
use OxidEsales\GraphQl\Service\EnvironmentServiceInterface;
use OxidEsales\GraphQl\Service\KeyRegistryInterface;
use OxidEsales\TestingLibrary\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Container;

class BaseGraphQlAcceptanceTestCase extends UnitTestCase
{

    /** @var  RequestReaderInterface|MockObject */
    private $requestReader;

    /** @var  ResponseWriterInterface|MockObject */
    private $responseWriter;

    /** @var  EnvironmentServiceInterface */
    private $environmentService;

    /** @var  ExecutionResult */
    protected $queryResult;

    /** @var int */
    protected $httpStatus;

    /** @var  string */
    protected $logResult = "";

    /** @var Container */
    protected $container;

    public function responseCallback($result, $httpStatus)
    {
        $this->queryResult = $result;
        $this->httpStatus = $httpStatus;
    }

    public function loggerCallback(string $logmessage)
    {
        $this->logResult .= $logmessage;
    }

    public function setUp()
    {
        $this->queryResult = null;
        $this->httpStatus = null;
        $this->logResult = "";

        $containerFactory = new TestContainerFactory();
        $this->container = $containerFactory->create();
        $this->requestReader = $this->getMockBuilder(RequestReaderInterface::class)->getMock();
        $this->responseWriter = $this->getMockBuilder(ResponseWriterInterface::class)->getMock();
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger->method('error')->willReturnCallback([$this, 'loggerCallback']);
        $this->container->set(RequestReaderInterface::class, $this->requestReader);
        $this->container->autowire(RequestReaderInterface::class, RequestReader::class);
        $this->container->set(ResponseWriterInterface::class, $this->responseWriter);
        $this->container->autowire(ResponseWriterInterface::class, ResponseWriter::class);
        $this->container->set(LoggerInterface::class, $logger);
        $this->container->autowire(LoggerInterface::class, get_class($logger));
        $this->container->compile();
        $this->environmentService = $this->container->get(EnvironmentServiceInterface::class);
    }

    public function executeQuery($query, $userGroup='anonymous')
    {
        if (is_null($userGroup)) {
            $this->requestReader->method('getAuthorizationHeader')
                ->willThrowException(new NoAuthHeaderException());
        }
        else {
            $this->requestReader->method('getAuthorizationHeader')
                ->willReturn($this->createAuthHeader($userGroup));
        }
        $this->requestReader->method('getGraphQLRequestData')->willReturn(['query' => $query]);
        $this->responseWriter->method('renderJsonResponse')->willReturnCallback([$this, 'responseCallback']);

        $queryHandler = $this->container->get(GraphQlQueryHandlerInterface::class);
        $queryHandler->executeGraphQlQuery();

    }

    private function createAuthHeader($userGroup)
    {
        /** @var KeyRegistryInterface $keyRegistry */
        $keyRegistry = $this->container->get(KeyRegistryInterface::class);

        $token = new Token();
        $token->setUserGroup($userGroup);
        $token->setKey('somekey');
        $token->setSubject($this->environmentService->getShopUrl());
        $token->setShopUrl($this->environmentService->getShopUrl());
        $token->setLang($this->getLang());
        $token->setShopid($this->getShopId());

        return 'Bearer ' . $token->getJwt($keyRegistry->getSignatureKey());

    }

    public function getShopId() {
        return $this->environmentService->getDefaultShopId();
    }

    public function getLang() {
        return $this->environmentService->getDefaultLanguage();
    }
}

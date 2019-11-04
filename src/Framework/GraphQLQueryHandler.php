<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Framework;

use GraphQL\Error\Error;
use GraphQL\Error\FormattedError;
use Psr\Log\LoggerInterface;

class GraphQLQueryHandler implements GraphQLQueryHandlerInterface
{

    /** @var LoggerInterface  */
    private $logger;

    /** @var SchemaFactoryInterface  */
    private $schemaFactory;

    /** @var ErrorCodeProviderInterface  */
    private $errorCodeProvider;

    /** @var  RequestReaderInterface */
    private $requestReader;

    /** @var  ResponseWriterInterface */
    private $responseWriter;

    private $loggingErrorFormatter;

    public function __construct(
        LoggerInterface $logger,
        SchemaFactoryInterface $schemaFactory,
        ErrorCodeProviderInterface $errorCodeProvider,
        RequestReaderInterface $requestReader,
        ResponseWriterInterface $responseWriter
    ) {
        $this->logger = $logger;
        $this->schemaFactory = $schemaFactory;
        $this->errorCodeProvider = $errorCodeProvider;
        $this->requestReader = $requestReader;
        $this->responseWriter = $responseWriter;

        $this->loggingErrorFormatter = function (Error $error) {
            $this->logger->error((string)$error);
            return FormattedError::createFromException($error);
        };
    }

    public function executeGraphQLQuery()
    {
        $result = $this->executeQuery(
            $this->requestReader->getGraphQLRequestData()
        );
        $httpStatus = $this->errorCodeProvider->getHttpReturnCode($result);
        $result->setErrorFormatter($this->loggingErrorFormatter);
        $this->responseWriter->renderJsonResponse(
            $result->toArray(true),
            $httpStatus
        );
    }

    /**
     * Execute the GraphQL query
     *
     * @throws \Throwable
     */
    private function executeQuery($queryData)
    {
        $graphQL = new \GraphQL\GraphQL();
        $variables = null;
        if (isset($queryData['variables'])) {
            $variables = $queryData['variables'];
        }
        $operationName = null;
        if (isset($queryData['operationName'])) {
            $operationName = $queryData['operationName'];
        }

        $result = $graphQL->executeQuery(
            $this->schemaFactory->getSchema(),
            $queryData['query'],
            null,
            null,
            $variables,
            $operationName
        );
        return $result;
    }
}

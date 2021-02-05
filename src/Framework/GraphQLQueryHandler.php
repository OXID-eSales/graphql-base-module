<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

use GraphQL\Error\Error;
use GraphQL\Error\FormattedError;
use GraphQL\Executor\ExecutionResult;
use Psr\Log\LoggerInterface;
use Throwable;

class GraphQLQueryHandler
{
    /** @var Error[] */
    private static $errors = [];

    /** @var LoggerInterface */
    private $logger;

    /** @var SchemaFactory */
    private $schemaFactory;

    /** @var ErrorCodeProvider */
    private $errorCodeProvider;

    /** @var RequestReader */
    private $requestReader;

    /** @var ResponseWriter */
    private $responseWriter;

    /** @var TimerHandler */
    private $timerHandler;

    private $loggingErrorFormatter;

    public function __construct(
        LoggerInterface $logger,
        SchemaFactory $schemaFactory,
        ErrorCodeProvider $errorCodeProvider,
        RequestReader $requestReader,
        ResponseWriter $responseWriter,
        TimerHandler $timerHandler
    ) {
        $this->logger            = $logger;
        $this->schemaFactory     = $schemaFactory;
        $this->errorCodeProvider = $errorCodeProvider;
        $this->requestReader     = $requestReader;
        $this->responseWriter    = $responseWriter;
        $this->timerHandler      = $timerHandler;

        $this->loggingErrorFormatter = function (Error $error) {
            $this->logger->error((string) $error);

            return FormattedError::createFromException($error);
        };
    }

    public function executeGraphQLQuery(): void
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
     * @param array{query: string, variables: string[], operationName: string} $queryData
     *
     * @throws Throwable
     */
    private function executeQuery(array $queryData): ExecutionResult
    {
        $graphQL       = new \GraphQL\GraphQL();
        $variables     = null;
        $operationName = null;

        if (isset($queryData['variables'])) {
            $variables = $queryData['variables'];
        }

        if (isset($queryData['operationName'])) {
            $operationName = $queryData['operationName'];
        }

        $schemaTimer = $this->timerHandler->create('schema')->start();
        $schemaTimer->stop();

        $queryTimer = $this->timerHandler->create('query')->start();

        $result = $graphQL->executeQuery(
            $this->schemaFactory->getSchema(),
            $queryData['query'],
            null,
            null,
            $variables,
            $operationName
        );

        $queryTimer->stop();

        $result->errors = array_merge(
            $result->errors,
            self::$errors
        );

        return $result;
    }

    public static function addError(Error $e): void
    {
        self::$errors[] = $e;
    }
}

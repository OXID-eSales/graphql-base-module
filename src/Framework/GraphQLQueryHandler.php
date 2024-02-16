<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

use GraphQL\Error\DebugFlag;
use GraphQL\Error\Error;
use GraphQL\Error\FormattedError;
use GraphQL\Executor\ExecutionResult;
use GraphQL\GraphQL;
use Psr\Log\LoggerInterface;
use Throwable;

class GraphQLQueryHandler
{
    /** @var Error[] */
    private static array $errors = [];

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SchemaFactory $schemaFactory,
        private readonly RequestReader $requestReader,
        private readonly ResponseWriter $responseWriter,
        private readonly TimerHandler $timerHandler
    ) {
    }

    public function executeGraphQLQuery(): void
    {
        $result = $this->executeQuery(
            $this->requestReader->getGraphQLRequestData()
        );
        $result->setErrorFormatter($this->getErrors());
        $this->responseWriter->renderJsonResponse(
            $result->toArray(DebugFlag::INCLUDE_DEBUG_MESSAGE)
        );
    }

    /**
     * Execute the GraphQL query
     *
     * @param array{query: string, variables?: string[], operationName?: string} $queryData
     *
     * @throws Throwable
     */
    private function executeQuery(array $queryData): ExecutionResult
    {
        $graphQL = new GraphQL();
        $variables = null;
        $operationName = null;

        if (isset($queryData['variables'])) {
            $variables = $queryData['variables'];
        }

        if (isset($queryData['operationName'])) {
            $operationName = $queryData['operationName'];
        }

        $schema = $this->schemaFactory->getSchema();

        $queryTimer = $this->timerHandler->create('query-exec')->start();

        $result = $graphQL->executeQuery(
            $schema,
            $queryData['query'],
            null,
            null,
            $variables,
            $operationName
        );

        $result->errors = array_merge(
            $result->errors,
            self::$errors
        );

        $queryTimer->stop();

        return $result;
    }

    public static function addError(Error $error): void
    {
        self::$errors[] = $error;
    }

    private function getErrors(): \Closure
    {
        return function (Error $error) {
            $this->logger->error((string)$error);

            return FormattedError::createFromException($error);
        };
    }
}

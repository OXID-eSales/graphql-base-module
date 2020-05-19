<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

use GraphQL\Error\InvariantViolation;
use GraphQL\Executor\ExecutionResult;
use OxidEsales\GraphQL\Base\Exception\HttpErrorInterface;

use function get_class;

/**
 * Class ErrorCodeProvider
 *
 * Tries to map the (first) error in the GraphQL
 * execution result to a http status code.
 *
 * TODO: Think of something more sophisticated
 */
class ErrorCodeProvider
{
    public function getHttpReturnCode(ExecutionResult $result): int
    {
        // TODO: The problem is, that the exceptions are already
        // transformed to a GraphQL error when we receive the
        // result. So we would need take the message to determine
        // which is the correct http status.

        if (count($result->errors) == 0) {
            return 200; // OK
        }

        $error = $result->errors[0];

        $previous = $error->getPrevious();

        if ($previous instanceof HttpErrorInterface) {
            return $previous->getHttpStatus();
        }

        $errorClass = get_class($error);

        switch ($errorClass) {
            case InvariantViolation::class:
                return 500; // Internal server error
        }

        return 400; // Bad request
    }
}

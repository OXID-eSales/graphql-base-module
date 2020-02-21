<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Base\Framework;

use GraphQL\Executor\ExecutionResult;

/**
 * @deprecated use ErrorCodeProvider
 */
interface ErrorCodeProviderInterface
{
    public function getHttpReturnCode(ExecutionResult $result): int;
}

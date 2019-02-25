<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Framework;

use GraphQL\Error\Error;

class ErrorCodeProvider implements ErrorCodeProviderInterface
{
    public function getHttpReturnCode(Error $error): int
    {
        return 500;
    }

}

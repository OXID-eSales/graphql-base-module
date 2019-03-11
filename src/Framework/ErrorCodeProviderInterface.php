<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 20.02.19
 * Time: 14:20
 */

namespace OxidEsales\GraphQl\Framework;

use GraphQL\Executor\ExecutionResult;

interface ErrorCodeProviderInterface
{
    public function getHttpReturnCode(ExecutionResult $result): int;
}
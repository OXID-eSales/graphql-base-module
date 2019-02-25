<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 20.02.19
 * Time: 14:20
 */

namespace OxidEsales\GraphQl\Framework;

use GraphQL\Error\Error;

interface ErrorCodeProviderInterface
{
    public function getHttpReturnCode(Error $error): int;
}
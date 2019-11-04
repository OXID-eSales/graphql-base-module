<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Tests\Integration\Framework\Controller;

use OxidEsales\GraphQL\Exception\InvalidTokenException;
use TheCodingMachine\GraphQLite\Annotations\Query;

class TestController
{
    /**
     * @Query
     */
    public function testQuery(string $foo): string
    {
        return $foo;
    }

    /**
     * @Query
     */
    public function exceptionQuery(string $foo): string
    {
        throw new \Exception();
    }

    /**
     * @Query
     */
    public function clientAwareExceptionQuery(string $foo): string
    {
        throw new InvalidTokenException('invalid token message');
    }
}

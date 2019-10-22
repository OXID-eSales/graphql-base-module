<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Tests\Integration\Framework\Controller;

use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use OxidEsales\GraphQL\Exception\InvalidTokenException;

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

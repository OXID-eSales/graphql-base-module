<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Framework\Controller;

use OxidEsales\GraphQL\Base\Exception\InvalidTokenException;
use OxidEsales\GraphQL\Base\Exception\NotFoundException;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Right;

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
     * @Logged
     */
    public function testLoggedQuery(string $foo): string
    {
        return $foo;
    }

    /**
     * @Query
     * @Logged
     * @Right("FOOBAR")
     */
    public function testLoggedRightQuery(string $foo): string
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

    /**
     * @Query
     */
    public function notFoundExceptionQuery(string $foo): string
    {
        throw new NotFoundException('Foo does not exist');
    }
}

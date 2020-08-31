<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Framework\Controller;

use Exception;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Tests\Integration\Framework\DataType\TestFilter;
use OxidEsales\GraphQL\Base\Tests\Integration\Framework\DataType\TestSorting;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
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
     * @Logged
     * @Right("BARFOO")
     */
    public function testLoggedButNoRightQuery(string $foo): string
    {
        return $foo;
    }

    /**
     * @Query
     */
    public function exceptionQuery(string $foo): string
    {
        throw new Exception();
    }

    /**
     * @Query
     */
    public function clientAwareExceptionQuery(string $foo): string
    {
        throw new InvalidToken('invalid token message');
    }

    /**
     * @Query
     */
    public function notFoundExceptionQuery(string $foo): string
    {
        throw new NotFound('Foo does not exist');
    }

    /**
     * @Query
     */
    public function basicInputFilterQuery(TestFilter $filter): string
    {
        return (string) $filter;
    }

    /**
     * @Query
     */
    public function basicSortingQuery(?TestSorting $sort = null): bool
    {
        return true;
    }
}

<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\DataType\IntegerFilter;
use PHPUnit\Framework\TestCase;

class IntegerFilterTest extends TestCase
{
    public function testThrowsExceptionOnNoInput()
    {
        $this->expectException(\Exception::class);
        IntegerFilter::fromUserInput();
    }

    public function invalidBetweens(): array
    {
        return [
            [
                []
            ], [
                [1]
            ], [
                [null, 1]
            ], [
                [1, null]
            ], [
                [1, 2, 3]
            ],
        ];
    }

    /**
     * @dataProvider invalidBetweens
     */
    public function testThrowsExceptionOnInvalidBetween(
        array $between
    ) {
        $this->expectException(\Exception::class);
        IntegerFilter::fromUserInput(
            null,
            null,
            null,
            $between
        );
    }
}

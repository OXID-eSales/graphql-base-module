<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\DataType\FloatFilter;
use PHPUnit\Framework\TestCase;

class FloatFilterTest extends TestCase
{
    public function testThrowsExceptionOnNoInput()
    {
        $this->expectException(\Exception::class);
        FloatFilter::fromUserInput();
    }

    public function invalidBetweens(): array
    {
        return [
            [
                []
            ], [
                [1.0 ]
            ], [
                [null, 1.0]
            ], [
                [1.0, null]
            ], [
                [1.0, 2.0, 3.0]
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
        FloatFilter::fromUserInput(
            null,
            null,
            null,
            $between
        );
    }
}

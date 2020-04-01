<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use Exception;
use OxidEsales\GraphQL\Base\DataType\IntegerFilter;
use PHPUnit\Framework\TestCase;

class IntegerFilterTest extends TestCase
{
    public function testThrowsExceptionOnNoInput(): void
    {
        $this->expectException(Exception::class);
        IntegerFilter::fromUserInput();
    }

    public function testGivesEquals(): void
    {
        $this->assertSame(
            2,
            (IntegerFilter::fromUserInput(
                2
            ))->equals()
        );
    }

    public function testGivesLowerThen(): void
    {
        $this->assertSame(
            2,
            (IntegerFilter::fromUserInput(
                null,
                2
            ))->lowerThen()
        );
    }

    public function testGivesGreaterThen(): void
    {
        $this->assertSame(
            2,
            (IntegerFilter::fromUserInput(
                null,
                null,
                2
            ))->greaterThen()
        );
    }

    public function testGivesParametersIfSet(): void
    {
        $filter = IntegerFilter::fromUserInput(
            5,
            10,
            1,
            [
                0,
                10,
            ]
        );

        $this->assertSame(
            5,
            $filter->equals()
        );
        $this->assertSame(
            10,
            $filter->lowerThen()
        );
        $this->assertSame(
            1,
            $filter->greaterThen()
        );
        $this->assertSame(
            [
                0,
                10,
            ],
            $filter->between()
        );
    }

    public function invalidBetweens(): array
    {
        return [
            [
                [],
            ], [
                [1],
            ], [
                [null, 1],
            ], [
                [1, null],
            ], [
                [1, 2, 3],
            ],
        ];
    }

    /**
     * @dataProvider invalidBetweens
     */
    public function testThrowsExceptionOnInvalidBetween(
        array $between
    ): void {
        $this->expectException(Exception::class);
        IntegerFilter::fromUserInput(
            null,
            null,
            null,
            $between
        );
    }
}

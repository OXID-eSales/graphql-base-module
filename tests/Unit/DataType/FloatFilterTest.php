<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use Exception;
use OxidEsales\GraphQL\Base\DataType\FloatFilter;
use PHPUnit\Framework\TestCase;

class FloatFilterTest extends TestCase
{
    public function testThrowsExceptionOnNoInput(): void
    {
        $this->expectException(Exception::class);
        FloatFilter::fromUserInput();
    }

    public function testGivesEquals(): void
    {
        $this->assertSame(
            2.0,
            (FloatFilter::fromUserInput(
                2.0
            ))->equals()
        );
    }

    public function testGivesLowerThen(): void
    {
        $this->assertSame(
            2.0,
            (FloatFilter::fromUserInput(
                null,
                2.0
            ))->lowerThen()
        );
    }

    public function testGivesGreaterThen(): void
    {
        $this->assertSame(
            2.0,
            (FloatFilter::fromUserInput(
                null,
                null,
                2.0
            ))->greaterThen()
        );
    }

    public function testGivesParametersIfSet(): void
    {
        $filter = FloatFilter::fromUserInput(
            5.0,
            10.0,
            1.0,
            [
                1.0,
                10.0,
            ]
        );

        $this->assertSame(
            5.0,
            $filter->equals()
        );
        $this->assertSame(
            10.0,
            $filter->lowerThen()
        );
        $this->assertSame(
            1.0,
            $filter->greaterThen()
        );
        $this->assertSame(
            [
                1.0,
                10.0,
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
                [1.0],
            ], [
                [null, 1.0],
            ], [
                [1.0, null],
            ], [
                [1.0, 2.0, 3.0],
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
        FloatFilter::fromUserInput(
            null,
            null,
            null,
            $between
        );
    }
}

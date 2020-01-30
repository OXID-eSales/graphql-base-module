<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\DataType\DateFilter;
use PHPUnit\Framework\TestCase;

class DateFilterTest extends TestCase
{
    public function testThrowsExceptionOnNoInput()
    {
        $this->expectException(\Exception::class);
        DateFilter::fromUserInput();
    }

    public function invalidBetweens(): array
    {
        return [
            [
                []
            ], [
                [null, null, null]
            ], [
                ['foobar', null]
            ], [
                ['foobar', 'baz']
            ]
        ];
    }

    /**
     * @dataProvider invalidBetweens
     */
    public function testThrowsExceptionOnInvalidBetween(
        array $between
    ) {
        $this->expectException(\Exception::class);
        DateFilter::fromUserInput(
            null,
            $between
        );
    }

    public function testThrowsExceptionOnInvalidEquals()
    {
        $this->expectException(\Exception::class);
        DateFilter::fromUserInput(
            'foobar'
        );
    }

    public function testBasicDateFilter()
    {
        $filter = DateFilter::fromUserInput(
            '2020-01-30 12:37:21'
        );
        $this->assertSame(
            '2020-01-30T12:37:21+0000',
            $filter->equals()->format(\DateTimeInterface::ISO8601)
        );

        $filter = DateFilter::fromUserInput(
            '2020-01-30 12:37:21 CET'
        );
        $this->assertSame(
            '2020-01-30T12:37:21+0100',
            $filter->equals()->format(\DateTimeInterface::ISO8601)
        );

        $filter = DateFilter::fromUserInput(
            null,
            [
                '2020-01-30 12:37:21',
                '2020-01-30 12:37:22'
            ]
        );
        $this->assertSame(
            [
                '2020-01-30T12:37:21+0000',
                '2020-01-30T12:37:22+0000'
            ],
            [
                $filter->between()[0]->format(\DateTimeInterface::ISO8601),
                $filter->between()[1]->format(\DateTimeInterface::ISO8601)
            ]
        );
    }
}

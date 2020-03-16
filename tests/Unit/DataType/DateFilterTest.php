<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use DateTime;
use Exception;
use OutOfBoundsException;
use OxidEsales\GraphQL\Base\DataType\DateFilter;
use PHPUnit\Framework\TestCase;

class DateFilterTest extends TestCase
{
    public function testThrowsExceptionOnNoInput(): void
    {
        $this->expectException(Exception::class);
        DateFilter::fromUserInput();
    }

    public function invalidBetweens(): array
    {
        return [
            [
                [],
            ], [
                [null, null, null],
            ], [
                ['foobar', null],
            ], [
                [null, 'foobar'],
            ], [
                [null, null],
            ],
        ];
    }

    /**
     * @dataProvider invalidBetweens
     */
    public function testThrowsExceptionOnInvalidBetween(
        array $between
    ): void {
        $this->expectException(OutOfBoundsException::class);
        DateFilter::fromUserInput(
            null,
            $between
        );
    }

    public function testThrowExceptionOnInvalidDateTimeBetween(): void
    {
        $this->expectException(Exception::class);
        DateFilter::fromUserInput(
            null,
            ['foo', 'bar']
        );
    }

    public function testThrowsExceptionOnInvalidEquals(): void
    {
        $this->expectException(Exception::class);
        DateFilter::fromUserInput(
            'foobar'
        );
    }

    public function testBasicDateFilter(): void
    {
        $filter = DateFilter::fromUserInput(
            '2020-01-30 12:37:21'
        );
        $this->assertSame(
            '2020-01-30T12:37:21+00:00',
            $filter->equals()->format(DateTime::ATOM)
        );

        $filter = DateFilter::fromUserInput(
            '2020-01-30 12:37:21 CET'
        );
        $this->assertSame(
            '2020-01-30T12:37:21+01:00',
            $filter->equals()->format(DateTime::ATOM)
        );

        $filter = DateFilter::fromUserInput(
            null,
            [
                '2020-01-30 12:37:21',
                '2020-01-30 12:37:22',
            ]
        );
        $this->assertSame(
            [
                '2020-01-30T12:37:21+00:00',
                '2020-01-30T12:37:22+00:00',
            ],
            [
                $filter->between()[0]->format(DateTime::ATOM),
                $filter->between()[1]->format(DateTime::ATOM),
            ]
        );
    }
}

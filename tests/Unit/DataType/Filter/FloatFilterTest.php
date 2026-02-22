<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType\Filter;

use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Exception;
use InvalidArgumentException;
use OxidEsales\GraphQL\Base\DataType\Filter\FloatFilter;
use OxidEsales\GraphQL\Base\Tests\Unit\DataType\DataTypeTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;

#[AllowMockObjectsWithoutExpectations]
class FloatFilterTest extends DataTypeTestCase
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

    public function testGivesLowerThan(): void
    {
        $this->assertSame(
            2.0,
            (FloatFilter::fromUserInput(
                null,
                2.0
            ))->lessThan()
        );
    }

    public function testGivesGreaterThan(): void
    {
        $this->assertSame(
            2.0,
            (FloatFilter::fromUserInput(
                null,
                null,
                2.0
            ))->greaterThan()
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
            $filter->lessThan()
        );
        $this->assertSame(
            1.0,
            $filter->greaterThan()
        );
        $this->assertSame(
            [
                1.0,
                10.0,
            ],
            $filter->between()
        );
    }

    public static function invalidBetweens(): array
    {
        return [
            [
                [],
            ],
            [
                [1.0],
            ],
            [
                [null, 1.0],
            ],
            [
                [1.0, null],
            ],
            [
                [1.0, 2.0, 3.0],
            ],
        ];
    }

    #[DataProvider('invalidBetweens')]
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

    public function testAddQueryPartWithNoFrom(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();
        $filter = FloatFilter::fromUserInput(608.8077);

        $this->expectException(InvalidArgumentException::class);
        $filter->addToQuery($queryBuilder, 'db_field');
    }

    public function testAddQueryPartEquals(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();

        $number = 608.8077;
        $filter = FloatFilter::fromUserInput($number);

        $queryBuilder->select()->from('db_table');
        $filter->addToQuery($queryBuilder, 'db_field');

        /** @var CompositeExpression $where */
        $where = $queryBuilder->getQueryPart('where');

        $this->assertEquals($where::TYPE_AND, $where->getType());
        $this->assertEquals('db_table.DB_FIELD = :db_field_eq', (string)$where);
        $this->assertEquals($number, $queryBuilder->getParameter(':db_field_eq'));
    }

    public function testAddQueryPartLowerThan(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();

        $number = 608.8077;
        $filter = FloatFilter::fromUserInput(null, $number);

        $queryBuilder->select()->from('db_table');
        $filter->addToQuery($queryBuilder, 'db_field');

        /** @var CompositeExpression $where */
        $where = $queryBuilder->getQueryPart('where');

        $this->assertEquals($where::TYPE_AND, $where->getType());
        $this->assertEquals('db_table.DB_FIELD < :db_field_lt', (string)$where);
        $this->assertEquals($number, $queryBuilder->getParameter(':db_field_lt'));
    }

    public function testAddQueryPartGreaterThan(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();

        $number = 608.8077;
        $filter = FloatFilter::fromUserInput(null, null, $number);

        $queryBuilder->select()->from('db_table');
        $filter->addToQuery($queryBuilder, 'db_field');

        /** @var CompositeExpression $where */
        $where = $queryBuilder->getQueryPart('where');

        $this->assertEquals($where::TYPE_AND, $where->getType());
        $this->assertEquals('db_table.DB_FIELD > :db_field_gt', (string)$where);
        $this->assertEquals($number, $queryBuilder->getParameter(':db_field_gt'));
    }

    public function testAddQueryPartBetween(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();

        $numbers = [
            608.8077,
            3469.01,
        ];
        $filter = FloatFilter::fromUserInput(null, null, null, $numbers);

        $queryBuilder->select()->from('db_table');
        $filter->addToQuery($queryBuilder, 'db_field');

        /** @var CompositeExpression $where */
        $where = $queryBuilder->getQueryPart('where');

        $this->assertEquals($where::TYPE_AND, $where->getType());
        $this->assertEquals(
            'db_table.DB_FIELD BETWEEN :db_field_less AND :db_field_upper',
            (string)$where
        );
        $this->assertEquals($numbers[0], $queryBuilder->getParameter(':db_field_less'));
        $this->assertEquals($numbers[1], $queryBuilder->getParameter(':db_field_upper'));
    }

    public function testAddQueryPartWithAlias(): void
    {
        $queryBuilder = $this->createQueryBuilderMock();
        $filter = FloatFilter::fromUserInput(608.8077);

        $queryBuilder->select()->from('db_table', 'db_table_alias');
        $filter->addToQuery($queryBuilder, 'db_field');

        /** @var CompositeExpression $where */
        $where = $queryBuilder->getQueryPart('where');

        $this->assertEquals('db_table_alias.DB_FIELD = :db_field_eq', (string)$where);
    }

    #[DataProvider('matchesDataProvider')]
    public function testMatches(
        float $trueCase,
        mixed $falseCase,
        FloatFilter $filter
    ): void {
        $this->assertTrue($filter->matches($trueCase));
        $this->assertFalse($filter->matches($falseCase));
    }

    public static function matchesDataProvider(): \Generator
    {
        yield "test match equals" => [
            'trueCase' => 1.0,
            'falseCase' => 2.0,
            'filter' => new FloatFilter(equals: 1.0)
        ];

        yield "test match less than" => [
            'trueCase' => 1.3,
            'falseCase' => 5.4,
            'filter' => new FloatFilter(lessThan: 3.0)
        ];

        yield "test match greater than" => [
            'trueCase' => 6.6,
            'falseCase' => 2.3,
            'filter' => new FloatFilter(greaterThan: 4.5)
        ];

        yield "test match between" => [
            'trueCase' => 5.3,
            'falseCase' => 2.7,
            'filter' => new FloatFilter(between: [3.1, 5.3])
        ];

        yield "test match less and greater than" => [
            'trueCase' => 7.8,
            'falseCase' => 3.3,
            'filter' => new FloatFilter(lessThan: 10.5, greaterThan: 4.5)
        ];

        yield "test not matches with float" => [
            'trueCase' => 2.5,
            'falseCase' => 2,
            'filter' => new FloatFilter(equals: 2.5)
        ];

        yield "test not matches with string" => [
            'trueCase' => 3.6,
            'falseCase' => '3,6',
            'filter' => new FloatFilter(equals: 3.6)
        ];
    }
}

<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use OxidEsales\GraphQL\Base\DataType\Filter\BoolFilter;
use OxidEsales\GraphQL\Base\DataType\Filter\DateFilter;
use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use OxidEsales\GraphQL\Base\DataType\TokenFilterList;
use TheCodingMachine\GraphQLite\Types\ID;

class TokenFilterListTest extends DataTypeTestCase
{
    public function testDefaultFactory(): void
    {
        $filterList = new TokenFilterList();

        $expected = [
            'oxuserid' => null,
            'oxshopid' => null,
            'expires_at' => null,
        ];

        $this->assertSame($expected, $filterList->getFilters());
    }

    public function testFactory(): void
    {
        $expected = [
            'oxuserid' => new IDFilter(new ID('_userId')),
            'oxshopid' => new IDFilter(new ID(66)),
            'expires_at' => new DateFilter(null, ['2021-01-12 12:12:12', '2021-12-31 12:12:12']),
        ];

        $filterList = new TokenFilterList(...array_values($expected));

        $this->assertSame($expected, $filterList->getFilters());
    }

    public function testActiveFilter(): void
    {
        $filterList = new TokenFilterList();

        $this->assertNull($filterList->getActive());

        $filterList->withActiveFilter(new BoolFilter());

        $this->assertNull($filterList->getActive());
    }

    public function testWithUserFilter(): void
    {
        $filterList = new TokenFilterList();

        $this->assertNull($filterList->getUserFilter());

        $userFilter = new IDFilter(new ID('_userId'));
        $filterList = $filterList->withUserFilter($userFilter);

        $this->assertEquals($userFilter, $filterList->getUserFilter());
    }
}

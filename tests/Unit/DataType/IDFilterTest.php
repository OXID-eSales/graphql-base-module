<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use OxidEsales\GraphQL\Base\DataType\IDFilter;
use PHPUnit\Framework\TestCase;
use TheCodingMachine\GraphQLite\Types\ID;

class IDFilterTest extends TestCase
{
    public function testBasicIDFilter(): void
    {
        $filter = IDFilter::fromUserInput(
            new ID('test')
        );
        $this->assertSame(
            'test',
            (string) $filter->equals()
        );
    }
}

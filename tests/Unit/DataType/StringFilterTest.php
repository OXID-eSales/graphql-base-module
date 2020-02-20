<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use Exception;
use OxidEsales\GraphQL\Base\DataType\StringFilter;
use PHPUnit\Framework\TestCase;

class StringFilterTest extends TestCase
{
    public function testThrowsExceptionOnNoInput(): void
    {
        $this->expectException(Exception::class);
        StringFilter::fromUserInput();
    }

    public function testBasicStringFilter(): void
    {
        $filter = StringFilter::fromUserInput(
            'equals',
            'contains',
            'beginsWith'
        );
        $this->assertSame(
            'equals',
            $filter->equals()
        );
        $this->assertSame(
            'contains',
            $filter->contains()
        );
        $this->assertSame(
            'beginsWith',
            $filter->beginsWith()
        );
    }
}

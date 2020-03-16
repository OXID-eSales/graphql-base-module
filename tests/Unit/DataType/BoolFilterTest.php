<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\DataType;

use OxidEsales\GraphQL\Base\DataType\BoolFilter;
use PHPUnit\Framework\TestCase;

class BoolFilterTest extends TestCase
{
    public function testReturnsTrueOnEmptyInitialization(): void
    {
        $this->assertTrue((new BoolFilter())->equals());
    }

    /**
     * @dataProvider boolDataProvider
     */
    public function testReturnsGivenEqualValue(bool $userInput, bool $returnValue): void
    {
        $this->assertSame(
            (BoolFilter::fromUserInput($userInput))->equals(),
            $returnValue
        );
    }

    public function boolDataProvider(): array
    {
        return [
            'equals returns false, if false is given' => [false, false],
            'equals returns true, if true is given'   => [true, true],
        ];
    }
}

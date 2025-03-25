<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Framework;

use ArrayIterator;
use Kcs\ClassFinder\Finder\FinderInterface;
use OxidEsales\GraphQL\Base\Framework\AggregatedFinder;
use OxidEsales\GraphQL\Base\Tests\Unit\BaseTestCase;

class AggregatedFinderTest extends BaseTestCase
{
    public function testGetIterator(): void
    {
        $elements = [
            [uniqid() => uniqid(), uniqid() => uniqid()],
            [uniqid() => uniqid(), uniqid() => uniqid()],
        ];

        $finder = new AggregatedFinder();
        $finder->addFinder($this->createConfiguredMock(FinderInterface::class, [
            'getIterator' => new ArrayIterator($elements[0]),
        ]));
        $finder->addFinder($this->createConfiguredMock(FinderInterface::class, [
            'getIterator' => new ArrayIterator($elements[1]),
        ]));

        $array = iterator_to_array($finder->getIterator());
        $this->assertEquals($elements[0] + $elements[1], $array);
    }
}

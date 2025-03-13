<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

use AppendIterator;
use Kcs\ClassFinder\Finder\FinderInterface;
use Kcs\ClassFinder\Finder\Psr4Finder;
use Kcs\ClassFinder\Finder\ReflectionFilterTrait;
use Traversable;

/**
 * @internal This class is not covered by the backward compatibility promise
 */
class Psr4AggregatedFinder implements FinderInterface
{
    use ReflectionFilterTrait;

    private AppendIterator $iterator;

    public function __construct()
    {
        $this->iterator = new AppendIterator();
    }

    public function addFinder(Psr4Finder $finder): void
    {
        $this->iterator->append($finder->getIterator());
    }

    public function getIterator(): Traversable
    {
        return $this->iterator;
    }
}

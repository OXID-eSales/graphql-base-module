<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

use AppendIterator;
use IteratorIterator;
use Kcs\ClassFinder\Finder\FinderInterface;
use Kcs\ClassFinder\Finder\ReflectionFilterTrait;
use Traversable;

class AggregatedFinder implements FinderInterface
{
    use ReflectionFilterTrait;

    /** @var AppendIterator<mixed, mixed, IteratorIterator<mixed, mixed, Traversable<mixed, mixed>>> */
    private AppendIterator $iterator;

    public function __construct()
    {
        $this->iterator = new AppendIterator();
    }

    public function addFinder(FinderInterface $finder): void
    {
        $this->iterator->append(new IteratorIterator($finder->getIterator()));
    }

    public function getIterator(): Traversable
    {
        return $this->iterator;
    }
}

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
use Kcs\ClassFinder\Finder\FinderTrait;
use Traversable;

class AggregatedFinder implements FinderInterface
{
    use FinderTrait;

    /** @var FinderInterface[] */
    private array $finders = [];

    public function addFinder(FinderInterface $finder): void
    {
        $this->finders[] = $finder;
    }

    public function getIterator(): Traversable
    {
        $iterator = new AppendIterator();
        foreach ($this->finders as $finder) {
            $iterator->append(new IteratorIterator($finder->getIterator()));
        }

        return $iterator;
    }
}

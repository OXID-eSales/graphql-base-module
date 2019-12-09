<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Framework\DataObject;

use OxidEsales\GraphQL\Base\DataObject\BoolFilterInput;
use OxidEsales\GraphQL\Base\DataObject\FloatFilterInput;
use OxidEsales\GraphQL\Base\DataObject\IntegerFilterInput;
use OxidEsales\GraphQL\Base\DataObject\StringFilterInput;

class TestFilterInput
{
    private $active;
    private $price;
    private $stock;
    private $title;

    public function __construct(
        ?BoolFilterInput $active = null,
        ?FloatFilterInput $price = null,
        ?IntegerFilterInput $stock = null,
        ?StringFilterInput $title = null
    ) {
        $this->active = $active;
        $this->price = $price;
        $this->stock = $stock;
        $this->title = $title;
    }

    public function __toString(): string
    {
        $s  = 'active: ' . ($this->active->equals() ? 'true' : 'false') . PHP_EOL;
        $s .= 'price-eq: ' . $this->price->equals() . PHP_EOL;
        $s .= 'price-lt: ' . $this->price->lowerThen() . PHP_EOL;
        $s .= 'price-gt: ' . $this->price->greaterThen() . PHP_EOL;
        $s .= 'price-between: ' . print_r($this->price->between(), true) . PHP_EOL;
        $s .= 'stock-eq: ' . $this->stock->equals() . PHP_EOL;
        $s .= 'stock-lt: ' . $this->stock->lowerThen() . PHP_EOL;
        $s .= 'stock-gt: ' . $this->stock->greaterThen() . PHP_EOL;
        $s .= 'stock-between: ' . print_r($this->stock->between(), true) . PHP_EOL;
        $s .= 'title-eq: ' . $this->title->equals() . PHP_EOL;
        $s .= 'title-contains: ' . $this->title->contains() . PHP_EOL;
        $s .= 'title-beginsWith: ' . $this->title->beginsWith() . PHP_EOL;
        return $s;
    }
}

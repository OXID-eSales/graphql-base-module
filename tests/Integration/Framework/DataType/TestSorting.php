<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Framework\DataType;

use OxidEsales\GraphQL\Base\DataType\Sorting;
use TheCodingMachine\GraphQLite\Annotations\Factory;

class TestSorting extends Sorting
{
    /**
     * @Factory()
     */
    public static function fromUserInput(
        ?string $title = Sorting::SORTING_DESC,
        ?string $price = Sorting::SORTING_ASC
    ): self {
        return new self([
            'oxtitle' => $title,
            'oxprice' => $price,
        ]);
    }
}

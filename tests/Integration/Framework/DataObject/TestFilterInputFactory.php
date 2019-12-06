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
use TheCodingMachine\GraphQLite\Annotations\Factory;

class TestFilterInputFactory
{
    /**
     * @Factory()
     */
    public static function createTestFilterInput(
        ?BoolFilterInput $active = null,
        ?FloatFilterInput $price = null,
        ?IntegerFilterInput $stock = null,
        ?StringFilterInput $title = null
    ): TestFilterInput {
        return new TestFilterInput(
            $active,
            $price,
            $stock,
            $title
        );
    }
}

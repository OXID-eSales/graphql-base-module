<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Codeception\Helper;

use OxidEsales\Facts\Facts;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module
{
    public function _beforeSuite($settings = []) {
        exec((new Facts())->getShopRootPath() . '/bin/oe-console oe:module:activate oe/graphql-base');
    }
}

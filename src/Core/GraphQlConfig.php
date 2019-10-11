<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Core;

class GraphQlConfig extends GraphQlConfig_parent
{
    protected function calculateActiveShopId()
    {
        return parent::calculateActiveShopId();
    }
}

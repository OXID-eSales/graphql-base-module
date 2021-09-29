<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Infrastructure;

use OxidEsales\Eshop\Core\Model\BaseModel;

interface ShopModelAwareInterface
{
    public function getEshopModel(): BaseModel;
}

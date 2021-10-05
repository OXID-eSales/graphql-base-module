<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\DataType;

use OxidEsales\Eshop\Core\Model\BaseModel;

interface DataTypeInterface
{
    public function getEshopModel(): BaseModel;

    /**
     * @return class-string
     */
    public static function getModelClass(): string;
}

<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Base\Shared\Infrastructure\Factory\Seo;

use OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer;

interface SeoEncoderManufacturerFactoryInterface
{
    public function create(): SeoEncoderManufacturer;
}

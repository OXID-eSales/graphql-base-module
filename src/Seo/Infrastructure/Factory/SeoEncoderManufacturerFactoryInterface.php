<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory;

use OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer;

interface SeoEncoderManufacturerFactoryInterface
{
    public function create(): SeoEncoderManufacturer;
}

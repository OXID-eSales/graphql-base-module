<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory;

use OxidEsales\Eshop\Core\SeoEncoder;
use OxidEsales\GraphQL\Base\Seo\Enum\SeoType;

interface SeoEncoderFactoryInterface
{
    public function create(?SeoType $seoType = null): SeoEncoder;
}

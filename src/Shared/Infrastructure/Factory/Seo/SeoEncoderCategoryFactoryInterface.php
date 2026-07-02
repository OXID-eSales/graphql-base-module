<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Base\Shared\Infrastructure\Factory\Seo;

use OxidEsales\Eshop\Application\Model\SeoEncoderCategory;

interface SeoEncoderCategoryFactoryInterface
{
    public function create(): SeoEncoderCategory;
}

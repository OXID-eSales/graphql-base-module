<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory;

use OxidEsales\Eshop\Application\Model\SeoEncoderCategory;

class SeoEncoderCategoryFactory implements SeoEncoderCategoryFactoryInterface
{
    public function create(): SeoEncoderCategory
    {
        return oxNew(SeoEncoderCategory::class);
    }
}

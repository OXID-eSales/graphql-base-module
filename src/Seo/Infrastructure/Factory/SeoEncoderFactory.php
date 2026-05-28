<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory;

use OxidEsales\Eshop\Application\Model\SeoEncoderArticle;
use OxidEsales\Eshop\Application\Model\SeoEncoderCategory;
use OxidEsales\Eshop\Application\Model\SeoEncoderContent;
use OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer;
use OxidEsales\Eshop\Application\Model\SeoEncoderVendor;
use OxidEsales\Eshop\Core\SeoEncoder;
use OxidEsales\GraphQL\Base\Seo\Enum\SeoType;

readonly class SeoEncoderFactory implements SeoEncoderFactoryInterface
{
    public function create(SeoType $seoType): SeoEncoder
    {
        return match ($seoType) {
            SeoType::Article => oxNew(SeoEncoderArticle::class),
            SeoType::Category => oxNew(SeoEncoderCategory::class),
            SeoType::Manufacturer => oxNew(SeoEncoderManufacturer::class),
            SeoType::Vendor => oxNew(SeoEncoderVendor::class),
            SeoType::Content => oxNew(SeoEncoderContent::class),
        };
    }
}

<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory;

use OxidEsales\Eshop\Core\SeoEncoder;
use OxidEsales\GraphQL\Base\Seo\Enum\SeoType;

readonly class SeoEncoderFactory implements SeoEncoderFactoryInterface
{
    public function __construct(
        private SeoEncoderArticleFactoryInterface $articleFactory,
        private SeoEncoderCategoryFactoryInterface $categoryFactory,
        private SeoEncoderManufacturerFactoryInterface $manufacturerFactory,
        private SeoEncoderVendorFactoryInterface $vendorFactory,
        private SeoEncoderContentFactoryInterface $contentFactory,
    ) {
    }

    public function create(?SeoType $seoType = null): SeoEncoder
    {
        if ($seoType === null) {
            return oxNew(SeoEncoder::class);
        }

        return match ($seoType) {
            SeoType::Article => $this->articleFactory->create(),
            SeoType::Category => $this->categoryFactory->create(),
            SeoType::Manufacturer => $this->manufacturerFactory->create(),
            SeoType::Vendor => $this->vendorFactory->create(),
            SeoType::Content => $this->contentFactory->create(),
        };
    }
}

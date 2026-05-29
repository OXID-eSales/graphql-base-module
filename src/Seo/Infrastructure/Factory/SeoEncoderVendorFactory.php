<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory;

use OxidEsales\Eshop\Application\Model\SeoEncoderVendor;

class SeoEncoderVendorFactory implements SeoEncoderVendorFactoryInterface
{
    public function create(): SeoEncoderVendor
    {
        return oxNew(SeoEncoderVendor::class);
    }
}

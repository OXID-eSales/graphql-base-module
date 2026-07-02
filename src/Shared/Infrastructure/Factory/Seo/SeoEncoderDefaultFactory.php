<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Shared\Infrastructure\Factory\Seo;

use OxidEsales\Eshop\Core\SeoEncoder;

class SeoEncoderDefaultFactory implements SeoEncoderDefaultFactoryInterface
{
    public function create(): SeoEncoder
    {
        return oxNew(SeoEncoder::class);
    }
}

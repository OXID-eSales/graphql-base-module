<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory;

use OxidEsales\Eshop\Application\Model\SeoEncoderContent;

class SeoEncoderContentFactory implements SeoEncoderContentFactoryInterface
{
    public function create(): SeoEncoderContent
    {
        return oxNew(SeoEncoderContent::class);
    }
}

<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Base\Seo\Infrastructure\Factory;

use OxidEsales\Eshop\Application\Model\SeoEncoderContent;

interface SeoEncoderContentFactoryInterface
{
    public function create(): SeoEncoderContent;
}

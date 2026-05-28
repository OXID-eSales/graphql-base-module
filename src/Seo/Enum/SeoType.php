<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Seo\Enum;

enum SeoType: string
{
    case Article = 'oxarticle';
    case Category = 'oxcategory';
    case Manufacturer = 'oxmanufacturer';
    case Vendor = 'oxvendor';
    case Content = 'oxcontent';
}

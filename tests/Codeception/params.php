<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidEsales\Facts\Facts;

$facts = new Facts();
return [
    'SHOP_URL' => $facts->getShopUrl()
];

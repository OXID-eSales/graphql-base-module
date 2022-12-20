<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

// This is acceptance bootstrap
use Webmozart\PathUtil\Path;

require_once Path::join((new \OxidEsales\Facts\Facts())->getShopRootPath(), 'source', 'bootstrap.php');

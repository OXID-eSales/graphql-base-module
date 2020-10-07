<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

define('AUTOLOAD_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR);
define('VENDOR_OX_BASE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR);
require_once VENDOR_OX_BASE_PATH . DIRECTORY_SEPARATOR . "bootstrap.php";

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\GraphQL\Base\Framework\GraphQLQueryHandler;

ContainerFactory::getInstance()
    ->getContainer()
    ->get(GraphQLQueryHandler::class)
    ->executeGraphQLQuery();

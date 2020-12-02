<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

define('INSTALLATION_ROOT_PATH', __DIR__ . '/vendor/oxid-esales/oxideshop-ce/');
define('VENDOR_PATH', __DIR__ . '/vendor/');

require_once __DIR__ . '/vendor/oxid-esales/oxideshop-ce/source/bootstrap.php';

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\GraphQL\Base\Framework\GraphQLQueryHandler;

ContainerFactory::getInstance()
    ->getContainer()
    ->get(GraphQLQueryHandler::class)
    ->executeGraphQLQuery();

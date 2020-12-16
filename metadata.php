<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = [
    'id'          => 'oe_graphql_base',
    'title'       => [
        'de'      => 'GraphQL Base',
        'en'      => 'GraphQL Base',
    ],
    'description' => [
        'de'      => '<span>OXID GraphQL API Framework</span>',
        'en'      => '<span>OXID GraphQL API Framework</span>',
    ],
    'thumbnail'   => 'out/pictures/logo.png',
    'version'     => '5.1.1',
    'author'      => 'OXID eSales',
    'url'         => 'www.oxid-esales.com',
    'email'       => 'info@oxid-esales.com',
    'extend'      => [
    ],
    'controllers' => [
        // Widget Controller
        'graphql' => OxidEsales\GraphQL\Base\Component\Widget\GraphQL::class,
    ],
    'templates'   => [
    ],
    'blocks'      => [
    ],
    'settings'    => [
        [
            'group' => 'graphql_base',
            'name'  => 'sJsonWebTokenSignature',
            'type'  => 'str',
            'value' => 'CHANGE ME',
        ],
    ],
    'events'      => [
        'onActivate'   => '\OxidEsales\GraphQL\Base\Framework\ModuleSetup::onActivate',
        'onDeactivate' => '\OxidEsales\GraphQL\Base\Framework\ModuleSetup::onDeactivate',
    ],
];

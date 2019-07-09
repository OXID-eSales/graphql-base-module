<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use \OxidEsales\Eshop\Application\Controller\Admin\ModuleConfiguration;
use \OxidEsales\Eshop\Application\Controller\Admin\NavigationTree;


/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = [
    'id'            => 'oe/graphql-base',
    'title'         => [
        'de'        =>  'GraphQL Base',
        'en'        =>  'GraphQL Base',
    ],
    'description'   =>  [
        'de' => '<span>OXID GraphQL API Framework</span>',

        'en' => '<span>OXID GraphQL API Framework</span>',
    ],
    'thumbnail'   => 'out/pictures/logo.png',
    'version'     => '0.0.1',
    'author'      => 'OXID eSales',
    'url'         => 'www.oxid-esales.com',
    'email'       => 'info@oxid-esales.com',
    'extend' => [
        // Sets the shop id from the GraphQlToken if available
        \OxidEsales\Eshop\Core\Config::class => \OxidEsales\GraphQl\Core\GraphQlConfig::class
    ],
    'controllers' => [
        // Widget Controller
        'graphql'       => OxidEsales\GraphQl\Component\Widget\GraphQL::class,
    ],
    'templates'   => [
    ],
    'blocks'      => [
    ],
    'settings'    => [
    ],
    'events'      => [
        'onActivate'   => 'OxidEsales\\GraphQl\\Framework\\ModuleSetup::onActivate',
        'onDeactivate' => 'OxidEsales\\GraphQl\\Framework\\ModuleSetup::onDeactivate'
    ]
];

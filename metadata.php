<?php
/**
 * This Software is the property of OXID eSales and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @category    module
 * @package     GraphQL
 * @link        http://www.oxid-esales.com
 * @copyright   (C) OXID eSales AG 2003-2018
 * @version     OXID eSales GraphQL
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
        'de'        =>  'OXID eSales :: GraphQL Base',
        'en'        =>  'OXID eSales :: GraphQL Base',
    ],
    'description'   =>  [
        'de' => '<span>OXID GraphQL API Framework</span>',

        'en' => '<span>OXID GraphQL API Framework</span>',
    ],
    'thumbnail'   => 'out/pictures/picture.png',
    'version'     => '0.0.1',
    'author'      => 'OXID eSales',
    'url'         => 'www.oxid-esales.com',
    'email'       => 'departmentdevelopment@oxid-esales.com',
    'extend'      => [],
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

<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Type\Provider;


/**
 * Class LoginType
 *
 * @package OxidEsales\GraphQl\Type\Provider
 */
interface QueryProviderInterface
{

    /**
     * @return array
     */
    public function getQueries();

    /**
     * @return array
     */
    public function getQueryResolvers();
}
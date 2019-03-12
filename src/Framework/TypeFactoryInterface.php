<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Framework;

use OxidEsales\GraphQl\Type\BaseType;


/**
 * Class MutationTypeFactory
 *
 * This factory is used to collect all the query and
 * mutation types before building the schema. This allows
 * us to use dependency injection to build the complete
 * schema.
 *
 * @package OxidEsales\GraphQl\Framework
 */
interface TypeFactoryInterface
{
    /**
     * @return mixed
     */
    public function getType();
}
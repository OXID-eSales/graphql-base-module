<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Framework;

use TheCodingMachine\GraphQLite\Schema;

/**
 * Class SchemaFactory
 *
 * @package OxidProfessionalServices\GraphQL\Core\Schema
 */
interface SchemaFactoryInterface
{
    public function getSchema(): Schema;
}

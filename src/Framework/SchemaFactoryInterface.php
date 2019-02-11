<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 29.01.19
 * Time: 10:09
 */

namespace OxidEsales\GraphQl\Framework;

use GraphQL\Type\Schema;


/**
 * Class SchemaFactory
 *
 * @package OxidProfessionalServices\GraphQl\Core\Schema
 */
interface SchemaFactoryInterface
{

    /**
     * @return Schema
     */
    public function getSchema(): Schema;
}

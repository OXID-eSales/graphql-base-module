<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 04.03.19
 * Time: 10:27
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
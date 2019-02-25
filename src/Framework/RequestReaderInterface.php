<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 20.02.19
 * Time: 14:49
 */

namespace OxidEsales\GraphQl\Framework;

interface RequestReaderInterface
{

    /**
     *  Get header Authorization
     *
     * @return $aHeaders array
     */
    public function getAuthorizationHeader();

    /**
     * Get the Request data
     *
     * @return array
     */
    public function getGraphQLRequestData();

}
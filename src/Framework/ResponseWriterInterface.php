<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 20.02.19
 * Time: 14:49
 */

namespace OxidEsales\GraphQl\Framework;

interface ResponseWriterInterface
{
    /**
     * Return a JSON Object with the graphql results
     *
     * @param $aResult
     */
    public function renderJsonResponse($result, $httpStatus);
}
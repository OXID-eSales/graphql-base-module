<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 01.04.19
 * Time: 15:47
 */

namespace OxidEsales\GraphQl\Framework;

interface GenericFieldResolverInterface
{

    /**
     * @param string $fieldname
     * @param object $dataObject
     *
     * @return mixed
     */
    public function getField(string $fieldname, $dataObject);

    public function setField(string $fieldname, $value, $dataObject);
}
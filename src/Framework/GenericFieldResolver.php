<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Framework;

use OxidEsales\GraphQl\Exception\NoSuchGetterException;
use OxidEsales\GraphQl\Exception\NoSuchSetterException;

class GenericFieldResolver implements GenericFieldResolverInterface
{

    /**
     * @param string $fieldname
     * @param object $dataObject
     *
     * @return mixed
     */
    public function getField(string $fieldname, $dataObject) {

        $getterName = $this->createGetterName($fieldname);
        try {
            $reflectionMethod = new \ReflectionMethod(get_class($dataObject), $getterName);
        } catch (\ReflectionException $e) {
            throw new NoSuchGetterException("Can't resolve field with name \"$fieldname\".");
        }
        return $reflectionMethod->invoke($dataObject);

    }

    /**
     * @param string $fieldname
     * @param        $value
     * @param        $dataObject
     *
     * @return mixed
     * @throws NoSuchSetterException
     */
    public function setField(string $fieldname, $value, $dataObject) {

        $setterName = $this->createSetterName($fieldname);
        try {
            $reflectionMethod = new \ReflectionMethod(get_class($dataObject), $setterName);
        } catch (\ReflectionException $e) {
            throw new NoSuchSetterException("Can't set field with name \"$fieldname\".");
        }
        $reflectionMethod->invoke($dataObject, $value);
        return $dataObject;

    }

    /**
     * @param string $fieldname
     *
     * @return string
     */
    private function createGetterName(string $fieldname) {

        return 'get' . ucfirst($fieldname);
    }

    /**
     * @param string $fieldname
     *
     * @return string
     */
    private function createSetterName(string $fieldname) {

        return 'set' . ucfirst($fieldname);
    }
}

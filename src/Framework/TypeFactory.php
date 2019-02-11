<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Framework;

use OxidProfessionalServices\GraphQl\Core\Type\BaseType;

/**
 * @internal
 */
class TypeFactory
{
    private $typeClass;

    private $fields = [];

    private $fieldHandlers = [];

    /**
     * TypeFactory constructor.
     *
     * @param string $typeClass
     */
    public function __construct(string $typeClass)
    {
        $this->typeClass = $typeClass;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return new $this->typeClass($this->fields, $this->fieldHandlers);
    }

    /**
     * @param BaseType $type
     */
    public function addSubType(BaseType $type)
    {
        foreach ($type->getProvidedFields() as $parentFieldName => $parentField) {
            $this->fields[$parentFieldName] = $parentField;
        }
        foreach ($type->getFieldHandlers() as $handlerName => $handler) {
            $this->fieldHandlers[$handlerName] = $handler;
        }
    }
}

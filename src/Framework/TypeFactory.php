<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Framework;

use OxidEsales\GraphQl\Type\BaseType;

/**
 * Class TypeFactory
 *
 * This factory is used to collect all the query and
 * mutation types before building the schema. This allows
 * us to use dependency injection to build the complete
 * schema.
 *
 * @package OxidEsales\GraphQl\Framework
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
        foreach ($type->getQueriesOrMutations() as $parentFieldName => $parentField) {
            $this->fields[$parentFieldName] = $parentField;
        }
        foreach ($type->getQueryOrMutationHandlers() as $handlerName => $handler) {
            $this->fieldHandlers[$handlerName] = $handler;
        }
    }
}

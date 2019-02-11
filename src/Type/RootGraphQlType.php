<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class RootGraphQlType extends ObjectType
{

    private $fieldHandlers = [];

    public $name = 'Set name in subclass';

    public $description = 'Set description in subclass';
    /**
     * OxQLType constructor.
     *
     * @param array $fields
     * @param array $fieldHandlers
     */
    public function __construct(array $fields, array $fieldHandlers)
    {
        $this->fieldHandlers = $fieldHandlers;
        $config = $config = [
            'name' => $this->name,
            'description' => $this->description,
            'fields' => $fields,
            'resolveField' => function ($val, $args, $context, ResolveInfo $info) {
                return $this->fieldHandlers[$info->fieldName]($val, $args, $context, $info);
            },
        ];
        parent::__construct($config);
    }
}

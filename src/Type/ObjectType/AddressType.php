<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Type\ObjectType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use OxidEsales\GraphQl\DataObject\Address;
use OxidEsales\GraphQl\Framework\GenericFieldResolver;

class AddressType extends ObjectType
{

    protected $genericFieldResolver;

    public function __construct(GenericFieldResolver $genericFieldResolver)
    {
        /** @var GenericFieldResolver genericFieldResolver */
        $this->genericFieldResolver = $genericFieldResolver;

        $config = [
            'name'         => 'Address',
            'description'  => 'An type for address data',
            'fields'       => [
                'street' => Type::string(),
                'streetnr' => Type::string(),
                'additionalinfo' => Type::string(),
                'city' => Type::string(),
                'zip' => Type::string(),
                'countryshortcut' => Type::string()
            ],
            'resolveField' => function ($value, $args, $context, ResolveInfo $info) {
                return $this->genericFieldResolver->getField($info->fieldName, $value);
            }
        ];
        parent::__construct($config);
    }
}

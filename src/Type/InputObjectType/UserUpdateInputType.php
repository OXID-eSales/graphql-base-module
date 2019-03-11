<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Type\InputObjectType;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class UserUpdateInputType extends InputObjectType
{

    public function __construct(AddressInputType $addressInputType)
    {
        $config = [
            'name'        => 'Userupdateinput',
            'description' => 'An type for address data',
            /** We explicitely won't allow an update of the shop id
             * or the usergroup. This would create too much confusion. */
            'fields'      => [
                'password'  => Type::string(),
                'firstname' => Type::string(),
                'lastname'  => Type::string(),
                'id'        => Type::nonNull(Type::string()),
                'address'   => $addressInputType
            ]
        ];
        parent::__construct($config);
    }

}

<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Type\InputObjectType;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class UserCreateInputType extends InputObjectType
{

    public function __construct(AddressInputType $addressInputType)
    {
        $config = [
            'name'        => 'Usercreateinput',
            'description' => 'An type for address data',
            'fields'      => [
                'username'  => Type::nonNull(Type::string()),
                'password'  => Type::nonNull(Type::string()),
                'firstname' => Type::string(),
                'lastname'  => Type::string(),
                'address'   => $addressInputType,
                'usergroup' => Type::nonNull(Type::string()),
                'shopid'    => Type::int()
            ]
        ];
        parent::__construct($config);
    }

}

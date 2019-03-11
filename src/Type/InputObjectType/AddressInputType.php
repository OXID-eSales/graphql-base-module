<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Type\InputObjectType;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class AddressInputType extends InputObjectType
{

    public function __construct()
    {
        $config = [
            'name'         => 'Addressinput',
            'description'  => 'An type for address data',
            'fields'       => [
                'street' => Type::string(),
                'streetnr' => Type::string(),
                'additionalinfo' => Type::string(),
                'city' => Type::string(),
                'zip' => Type::string(),
                'countryshortcut' => Type::string()
            ]
        ];
        parent::__construct($config);
    }

}

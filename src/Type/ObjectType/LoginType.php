<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Type\ObjectType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

/**
 * Class LoginType
 *
 * @package OxidEsales\GraphQl\Type\ObjectType
 */
class LoginType extends ObjectType
{

    public function __construct()
    {
        $config = [
            'name' => 'Login',
            'description' => 'Get / update authentification token',
            'fields' => ['token' => Type::string()],
            'resolveField' => function ($value, $args, $context, ResolveInfo $info) {
                return $value;
            }
        ];
        parent::__construct($config);
    }

}

<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Type\ObjectType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use OxidEsales\GraphQl\DataObject\User;
use OxidEsales\GraphQl\Service\UserServiceInterface;

class UserType extends ObjectType
{
    /**
     * UserType constructor.
     *
     * @param UserServiceInterface $userService
     */
    public function __construct()
    {
        $config = [
            'name' => 'User',
            'description' => 'Mutation to create or change user',
            'fields' => [
                'username' => Type::string(),
                'firstname' => Type::string(),
                'lastname' => Type::string(),
                'id' => Type::string(),
                'address' => new AddressType()
            ],
            'resolveField' => function ($value, $args, $context, ResolveInfo $info) {
                /** @var User $value */
                return $value->getField($info->fieldName);
            }
        ];
        parent::__construct($config);
    }

}

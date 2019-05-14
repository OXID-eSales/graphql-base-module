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
use OxidEsales\GraphQl\Framework\GenericFieldResolverInterface;
use OxidEsales\GraphQl\Service\UserServiceInterface;

class UserType extends ObjectType
{
    /** @var GenericFieldResolverInterface $genericFieldResolver */
    protected $genericFieldResolver;
    /**
     * UserType constructor.
     *
     * @param UserServiceInterface $userService
     */
    public function __construct(GenericFieldResolverInterface $genericFieldResolver, AddressType $addressType)
    {
        $this->genericFieldResolver = $genericFieldResolver;

        $config = [
            'name' => 'User',
            'description' => 'Rudimentary user object',
            'fields' => [
                'username' => Type::string(),
                'firstname' => Type::string(),
                'lastname' => Type::string(),
                'id' => Type::string(),
                'address' => $addressType
            ],
            'resolveField' => function ($value, $args, $context, ResolveInfo $info) {
                return $this->genericFieldResolver->getField($info->fieldName, $value);
            }
        ];
        parent::__construct($config);
    }

}

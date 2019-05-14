<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Type\Provider;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use OxidEsales\GraphQl\Dao\UserDao;
use OxidEsales\GraphQl\Framework\AppContext;
use OxidEsales\GraphQl\Service\PermissionsService;
use OxidEsales\GraphQl\Service\PermissionsServiceInterface;
use OxidEsales\GraphQl\Service\UserServiceInterface;
use OxidEsales\GraphQl\Type\InputObjectType\AddressInputType;
use OxidEsales\GraphQl\Type\InputObjectType\UserCreateInputType;
use OxidEsales\GraphQl\Type\InputObjectType\UserUpdateInputType;
use OxidEsales\GraphQl\Type\ObjectType\UserType;
use OxidEsales\GraphQl\Utility\AuthConstants;

class UserMutationProvider implements MutationProviderInterface, QueryProviderInterface
{

    /** @var  UserType */
    protected $userType;

    /** @var  UserServiceInterface */
    protected $userService;

    /** @var  PermissionsServiceInterface */
    protected $permissionService;

    public function __construct(UserServiceInterface $userService,
                                PermissionsServiceInterface $permissionService,
                                UserType $userType)
    {
        $this->userService = $userService;
        $this->permissionService = $permissionService;
        $this->userType = $userType;
    }

    public function getQueries()
    {
        return [
            'user' => [
                'type'        => $this->userType,
                'description' => 'Get a user object. If no parameter is given, get self.',
                'args'        => [
                    'userid' => Type::string()
                ]
            ]
        ];
    }

    public function getQueryResolvers()
    {
        return [
            'user' => function ($value, $args, $context, ResolveInfo $info) {
                /** @var AppContext $context */
                $userid = array_key_exists('userid', $args) ? $args['userid'] : $context->getAuthToken()->getSubject();
                if ($userid == $context->getAuthToken()->getSubject()) {
                    $this->permissionService->checkPermission($context->getAuthToken(), ['maygetself', 'maygetanyuser']);
                }
                else {
                    $this->permissionService->checkPermission($context->getAuthToken(), ['maygetshopuser', 'maygetanyuser']);
                }
                $user = $this->userService->getUser($userid);
                if ($user->getShopid() != $context->getAuthToken()->getShopid())
                {
                    $this->permissionService->checkPermission($context->getAuthToken(), ['maygetshopuser', 'maygetanyuser']);
                }
                return $user;
            }
        ];
    }

    public function getMutations()
    {
        $addressInputType = new AddressInputType();

        return [
            'createUser' => [
                'type'        => $this->userType,
                'description' => 'Creates a new user object in the database.',
                'args'        => [
                    'user' => new UserCreateInputType($addressInputType)
                ]
            ],
            'updateUser' => [
                'type'        => $this->userType,
                'description' => 'Updates an existing user object in the database.',
                'args'        => [
                    'user' => new UserUpdateInputType($addressInputType)
                ]
            ]
        ];
    }

    public function getMutationResolvers()
    {
        return [
            'createUser' => function ($value, $args, $context, ResolveInfo $info) {
                /** @var AppContext $context */
                $userData = $args['user'];
                if ($userData['usergroup'] == AuthConstants::USER_GROUP_CUSTOMER) {
                    $this->permissionService->checkPermission($context->getAuthToken(), ['maycreatecustomer', 'maycreateanyuser']);
                }
                else {
                    $this->permissionService->checkPermission($context->getAuthToken(), 'maycreateanyuser');
                }

                /** Force a shop id. We do not make this mandatory in the type
                 * because CE / PE don't use shop ids */
                if (! array_key_exists('shopid', $userData)) {
                    $userData['shopid'] = $context->getDefaultShopId();
                }
                if ($userData['shopid'] != $context->getAuthToken()->getShopid()) {
                    $this->permissionService->checkPermission($context->getAuthToken(), 'maycreateuserforothershop');
                }


                return $this->userService->saveUser($userData);
            },
            'updateUser' => function ($value, $args, $context, ResolveInfo $info) {
                /** @var AppContext $context */
                if ($context->getAuthToken()->getSubject() == $args['user']['id']) {
                    $this->permissionService->checkPermission($context->getAuthToken(), ['mayupdateself', 'mayupdateanyuser']);
                }
                else {
                    $this->permissionService->checkPermission($context->getAuthToken(), ['mayupdateanyuser']);
                }
                return $this->userService->updateUser($args['user']);
            }
        ];
    }

}

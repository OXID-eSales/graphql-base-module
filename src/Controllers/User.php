<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Controllers;

use OxidEsales\GraphQl\Dao\UserDaoInterface;
use OxidEsales\GraphQl\DataObject\User as UserDataObject;
use OxidEsales\GraphQl\Framework\AppContext;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;

class User
{
    /** @var AppContext */
    protected $context;

    /** @var UserDaoInterface */
    protected $userDao;

    public function __construct(
        AppContext $context,
        UserDaoInterface $userDao
    ) {
        $this->context = $context;
        $this->userDao = $userDao;
    }
 
    /**
     * @Query
     * @Logged
     */
    public function user(string $username = null): UserDataObject
    {
        return $this->userDao->getUserByName(
            $username,
            $this->context->getCurrentShopId()
        );
    }

    /**
     * @Mutation
     */
    public function userRegister(UserDataObject $user): UserDataObject
    {
        $this->userDao->saveUser($user);
        return $user;
    }
}

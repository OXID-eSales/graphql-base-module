<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Controller;

use OxidEsales\GraphQL\Dao\UserDaoInterface;
use OxidEsales\GraphQL\DataObject\User as UserDataObject;
use OxidEsales\GraphQL\Service\EnvironmentServiceInterface;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;

class User
{
    /** @var EnvironmentServiceInterface */
    protected $environment;

    /** @var UserDaoInterface */
    protected $userDao;

    public function __construct(
        EnvironmentServiceInterface $environment,
        UserDaoInterface $userDao
    ) {
        $this->environment = $environment;
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
            $this->environment->getShopId()
        );
    }

    /**
     * @Mutation
     * @Logged
     */
    public function userRegister(UserDataObject $user): UserDataObject
    {
        $this->userDao->saveUser($user);
        return $user;
    }
}

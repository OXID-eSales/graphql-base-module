<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Service;

use OxidEsales\GraphQl\Dao\UserDaoInterface;
use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\DataObject\User;
use OxidEsales\GraphQl\Exception\InsufficientData;
use OxidEsales\GraphQl\Utility\LegacyWrapperInterface;

class UserService implements UserServiceInterface
{

    /** @var  UserDaoInterface */
    private $userDao;

    /** @var  LegacyWrapperInterface */
    private $legacyWrapper;

    public function __construct(UserDaoInterface $userDao, LegacyWrapperInterface $legacyWrapper)
    {
        $this->userDao = $userDao;
        $this->legacyWrapper = $legacyWrapper;
    }

    public function saveUser(array $data): User
    {
        $user = new User($data);
        $this->setPassword($user, $data['password']);


        $this->userDao->saveOrUpdateUser($user);

        $savedUser = $this->userDao->getUserByName($user->getUsername(), $user->getShopid());

        return $savedUser;
    }

    public function updateUser(array $data): User
    {
        $user = $this->userDao->getUserById($data['id']);
        foreach ($data as $key => $value) {
            $user->setField($key, $value);
        }
        if (array_key_exists('password', $data)) {
            $this->setPassword($user, $data['password']);
        }
        $this->userDao->saveOrUpdateUser($user);

        return $user;
    }

    public function getUser(string $userId): User
    {
        return $this->userDao->getUserById($userId);
    }

    private function setPassword(User $user, $password)
    {
        $user->setPasswordsalt($this->legacyWrapper->createSalt());
        $user->setPasswordhash($this->legacyWrapper->encodePassword($password, $user->getPasswordsalt()));
    }

}

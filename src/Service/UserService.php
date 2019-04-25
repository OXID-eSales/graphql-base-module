<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Service;

use OxidEsales\GraphQl\Dao\UserDaoInterface;
use OxidEsales\GraphQl\DataObject\Address;
use OxidEsales\GraphQl\DataObject\User;
use OxidEsales\GraphQl\Framework\GenericFieldResolver;
use OxidEsales\GraphQl\Utility\LegacyWrapperInterface;

class UserService implements UserServiceInterface
{

    /** @var  UserDaoInterface */
    private $userDao;

    /** @var  LegacyWrapperInterface */
    private $legacyWrapper;

    /** @var  GenericFieldResolver */
    private $genericFieldResolver;

    public function __construct(
        UserDaoInterface $userDao,
        LegacyWrapperInterface $legacyWrapper,
        GenericFieldResolver $genericFieldResolver)
    {
        $this->userDao = $userDao;
        $this->legacyWrapper = $legacyWrapper;
        $this->genericFieldResolver = $genericFieldResolver;
    }

    public function saveUser(array $data): User
    {
        $user = new User();
        $user = $this->setUserFromFields($user, $data);
        $this->userDao->saveOrUpdateUser($user);

        $savedUser = $this->userDao->getUserByName($user->getUsername(), $user->getShopid());

        return $savedUser;
    }

    public function updateUser(array $data): User
    {
        $user = $this->userDao->getUserById($data['id']);
        $user = $this->setUserFromFields($user, $data);
        $this->userDao->saveOrUpdateUser($user);

        return $user;
    }

    public function getUser(string $userId): User
    {
        return $this->userDao->getUserById($userId);
    }

    private function setUserFromFields(User $user, array $data) {

        foreach ($data as $key => $value) {
            if ($key === 'password') {
                $this->setPassword($user, $value);
                continue;
            }
            if ($key === 'address') {
                $address = new Address();
                foreach ($value as $addressKey => $addressValue) {
                    $this->genericFieldResolver->setField($addressKey, $addressValue, $address);
                }
                $this->genericFieldResolver->setField($key, $address, $user);
                continue;
            }
            $this->genericFieldResolver->setField($key, $value, $user);
        }

        return $user;
    }

    private function setPassword(User $user, $password)
    {
        $user->setPasswordsalt($this->legacyWrapper->createSalt());
        $user->setPasswordhash($this->legacyWrapper->encodePassword($password, $user->getPasswordsalt()));
    }

}

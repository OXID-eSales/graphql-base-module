<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 27.02.19
 * Time: 15:07
 */

namespace OxidEsales\GraphQl\Service;

use OxidEsales\GraphQl\DataObject\User;

interface UserServiceInterface
{

    /**
     * Saves the user data given in data.
     * The data may not contain a user id.
     *
     * Returns the id of the user
     *
     * @param $data
     *
     * @return User
     */
    public function saveUser(array $data): User;

    /**
     * Updates the user. The data needs to
     * contain a user id.
     *
     * @param $data
     *
     * @return User
     */
    public function updateUser(array $data): User;

    /**
     * @param $userId
     * @param $shopId
     *
     * @return User
     */
    public function getUser(string $userId): User;

}
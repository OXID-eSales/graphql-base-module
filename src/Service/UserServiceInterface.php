<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Dao;

use OxidEsales\GraphQl\DataObject\TokenRequest;
use OxidEsales\GraphQl\DataObject\User;

interface UserDaoInterface
{
    public function getUserById(string $userid): User;

    public function getUserByName(string $username, int $shopid): User;

    public function saveOrUpdateUser(User $user);
}

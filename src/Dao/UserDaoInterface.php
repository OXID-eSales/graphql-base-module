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
    public function addIdAndUserGroupToTokenRequest(TokenRequest $tokenRequest);

    public function getUserById(string $userid);

    public function getUserByName(string $username, int $shopid);

    public function saveOrUpdateUser(User $user);

}
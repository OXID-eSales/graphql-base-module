<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 28.02.19
 * Time: 14:32
 */

namespace OxidEsales\GraphQl\Dao;

use OxidEsales\GraphQl\DataObject\TokenRequest;
use OxidEsales\GraphQl\DataObject\User;

interface UserDaoInterface
{
    public function addIdAndUserGroupToTokenRequest(TokenRequest $tokenRequest): TokenRequest;

    public function getUserById(string $userid): User;

    public function getUserByName(string $username, int $shopid): User;

    public function saveOrUpdateUser(User $user);

}
<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 14.02.19
 * Time: 11:20
 */

namespace OxidEsales\GraphQl\Dao;

use OxidEsales\GraphQl\DataObject\Token;

interface TokenDaoInterface
{

    public function saveOrUpdateToken(Token $token);

    public function loadToken($username, $shopid): Token;
}
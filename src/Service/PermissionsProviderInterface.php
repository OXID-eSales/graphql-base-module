<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 05.03.19
 * Time: 11:32
 */

namespace OxidEsales\GraphQl\Service;

interface PermissionsProviderInterface
{

    public function addPermission($group, $permission);

    public function getPermissions();
}
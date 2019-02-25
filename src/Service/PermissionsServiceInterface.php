<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 25.02.19
 * Time: 09:06
 */

namespace OxidEsales\GraphQl\Service;

use OxidEsales\GraphQl\DataObject\Token;
use OxidEsales\GraphQl\Exception\MissingPermissionException;

interface PermissionsServiceInterface
{

    /**
     * It is possible to either one or several permissions
     * as an array. If there are several permissions, they
     * are or-ed.
     *
     * @throws MissingPermissionException
     *
     * @param Token|null   $token
     * @param string|array $permissions
     */
    public function checkPermission($token, $permissions);
}
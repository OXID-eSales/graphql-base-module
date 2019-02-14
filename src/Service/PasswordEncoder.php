<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Service;

use OxidEsales\EshopCommunity\Application\Model\User;

class PasswordEncoder implements PasswordEncoderInterface
{
    public function encodePassword(string $password, string $salt): string
    {
        $userModel = oxNew(User::class);
        return $userModel->encodePassword($password, $salt);
    }

}

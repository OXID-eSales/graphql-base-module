<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Utility;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\OpenSSLFunctionalityChecker;
use OxidEsales\Eshop\Core\PasswordSaltGenerator;
use OxidEsales\Eshop\Core\Registry;

class LegacyWrapper implements LegacyWrapperInterface
{
    public function createUid()
    {
        return Registry::getUtilsObject()->generateUId();
    }

    public function encodePassword(string $password, string $salt): string
    {
        $userModel = oxNew(User::class);
        return $userModel->encodePassword($password, $salt);
    }

    public function createSalt(): string
    {
        $saltGenerator = new PasswordSaltGenerator(new OpenSSLFunctionalityChecker());
        return $saltGenerator->generate();
    }

}

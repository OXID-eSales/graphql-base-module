<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Dao;

use OxidEsales\GraphQl\Exception\PasswordMismatchException;
use OxidEsales\GraphQl\Exception\UserNotFoundException;

interface UserDaoInterface
{

    /**
     * Verifies that user and password match. If this
     * succeeds, it returns the oxid of the user.
     *
     * @throws UserNotFoundException
     * @throws PasswordMismatchException
     *
     * @param string $username
     * @param string $password
     * @param int $shopId
     *
     * @return string
     */
    public function fetchUserGroup(string $username, string $password, int $shopid): string;
}
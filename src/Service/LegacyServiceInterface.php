<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;

/**
 * @deprecated No need for a special Interface
 */
interface LegacyServiceInterface
{
    /**
     * @deprecated use OxidEsales\GraphQL\Base\Service\Legacy::GROUP_ADMIN
     */
    public const GROUP_ADMIN = 'admin';

    /**
     * @deprecated use OxidEsales\GraphQL\Base\Service\Legacy::GROUP_CUSTOMERS
     */
    public const GROUP_CUSTOMERS = 'customer';

    /**
     * @throws InvalidLogin
     */
    public function checkCredentials(string $username, string $password): void;

    /**
     * @throws InvalidLogin
     */
    public function getUserGroup(string $username): string;

    /**
     * @return mixed
     */
    public function getConfigParam(string $param);

    public function getShopUrl(): string;

    public function getShopId(): int;

    public function getLanguageId(): int;

    public function createUniqueIdentifier(): string;
}

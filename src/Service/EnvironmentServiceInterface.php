<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Service;

interface EnvironmentServiceInterface
{
    public function getShopUrl(): string;
    public function getLanguage(): string;
    public function getShopId(): int;
}

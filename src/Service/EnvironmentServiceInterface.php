<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Service;

interface EnvironmentServiceInterface
{

    public function getShopUrl(): string;

    public function getDefaultLanguage(): string;

    public function getDefaultShopId(): int;
}
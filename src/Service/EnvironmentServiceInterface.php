<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Service;

interface EnvironmentServiceInterface
{

    public function getShopUrl();

    public function getDefaultLanguage();

    public function getDefaultShopId();
}
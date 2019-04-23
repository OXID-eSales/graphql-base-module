<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Utility;

interface LegacyWrapperInterface
{
    public function encodePassword(string $password, string $salt);

    public function createSalt();

    public function createUid();
}
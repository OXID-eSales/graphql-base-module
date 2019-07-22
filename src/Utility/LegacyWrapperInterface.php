<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQl\Utility;

interface LegacyWrapperInterface
{
    public function createUid(): string;

    public function setLanguage(string $languageShortcut): void;

}
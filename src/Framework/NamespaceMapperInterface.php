<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

interface NamespaceMapperInterface
{
    /**
     * @return array<string, string>
     */
    public function getControllerNamespaceMapping(): array;

    /**
     * @return array<string, string>
     */
    public function getTypeNamespaceMapping(): array;
}

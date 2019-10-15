<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Framework;

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

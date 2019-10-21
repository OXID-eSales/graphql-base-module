<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Tests\Integration\Framework\Service;

use \OxidEsales\GraphQL\Framework\NamespaceMapperInterface;

class NamespaceMapper implements NamespaceMapperInterface
{
    public function getControllerNamespaceMapping(): array
    {
        return [
            '\\OxidEsales\\GraphQL\\Tests\\Integration\\Framework\\Controller' => __DIR__.'/../Controller/'
        ];
    }

    public function getTypeNamespaceMapping(): array
    {
        return [];
    }
}

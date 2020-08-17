<?php

declare(strict_types=1);

namespace MyVendor\GraphQL\MyGraph\Shared\Service;

use OxidEsales\GraphQL\Base\Framework\NamespaceMapperInterface;

final class NamespaceMapper implements NamespaceMapperInterface
{
    public function getControllerNamespaceMapping(): array
    {
        return [
            '\\MyVendor\\GraphQL\\MyGraph\\Category\\Controller' => __DIR__ . '/../../Category/Controller/',
            '\\MyVendor\\GraphQL\\MyGraph\\Product\\Controller' => __DIR__ . '/../../Product/Controller/',
        ];
    }

    public function getTypeNamespaceMapping(): array
    {
        return [
            '\\MyVendor\\GraphQL\\MyGraph\\Category\\DataType' => __DIR__ . '/../../Category/DataType/',
            '\\MyVendor\\GraphQL\\MyGraph\\Product\\DataType' => __DIR__ . '/../../Product/DataType/',
            '\\MyVendor\\GraphQL\\MyGraph\\Product\\Service'   => __DIR__ . '/../../Product/Service/',
        ];
    }
}

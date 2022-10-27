<?php

declare(strict_types=1);

namespace Full\Qualified\Namespace\Shared\Service;

use OxidEsales\GraphQL\Base\Framework\NamespaceMapperInterface;

final class NamespaceMapper implements NamespaceMapperInterface
{
    public function getTypeNamespaceMapping(): array
    {
        return [
            '\\Full\\Qualified\\Namespace\\Service' => __DIR__ . '/../../Service'
        ];
    }
}

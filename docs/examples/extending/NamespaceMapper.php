<?php

declare(strict_types=1);

namespace Full\Quallified\Namespace\Shared\Service;

use OxidEsales\GraphQL\Base\Framework\NamespaceMapperInterface;

final class NamespaceMapper implements NamespaceMapperInterface
{
    public function getTypeNamespaceMapping(): array
    {
        return [
            '\\Full\\Quallified\\Namespace\\Service' => __DIR__ . '/../../Service';
        ];
    }
}

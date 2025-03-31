<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Framework;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Filesystem\Path;

readonly class FilesystemAdapterFactory
{
    public function __construct(private ContextInterface $context)
    {
    }

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    public function create(): FilesystemAdapter
    {
        $path = Path::join(
            $this->context->getCacheDirectory(),
            'oe_graphql_base-schema',
            (string)$this->context->getCurrentShopId()
        );
        return new FilesystemAdapter(directory: $path);
    }
}

<?php

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

use function sprintf;

final class ProductNotFound extends NotFound
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Product was not found by id: %s', $id));
    }
}

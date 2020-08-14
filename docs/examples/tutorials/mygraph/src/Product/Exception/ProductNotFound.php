<?php

declare(strict_types=1);

namespace MyVendor\GraphQL\MyGraph\Product\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

use function sprintf;

final class ProductNotFound extends NotFound
{
    public static function byId(string $id): self
    {
        return new self(sprintf('Product was not found by id: %s', $id));
    }
}

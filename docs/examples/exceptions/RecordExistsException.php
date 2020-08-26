<?php

declare(strict_types=1);

namespace MyVendor\MyModule\Record\Exception;

use Exception;
use GraphQL\Error\ClientAware;
use OxidEsales\GraphQL\Base\Exception\ErrorCategories;
use OxidEsales\GraphQL\Base\Exception\HttpErrorInterface;

final class RecordExists extends Exception implements ClientAware, HttpErrorInterface
{
    public function getHttpStatus(): int
    {
        return 400;
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return ErrorCategories::REQUESTERROR;
    }

    public static function byUniqueField(string $field): self
    {
        return new self(sprintf("Record with field '%s' already exists!", $field));
    }
}

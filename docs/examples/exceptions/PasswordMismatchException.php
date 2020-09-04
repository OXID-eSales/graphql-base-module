<?php

declare(strict_types=1);

namespace MyVendor\MyModule\Record\Exception;

use Exception;
use GraphQL\Error\ClientAware;
use OxidEsales\GraphQL\Base\Exception\ErrorCategories;
use OxidEsales\GraphQL\Base\Exception\HttpErrorInterface;

final class PasswordMismatchException extends Exception implements ClientAware, HttpErrorInterface
{
    public function getHttpStatus(): int
    {
        return 403;
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return ErrorCategories::REQUESTERROR;
    }

    public static function byOldPassword(): self
    {
        return new self('Old password does not match our records');
    }

    public static function byLength(): self
    {
        return new self('Password does not match length requirements');
    }
}

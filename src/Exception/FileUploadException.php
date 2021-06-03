<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Exception;

final class FileUploadException extends Error
{
    public function getCategory(): string
    {
        return ErrorCategories::REQUESTERROR;
    }

    public static function maxFileSizeExceed(): self
    {
        return new self('Max upload file size was exceeded');
    }
}

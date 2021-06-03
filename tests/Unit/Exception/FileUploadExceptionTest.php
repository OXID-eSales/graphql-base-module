<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Exception;

use OxidEsales\GraphQL\Base\Exception\ErrorCategories;
use OxidEsales\GraphQL\Base\Exception\FileUploadException;
use PHPUnit\Framework\TestCase;

final class FileUploadExceptionTest extends TestCase
{
    public function testMaxFileSizeExceed(): void
    {
        $fileUploadException = FileUploadException::maxFileSizeExceed();

        $this->assertSame('Max upload file size was exceeded', $fileUploadException->getMessage());
    }

    public function testExceptionCategory(): void
    {
        $fileUploadException = FileUploadException::maxFileSizeExceed();

        $this->assertSame(ErrorCategories::REQUESTERROR, $fileUploadException->getCategory());
    }

    public function testIsClientSafe(): void
    {
        $fileUploadException = FileUploadException::maxFileSizeExceed();

        $this->assertTrue($fileUploadException->isClientSafe());
    }
}

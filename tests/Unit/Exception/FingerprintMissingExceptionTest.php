<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Exception;

use OxidEsales\GraphQL\Base\Exception\Error;
use OxidEsales\GraphQL\Base\Exception\ErrorCategories;
use OxidEsales\GraphQL\Base\Exception\FingerprintMissingException;
use PHPUnit\Framework\TestCase;

class FingerprintMissingExceptionTest extends TestCase
{
    public function testExceptionCategory(): void
    {
        $fingerprintMissingException = new FingerprintMissingException(uniqid());

        $this->assertSame(ErrorCategories::REQUESTERROR, $fingerprintMissingException->getCategory());
    }

    public function testExceptionType(): void
    {
        $fingerprintMissingException = new FingerprintMissingException(uniqid());

        $this->assertInstanceOf(Error::class, $fingerprintMissingException);
    }
}

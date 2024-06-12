<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Exception;

use OxidEsales\GraphQL\Base\Exception\ErrorCategories;
use OxidEsales\GraphQL\Base\Exception\UnknownToken;
use PHPUnit\Framework\TestCase;

final class UnknownTokenTest extends TestCase
{
    public function testExceptionCategory(): void
    {
        $invalidTokenException = new UnknownToken();

        $this->assertSame(ErrorCategories::PERMISSIONERRORS, $invalidTokenException->getCategory());
    }

    public function testIsClientSafe(): void
    {
        $invalidTokenException = new UnknownToken();

        $this->assertTrue($invalidTokenException->isClientSafe());
    }

    public function testUnknownToken(): void
    {
        $exception = new UnknownToken();

        $this->assertSame('The token is not registered', $exception->getMessage());
    }
}

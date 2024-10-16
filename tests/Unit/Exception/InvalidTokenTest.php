<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Exception;

use OxidEsales\GraphQL\Base\Exception\ErrorCategories;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use PHPUnit\Framework\TestCase;

final class InvalidTokenTest extends TestCase
{
    public function testExceptionCategory(): void
    {
        $invalidTokenException = new InvalidToken();

        $this->assertSame(ErrorCategories::PERMISSIONERRORS, $invalidTokenException->getCategory());
    }

    public function testIsClientSafe(): void
    {
        $invalidTokenException = new InvalidToken();

        $this->assertTrue($invalidTokenException->isClientSafe());
    }

    public function testInvalidToken(): void
    {
        $invalidTokenException = new InvalidToken();

        $this->assertSame('The access token is invalid', $invalidTokenException->getMessage());
    }
}

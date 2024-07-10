<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Unit\Exception;

use OxidEsales\GraphQL\Base\Exception\ErrorCategories;
use OxidEsales\GraphQL\Base\Exception\InvalidRefreshToken;
use PHPUnit\Framework\TestCase;

final class InvalidRefreshTokenTest extends TestCase
{
    public function testExceptionCategory(): void
    {
        $invalidTokenException = new InvalidRefreshToken();

        $this->assertSame(ErrorCategories::PERMISSIONERRORS, $invalidTokenException->getCategory());
    }

    public function testIsClientSafe(): void
    {
        $invalidTokenException = new InvalidRefreshToken();

        $this->assertTrue($invalidTokenException->isClientSafe());
    }

    public function testInvalidToken(): void
    {
        $invalidTokenException = new InvalidRefreshToken();

        $this->assertSame('The refresh token is invalid', $invalidTokenException->getMessage());
    }
}
